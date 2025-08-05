<?php
session_start();
session_destroy();
header("Location: connexion_etu.html");
exit();
?>
