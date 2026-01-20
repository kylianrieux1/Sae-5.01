from fastapi import FastAPI, HTTPException, Depends
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from pydantic import BaseModel
from typing import List, Optional
from datetime import datetime, timedelta
from jose import JWTError, jwt
import mysql.connector
import time
import os


SECRET_KEY = "api_pass"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 1

app = FastAPI()
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="token")

class Etudiant(BaseModel):
    nom: str
    no_groupe: int

class Token(BaseModel):
    access_token: str
    token_type: str
    statut: str

def get_db_connection():
    """ Tente de se connecter à la base avec plusieurs essais """
    attempts = 0
    while attempts < 10:
        try:
            connection = mysql.connector.connect(
    		user=os.getenv('DB_USER', 'api_user'),
    		password=os.getenv('DB_PASSWORD', 'api_password'),
 		host=os.getenv('DB_HOST', 'db'),
  		port=3306,
  		database=os.getenv('DB_NAME', 'ma_bdd'),
   		autocommit=True
)
            print("✅ Connecté à MariaDB !")
            return connection
        except mysql.connector.Error as err:
            attempts += 1
            print(f"❌ Tentative {attempts}/10 : Base non prête. Nouvel essai dans 3s...")
            time.sleep(3)
    
    # Si on arrive ici, l'API s'arrête proprement avec un message clair
    raise Exception("Impossible de se connecter à MariaDB après 10 tentatives.")

# Initialisation de la connexion
mydb = get_db_connection()

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        # Utilise la constante définie au début du fichier (30 min)
        expire = datetime.utcnow() + timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    
    to_encode.update({"exp": expire})
    return jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)

@app.post("/token", response_model=Token)
async def login(form_data: OAuth2PasswordRequestForm = Depends()):
    cursor = mydb.cursor(dictionary=True, buffered=True)
    try:
        # On récupère l'utilisateur
        sql = "SELECT EMAIL, PASS, STATUT FROM utilisateurs WHERE EMAIL = %s"
        cursor.execute(sql, (form_data.username,))
        user = cursor.fetchone()
        
        # Validation stricte (Point 1)
        if not user or user["PASS"] != form_data.password:
            raise HTTPException(
                status_code=401, # 401 est plus approprié que 400 pour une erreur d'auth
                detail="Identifiant ou mot de passe incorrect",
                headers={"WWW-Authenticate": "Bearer"},
            )

        # Génération du jeton avec EMAIL et STATUT (Point 1)
        access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
        access_token = create_access_token(
            data={"sub": user["EMAIL"], "statut": user["STATUT"]},
            expires_delta=access_token_expires
        )

        return {
            "access_token": access_token, 
            "token_type": "bearer", 
            "statut": str(user["STATUT"]) # Conversion en string pour éviter les surprises
        }
    finally:
        cursor.close()

async def get_current_user(token: str = Depends(oauth2_scheme)):
    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        username: str = payload.get("sub")
        if username is None:
            raise HTTPException(status_code=401, detail="Jeton invalide")
        return username
    except JWTError:
        raise HTTPException(status_code=401, detail="Jeton expiré ou invalide")


@app.get("/etudiants")
def get_etudiant(current_user: str = Depends(get_current_user)):
    cursor = mydb.cursor(dictionary=True, buffered=True)
    try:
        cursor.execute("SELECT * FROM Etudiants")
        res = cursor.fetchall()
        return res # FastAPI convertit automatiquement la liste de dict en JSON
    finally:
        cursor.close()

@app.get("/groupes")
def get_groupes(current_user: str = Depends(get_current_user)):
    cursor = mydb.cursor(dictionary=True, buffered=True)
    try:
        cursor.execute("SELECT NoGroupe, NomGroupe FROM Groupes")
        return cursor.fetchall()
    finally:
        cursor.close()

@app.post("/etudiants")
async def create_etudiant(etudiant: Etudiant, current_user: str = Depends(get_current_user)):
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
async def update_etudiant(nom_etu: str, etudiant: Etudiant, current_user: str = Depends(get_current_user)):
    cursor = mydb.cursor(buffered=True)
    try:
        sql = "UPDATE Etudiants SET Nom = %s, NoGroupe = %s WHERE Nom = %s"
        cursor.execute(sql, (etudiant.nom, etudiant.no_groupe, nom_etu))
        return {"message": "Étudiant mis à jour"}
    finally:
        cursor.close()

@app.delete("/etudiants/{nom_etu}")
async def delete_etudiant(nom_etu: str, current_user: str = Depends(get_current_user)):
    cursor = mydb.cursor(buffered=True)
    try:
        sql = "DELETE FROM Etudiants WHERE Nom = %s"
        cursor.execute(sql, (nom_etu,))
        return {"message": "Étudiant supprimé"}
    finally:
        cursor.close()
        
@app.get("/etudiants/groupe/{grp}")
def get_etu_by_grp(grp: int, current_user: str = Depends(get_current_user)):
    cursor = mydb.cursor(dictionary=True, buffered=True)
    try:
        cursor.execute("SELECT * FROM Etudiants WHERE NoGroupe = %s", (grp,))
        return cursor.fetchall()
    finally:
        cursor.close()
        
@app.get("/etudiants/{nom_etu}")
def get_one_etudiant(nom_etu: str, current_user: str = Depends(get_current_user)):
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
