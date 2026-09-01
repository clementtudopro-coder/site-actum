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

Configuré : dépôt GitHub `clementtudopro-coder/site-actum`, cloné côté serveur dans `/home/tucl9724/repositories/site-actum` via une clé SSH dédiée (`~/.ssh/id_ed25519_site_actum` sur le serveur, alias `github-site-actum.deploy`). `.cpanel.yml` fait un rsync vers `/home/tucl9724/public_html` — c'est bien la racine du domaine `actum-conseils.fr` (vérifié dans cPanel : le domaine partage `/public_html` avec le domaine principal du compte).

Comme pour `app.actum-conseils.fr`, chaque "Deploy HEAD Commit" réinitialise les permissions de `/public_html` à 700 (bug cPanel connu), ce qui casse la lecture du `.htaccess` → 403. Un cron (`*/5 * * * * chmod 755 /home/tucl9724/public_html`) corrige ça automatiquement sous 5 min ; en cas d'urgence, lancer le chmod à la main via le Terminal cPanel juste après un déploiement.

**HTTPS** : `actum-conseils.fr` et `app.actum-conseils.fr` sont tous les deux encore sur le certificat auto-signé par défaut de cPanel (AutoSSL n'a pas encore émis de certificat Let's Encrypt réel) — rien côté code ou config de ce dépôt n'en est la cause. Le `.htaccess` laisse la redirection forcée vers HTTPS commentée tant que ce n'est pas résolu, pour ne pas rendre le site inaccessible.
