function sendVerificationCode() {
    const email = document.querySelector('input[name="email"]').value;
    const msg = document.getElementById("code-msg");

    if (!email) {
        msg.style.color = "red";
        msg.textContent = "Veuillez entrer votre email avant d'envoyer le code.";
        return;
    }

    fetch("../controllers/verification_code.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `email=${encodeURIComponent(email)}`
    })
    .then(response => response.text())
    .then(text => {
        msg.style.color = "green";
        msg.textContent = text;
    })
    .catch(error => {
        msg.style.color = "red";
        msg.textContent = "Erreur lors de l'envoi du code.";
    });
}