from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import List
import mysql.connector

app = FastAPI()

# Modèle de données pour les requêtes POST et PUT
class Etudiant(BaseModel):
    nom: str
    no_groupe: int

# Connexion globale (Pensez à utiliser la boucle Try/Except si Docker est lent)
mydb = mysql.connector.connect(
    user='api_user',
    password='api_password',
    host='db',
    port=3306,
    database='ma_bdd',
    autocommit=True
)

# --- LES ROUTES SONT MAINTENANT LIBRES D'ACCÈS ---

@app.get("/etudiants")
def get_etudiants():
    cursor = mydb.cursor(dictionary=True, buffered=True)
    try:
        cursor.execute("SELECT * FROM Etudiants")
        return cursor.fetchall()
    finally:
        cursor.close()

@app.get("/groupes")
def get_groupes():
    cursor = mydb.cursor(dictionary=True, buffered=True)
    try:
        cursor.execute("SELECT NoGroupe, NomGroupe FROM Groupes")
        return cursor.fetchall()
    finally:
        cursor.close()

@app.post("/etudiants")
async def create_etudiant(etudiant: Etudiant):
    cursor = mydb.cursor(buffered=True)
    try:
        sql = "INSERT INTO Etudiants (Nom, NoGroupe) VALUES (%s, %s)"
        cursor.execute(sql, (etudiant.nom, etudiant.no_groupe))
        return {"message": "Étudiant ajouté"}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
    finally:
        cursor.close()

@app.put("/etudiants/{nom_etu}")
async def update_etudiant(nom_etu: str, etudiant: Etudiant):
    cursor = mydb.cursor(buffered=True)
    try:
        sql = "UPDATE Etudiants SET Nom = %s, NoGroupe = %s WHERE Nom = %s"
        cursor.execute(sql, (etudiant.nom, etudiant.no_groupe, nom_etu))
        return {"message": "Étudiant mis à jour"}
    finally:
        cursor.close()

@app.delete("/etudiants/{nom_etu}")
async def delete_etudiant(nom_etu: str):
    cursor = mydb.cursor(buffered=True)
    try:
        sql = "DELETE FROM Etudiants WHERE Nom = %s"
        cursor.execute(sql, (nom_etu,))
        return {"message": "Étudiant supprimé"}
    finally:
        cursor.close()

@app.get("/etudiants/groupe/{grp}")
def get_etu_by_grp(grp: int):
    cursor = mydb.cursor(dictionary=True, buffered=True)
    try:
        cursor.execute("SELECT * FROM Etudiants WHERE NoGroupe = %s", (grp,))
        return cursor.fetchall()
    finally:
        cursor.close()
        
@app.post("/auth")
async def login_simple(form_data: dict):
    cursor = mydb.cursor(dictionary=True, buffered=True)
    try:
        # On cherche l'utilisateur par son email
        sql = "SELECT EMAIL, PASS, STATUT FROM utilisateurs WHERE EMAIL = %s"
        cursor.execute(sql, (form_data['mail'],))
        user = cursor.fetchone()
        
        # Vérification du mot de passe
        if user and user["PASS"] == form_data['pass']:
            return {"success": True, "login": user["EMAIL"], "statut": user["STATUT"]}
        else:
            raise HTTPException(status_code=401, detail="Identifiants incorrects")
    finally:
        cursor.close()
        
@app.get("/etudiants/{nom_etu}")
def get_one_etudiant(nom_etu: str):
    cursor = mydb.cursor(dictionary=True, buffered=True)
    try:
        sql = "SELECT * FROM Etudiants WHERE Nom = %s"
        cursor.execute(sql, (nom_etu,))
        res = cursor.fetchone()
        if not res:
            raise HTTPException(status_code=404, detail="Étudiant non trouvé")
        return res
    finally:
        cursor.close()

