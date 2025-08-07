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
    ecole_universite TEXT NOT NULL,
    site_ville TEXT NOT NULL,
    classe_niveau TEXT NOT NULL,
    annee_academique TEXT NOT NULL,
    option_filiere TEXT NOT NULL,
    ue TEXT NOT NULL,
    matiere TEXT NOT NULL
);
