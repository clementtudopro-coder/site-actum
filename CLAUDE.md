# CLAUDE.md

## Ce que c'est

Site vitrine public pour l'activité de conseil en développement commercial de Clément Tudo ("ACTUM"). **Projet totalement séparé d'`Interface_ACTUM`** (l'outil interne de pilotage de missions, avec des données clients sensibles) — ne jamais mélanger les deux dépôts, ni leurs déploiements.

Destiné à `actum-conseils.fr` (domaine racine), séparé de `app.actum-conseils.fr` (sous-domaine de l'outil interne), sur le même compte o2switch (`tucl9724`).

## Architecture

- Site statique : `index.html` + `style.css` + `script.js`, une seule page avec ancres (`#approche`, `#services`, `#apropos`, `#contact`). Pas de framework, pas de build.
- `contact.php` : seul élément dynamique — traite le formulaire de contact et envoie un email via `mail()`, sans rien stocker côté serveur. Validation stricte + nettoyage anti-injection d'en-têtes sur le champ email, honeypot (`site_web`) contre les robots.
- `mentions-legales.html` : infos légales (SIRET, forme juridique, adresse) issues du registre public (societe.com) — à revérifier avec Clément si elles changent, mais à jour au moment de la rédaction.
- Police via Google Fonts (Fraunces + Inter) — connexion externe assumée, ce n'est pas un contexte Artifact contraint par une CSP.

## Déploiement

Pas encore configuré. À faire sur le même modèle que les autres projets sur ce compte (dépôt git dédié, clé SSH dédiée, cPanel Git Version Control) — vérifier d'abord le dossier racine réel du domaine `actum-conseils.fr` dans cPanel avant de configurer le déploiement, ne pas supposer qu'il s'agit de `/public_html`.
