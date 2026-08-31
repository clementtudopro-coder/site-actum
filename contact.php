<?php
// Traitement du formulaire de contact — aucune donnée n'est stockée sur le
// serveur, le message est transmis par email puis oublié : pas de base de
// données à protéger côté site vitrine.

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// Adresse de réception des messages du formulaire.
const DESTINATAIRE = 'clement@actum-conseils.fr';
const LONGUEUR_MAX_CHAMP_COURT = 200;
const LONGUEUR_MAX_MESSAGE = 4000;

function repondre(int $code, array $donnees): void {
    http_response_code($code);
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    repondre(405, ['ok' => false, 'erreur' => 'Méthode non autorisée.']);
}

// Honeypot : champ invisible pour un humain, souvent rempli par un robot.
// On répond succès sans rien envoyer, pour ne pas indiquer au robot que son
// remplissage a été détecté.
if (!empty($_POST['site_web'] ?? '')) {
    repondre(200, ['ok' => true]);
}

// Un caractère de retour à la ligne dans un champ destiné à un en-tête email
// permettrait d'injecter des en-têtes arbitraires (Bcc, etc.) — on le retire
// systématiquement de tout ce qui peut finir dans un en-tête.
function nettoyer_entete(string $valeur): string {
    return trim(str_replace(["\r", "\n"], '', $valeur));
}

$nom = trim((string)($_POST['nom'] ?? ''));
$email = nettoyer_entete((string)($_POST['email'] ?? ''));
$entreprise = trim((string)($_POST['entreprise'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($nom === '' || $email === '' || $message === '') {
    repondre(400, ['ok' => false, 'erreur' => 'Merci de renseigner votre nom, votre email et un message.']);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    repondre(400, ['ok' => false, 'erreur' => 'Adresse email invalide.']);
}
if (mb_strlen($nom) > LONGUEUR_MAX_CHAMP_COURT || mb_strlen($entreprise) > LONGUEUR_MAX_CHAMP_COURT) {
    repondre(400, ['ok' => false, 'erreur' => 'Un des champs dépasse la longueur autorisée.']);
}
if (mb_strlen($message) > LONGUEUR_MAX_MESSAGE) {
    repondre(400, ['ok' => false, 'erreur' => 'Le message est trop long.']);
}

$sujet = 'Nouveau message depuis actum-conseils.fr';
$corps = "Nom : {$nom}\n"
    . "Email : {$email}\n"
    . ($entreprise !== '' ? "Entreprise : {$entreprise}\n" : '')
    . "\nMessage :\n{$message}\n";

// From fixe sur le domaine du site (recommandé pour la délivrabilité) ;
// l'adresse du visiteur est en Reply-To, jamais dans From, pour ne pas se
// faire passer pour lui auprès des filtres anti-spam du destinataire.
$entetes = [
    'From: ACTUM Site <no-reply@actum-conseils.fr>',
    'Reply-To: ' . $email,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
];

$envoye = @mail(DESTINATAIRE, $sujet, $corps, implode("\r\n", $entetes));

if (!$envoye) {
    repondre(500, ['ok' => false, 'erreur' => "L'envoi a échoué, réessayez ou écrivez directement par email."]);
}

repondre(200, ['ok' => true]);
