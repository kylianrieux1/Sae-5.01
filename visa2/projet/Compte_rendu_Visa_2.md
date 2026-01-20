## SAE501 - Visa 2 - Compte Rendu : PHP & MariaDB avec Docker

- Rieux Kylian

---

## [cite_start]Visa 2 : PHP & MariaDB [cite: 70]

[cite_start]L'objectif de cette version (Version 2) était de modifier l'application afin de remplacer les bases de données SQLite par **MariaDB** pour la BDD de données et la BDD d'authentification. [cite_start]Le déploiement a été adapté pour utiliser deux conteneurs Docker (un pour PHP et un pour MariaDB).

### 1. Base de données : Migration vers MariaDB

[cite_start]La connectivité de l'application a été adaptée pour utiliser **MariaDB** en remplacement des bases SQLite.

#### 1.1 Structure des Bases de Données (MariaDB)

Les deux bases de données distinctes ont été recréées et configurées sous MariaDB, nécessitant l'adaptation des schémas SQL :

**Base `sae501_auth` (Authentification)**

```sql
-- Table des matières
CREATE TABLE Matieres(
    NoMatiere INT PRIMARY KEY AUTO_INCREMENT, 
    Matiere VARCHAR(20) NOT NULL DEFAULT 'Informatique'
);

-- Table des étudiants
CREATE TABLE Etudiants (
    NoEtu INT PRIMARY KEY AUTO_INCREMENT, 
    Nom VARCHAR(40) NOT NULL, 
    Prenom VARCHAR(40) NOT NULL 
);

-- Table des absences
CREATE TABLE Absences(
    NoAbs INT PRIMARY KEY AUTO_INCREMENT,
    Date_abs DATE NOT NULL DEFAULT '2020-01-01',
    Creneau VARCHAR(7) NOT NULL DEFAULT '08H-10H',
    NoMatiere INT NOT NULL,
    NoEtu INT NOT NULL,
    Email VARCHAR(50) NOT NULL,
    CONSTRAINT fk_NoEtu FOREIGN KEY (NoEtu) REFERENCES Etudiants (NoEtu),
    CONSTRAINT fk_NoMatiere FOREIGN KEY (NoMatiere) REFERENCES Matieres (NoMatiere)
);
```
### 2. Corrections et Adaptations
### 2.1 Adaptation des Fonctions de Connexion PHP
Le code PHP utilisant PDO a été entièrement revu pour utiliser le pilote pdo_mysql et se connecter au service MariaDB via le réseau Docker :

Connexion : Les DSN (Data Source Name) ont été modifiés dans fonctions.php et autres fichiers CRUD.

Host : Le nom du service MariaDB (sae501-mariadb) est utilisé comme hôte pour la connexion.

### 3. Containerisation Docker (Multi-conteneurs)
Le déploiement respecte l'exigence d'utiliser un Dockerfile pour PHP et un autre pour MariaDB.

### 3.1 Dockerfile_php (Application)
Ce Dockerfile est basé sur l'image PHP/Apache et installe l'extension MariaDB.

```Dockerfile
                               
FROM php:8.2-apache

# Installer les dépendances pour pdo_mysql
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copier le code
COPY ./projet /var/www/html

# Droits d’accès
RUN chown -R www-data:www-data /var/www/html \

```
### 3.2 Dockerfile_bdd (MariaDB)
Ce Dockerfile permet de déployer le serveur MariaDB.

```Dockerfile
# Image officielle MariaDB
FROM mariadb:11

# Variables d’environnement (pas de secrets sensibles ici)
ENV MARIADB_DATABASE=projet
ENV MARIADB_USER=user
ENV MARIADB_PASSWORD=userpass
ENV MARIADB_ROOT_PASSWORD=root

# Copie uniquement les fichiers SQL
# Le chemin est relatif à l'endroit d'où tu lances le build
COPY projet/bdd/*.sql /docker-entrypoint-initdb.d/

EXPOSE 3306
```
### 4. Fonctionnalités Validées
Les fonctions de l'application ont été testées et validées sur l'architecture multi-conteneur MariaDB/PHP.

Fonctionnalité,Statut

Connexion/Déconnexion,✅ Validé sur MariaDB

Ajout/Modification/Suppression,✅ Validé sur MariaDB

Relations entre tables (FK),✅ Intégrité des données vérifiée

### Notice d'Installation
Prérequis
Docker Engine 20.10+

Git (pour récupération du code)

Installation et Déploiement
Le déploiement est réalisé en utilisant les deux Dockerfiles et en liant les conteneurs sur un réseau Docker.

```bash
# 1. Créer le réseau Docker
docker network create sae501-net

# 2. Construire l'image MariaDB et lancer le conteneur
docker build -t sae501-bdd -f Dockerfile_bdd .
docker run -d --network sae501-net -p 3306:3306 --name sae501-mariadb sae501-bdd

# 3. Construire l'image PHP et lancer le conteneur
docker build -t sae501-php -f Dockerfile_php .
# Lier le conteneur PHP au conteneur BDD
docker run -d --network sae501-net -p 8080:80 --name sae501-app sae501-php

# 4. Accéder à l'application
# http://localhost:8080
```

## Conclusion
### Objectifs du VISA 2 Atteints
✅ Base de Données : Migration de SQLite vers MariaDB réalisée.

✅ Code PHP : Adaptation des fonctions PDO pour la nouvelle base de données.

✅ Déploiement Docker : Mise en place d'une architecture multi-conteneur avec deux Dockerfiles distincts.

✅ GitLab : Utilisation pour piloter le dépôt des codes sources.

### Préparation VISA 3
Le travail suivant (Version 3) consistera à créer une API Python avec FastAPi pour réaliser les opérations CRUD et une API pour gérer l'authentification simple (sans jeton). L'application PHP devra être modifiée pour utiliser ces API via cURL. Le déploiement se fera avec Docker Compose