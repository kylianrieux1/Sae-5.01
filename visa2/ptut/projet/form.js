function verifFormetu () {
    var retour = false;
    var nom = document.getElementById("id_nom").value;
    var grp = document.getElementById("id_groupe").value;
    if(nom!="" && grp!=""){
        retour = true;
    }
    else{
        alert("Il faut compléter les champs.");
    }
    return retour;
}