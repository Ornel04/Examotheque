CREATE DATABASE IF NOT EXISTS examotheque;
USE examotheque;

CREATE TABLE IF NOT EXISTS utilisateur_etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS utilisateur_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS download_epreuve (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_universite VARCHAR(255) NOT NULL,
    site_ville VARCHAR(255) NOT NULL,
    classe_niveau VARCHAR(100) NOT NULL,
    annee_academique VARCHAR(20) NOT NULL,
    option_filiere VARCHAR(255) NOT NULL,
    ue VARCHAR(255) NOT NULL,
    matiere VARCHAR(255) NOT NULL,
    nom_epreuve VARCHAR(255) DEFAULT NULL,
    fichier_path VARCHAR(500) DEFAULT NULL
);

