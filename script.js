document.getElementById("annee").textContent = new Date().getFullYear();

// --- Menu mobile ---
const burger = document.getElementById("menu-burger");
const navMobile = document.getElementById("nav-mobile");
burger.addEventListener("click", () => {
  document.body.classList.toggle("menu-ouvert");
});
navMobile.querySelectorAll("a").forEach((lien) => {
  lien.addEventListener("click", () => document.body.classList.remove("menu-ouvert"));
});

// --- Formulaire de contact ---
const form = document.getElementById("form-contact");
const msg = document.getElementById("form-msg");
const btnEnvoyer = document.getElementById("btn-envoyer");

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  msg.className = "msg";
  msg.textContent = "";
  btnEnvoyer.disabled = true;
  btnEnvoyer.textContent = "Envoi en cours...";

  try {
    const reponse = await fetch("contact.php", {
      method: "POST",
      headers: { "Accept": "application/json" },
      body: new FormData(form),
    });
    const data = await reponse.json();
    if (reponse.ok && data.ok) {
      msg.className = "msg ok";
      msg.textContent = "Message envoyé — je vous réponds personnellement dès que possible.";
      form.reset();
    } else {
      msg.className = "msg erreur";
      msg.textContent = data.erreur || "Une erreur est survenue, réessayez ou écrivez-moi directement par email.";
    }
  } catch (err) {
    msg.className = "msg erreur";
    msg.textContent = "Impossible d'envoyer le message pour le moment — écrivez-moi directement par email.";
  } finally {
    btnEnvoyer.disabled = false;
    btnEnvoyer.textContent = "Envoyer le message";
  }
});
