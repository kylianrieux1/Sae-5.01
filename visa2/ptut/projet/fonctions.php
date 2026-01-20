<?php	
	//****************Fonctions utilisées*****************************************************************
	$pdo = new PDO('mysql:host=mdb;port=3306;dbname=projet','user','userpass');
	
	function authentification($mail,$pass){
		$retour = false ;
		global $pdo;
		$madb = $pdo;
		$mail= $madb->quote($mail);
		$pass = $madb->quote($pass);
		$requete = "SELECT EMAIL,PASS FROM utilisateurs WHERE EMAIL = $mail AND PASS = $pass;" ;
		//var_dump($requete);echo "<br/>";  	
		$resultat = $madb->query($requete);
		$tableau_assoc = $resultat->fetchAll(PDO::FETCH_ASSOC);
		if (sizeof($tableau_assoc)!=0) $retour = true;	
		return $retour;
	}
	
	//***************************************************************************************************
	
	function isAdmin($mail){
		$retour = false ;
		// connexion BDD
		global $pdo;
		$madb = $pdo; 
		$mail= $madb->quote($mail);
		// requete SQL
		$sql="SELECT STATUT FROM utilisateurs WHERE EMAIL=$mail;";
		//var_dump($sql);echo '<br>';
		// Execution requête
		$res = $madb->query($sql);
		$statut = $res->fetch(PDO::FETCH_ASSOC);
		//var_dump($statut);echo '<br>';		
		if ( $statut["STATUT"] == 'admin') $retour=true;
		
		return $retour;			
	}
	//******************************************************************************************
	function listerEtudiants()	{
		$retour = false ;	
		// connexion BDD Etudiants!!!!
		global $pdo;
		$madb = $pdo;		
		// requete SQL
		$sql="SELECT Nom, NoEtu, NoGroupe FROM Etudiants ;";
		// Execution requête
		$res = $madb->query($sql);
		// On récupére le tableau des étudiants!!!
		$retour = $res->fetchAll(PDO::FETCH_ASSOC);		
		return $retour;
	}		
	
	function listerGroupes()	{
		$retour = false ;	
		// connexion BDD Etudiants!!!!
		global $pdo;
		$db = $pdo;
		// requete SQL
		$sql="SELECT NoGroupe, NomGroupe, TailleGroupe FROM Groupes ;";
		// Execution requête
		$res = $db->query($sql);
		// On récupére le tableau des étudiants!!!
		$retour = $res->fetchAll(PDO::FETCH_ASSOC);		
		return $retour;

	}
	
	function listerEtudiantsParGroupe($grp){
		$retour = false ;	
		// connexion BDD Etudiants!!!!
		$db = new PDO('sqlite:bdd/etudiants_grp.db');		
		// requete SQL
		$sql="SELECT Nom, NoEtu, NoGroupe FROM etudiants WHERE NoGroupe = $grp;";
		// Execution requête
		$res = $db->query($sql);
		// On récupére le tableau des étudiants!!!
		$retour = $res->fetchAll(PDO::FETCH_ASSOC);		
		return $retour;

	}
	//*****************************************************************************************************
	function ajouterEtudiant($nom,$grp){
		$retour=0;
		try {
			$madb = new PDO('sqlite:bdd/etudiants_grp.db');
			$madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$requete = "INSERT INTO etudiants(Nom,NoGroupe) VALUES('$nom','$grp');";
			$retour = $madb->exec($requete);
		}
		catch(PDOException $erreur) {
			echo '<p>'.$erreur->getMessage().'</p>';
		}
		return $retour;
	}

	function ajoutergroupe($nog,$nomg,$tg){
		/* on récupère directement le code de la ville qui a été transmis dans l'attribut value de la balise <option> du formulaire
		Il n'est donc pas nécessaire de rechercher le code INSEE de la ville*/
		$ret=0;
		try {
			$db = new PDO('sqlite:bdd/etudiants_grp.db');
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$requete = "INSERT INTO Groupes (NoGroupe,NomGroupe,TailleGroupe) VALUES('$nog','$nomg','$tg');";
			$ret = $db->exec($requete);
		}
		catch(PDOException $erreur) {
			echo '<p>'.$erreur->getMessage().'</p>';
		}
		return $ret;
	}
	//*****************************************************************************************************
	function supprimerEtudiant($nom){
		$ret=0;
		try {
			$db = new PDO('sqlite:bdd/etudiants_grp.db');
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$requete = "DELETE FROM Etudiants WHERE Nom='$nom';";
			$ret = $db->exec($requete);
		}
		catch(PDOException $erreur) {
			echo '<p>'.$erreur->getMessage().'</p>';
		}
		return $ret;
	}
	//******************************************************************************************************
	function modifierEtudiant($noETU, $nom, $NoGroupe, $etu) {
		$retour = 0;
		try {
			$madb = new PDO('sqlite:bdd/etudiants_grp.db');
			$madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$requete = "UPDATE Etudiants SET NoEtu = :noETU, Nom = :nom, NoGroupe = :NoGroupe WHERE Nom = :etu";
			$stmt = $madb->prepare($requete);
			$stmt->bindParam(':noETU', $noETU);
			$stmt->bindParam(':nom', $nom);
			$stmt->bindParam(':NoGroupe', $NoGroupe);
			$stmt->bindParam(':etu', $etu);
			$retour = $stmt->execute();
		} catch (PDOException $erreur) {
			echo '<p>' . $erreur->getMessage() . '</p>';
		}
		return $retour;
	}
	//*********************************************************************************************************
	//Nom : redirect()
	//Role : Permet une redirection en javascript
	//Parametre : URL de redirection et Délais avant la redirection
	//Retour : Aucun
	//*******************
	function redirect($url,$tps)
	{
		$temps = $tps * 1000;
		
		echo "<script type=\"text/javascript\">\n"
		. "<!--\n"
		. "\n"
		. "function redirect() {\n"
		. "window.location='" . $url . "'\n"
		. "}\n"
		. "setTimeout('redirect()','" . $temps ."');\n"
		. "\n"
		. "// -->\n"
		. "</script>\n";
		
	}
	//********************************************************************************************************
	function afficheTableau($tab){
		echo '<table>';	
		echo '<tr class="border">';// les entetes des colonnes qu'on lit dans le premier tableau par exemple
		foreach($tab[0] as $colonne=>$valeur){		echo "<th class='m-5 border'>$colonne</th>";		}
		echo "</tr>\n";
		// le corps de la table
		foreach($tab as $ligne){
			echo '<tr class="border">';
			foreach($ligne as $cellule)		{		echo "<td class='m-5 border'>$cellule</td>";		}
			echo "</tr>\n";
		}
		echo '</table>';
	}
?>
