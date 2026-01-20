function EnvoiRequete(event, form) {
    event.preventDefault();
    var data = new FormData(form);
    var value = data.get("grp");
    var req_AJAX = null;
    if (window.XMLHttpRequest) {
        req_AJAX = new XMLHttpRequest();
    } else {
        if (typeof ActiveXObject != "undefined") {
            req_AJAX = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }

    if (req_AJAX) {
        req_AJAX.onreadystatechange = function () {
            TraiteReponse(req_AJAX);
        }
        req_AJAX.open("POST", "page_serveur.php", true); 
        req_AJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        req_AJAX.send("grp="+value);     
    } else {
        alert("EnvoiRequete: pas de XMLHTTP !");
    }
}

function TraiteReponse(requete) {
    var etat = requete.readyState;
    if (etat == 4) {
        var taba = document.getElementById("taba");
        var status = requete.status; 
        if (status == 200) {
            var data = "";
            data = requete.responseText;
            taba.innerHTML = data;
        } else {
            taba.innerHTML = "erreur serveur, code " + status;
        }
    }
}