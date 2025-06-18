document.getElementById("login-form")?.addEventListener("submit", function(e) {
  e.preventDefault();
  alert("Connexion simulée pour : " + document.getElementById("login-username").value);
});

document.getElementById("register-form")?.addEventListener("submit", function(e) {
  e.preventDefault();
  alert("Inscription simulée pour : " + document.getElementById("register-username").value);
});

let capteurs = ["Capteur Température", "Capteur Humidité"];
let actionneurs = ["Ventilateur", "Lumière LED"];

function afficherListe(id, liste) {
  const ul = document.getElementById(id);
  ul.innerHTML = "";
  liste.forEach((item, index) => {
    const li = document.createElement("li");
    li.innerHTML = `${item} <button onclick="supprimer('${id}', ${index})">Supprimer</button>`;
    ul.appendChild(li);
  });
}

function ajouterCapteur() {
  const nom = prompt("Nom du capteur ?");
  if (nom) {
    capteurs.push(nom);
    afficherListe("capteurs-list", capteurs);
  }
}

function ajouterActionneur() {
  const nom = prompt("Nom de l'actionneur ?");
  if (nom) {
    actionneurs.push(nom);
    afficherListe("actionneurs-list", actionneurs);
  }
}

function supprimer(type, index) {
  if (type === "capteurs-list") {
    capteurs.splice(index, 1);
    afficherListe(type, capteurs);
  } else {
    actionneurs.splice(index, 1);
    afficherListe(type, actionneurs);
  }
}

if (document.getElementById("capteurs-list")) {
  afficherListe("capteurs-list", capteurs);
  afficherListe("actionneurs-list", actionneurs);
}
