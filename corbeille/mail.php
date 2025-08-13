<?php
$to = "zinsouplyornel@gmail.com";
$subject = "Test mail PHP";
$message = "Ceci est un test d'envoi mail.";
$headers = "From: noreply@examotheque.com\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Mail envoyé avec succès";
} else {
    echo "Échec de l'envoi du mail";
}
?>
