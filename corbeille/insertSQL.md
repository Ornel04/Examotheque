INSERT INTO `download_epreuve` (`id`, `ecole_universite`, `site_ville`, `classe_niveau`, `annee_academique`, `option_filiere`, `ue`, `matiere`, `nom_epreuve`, `fichier_path`) VALUES
(20, 'ENSIMAG', 'lyon', 'L3', '2021-2022', 'informatique', 'Systèmes distribués', 'Communication interprocessus', NULL, NULL),
(19, 'ENSIMAG', 'grenoble', 'L2', '2022-2023', 'informatique', 'Algorithmique', 'Tri et recherche', NULL, NULL),
(18, 'ENSIMAG', 'grenoble', 'L1', '2023-2024', 'mathématiques', 'Algèbre linéaire', 'Matrices', NULL, NULL),
(17, 'UTC', 'paris', 'L3', '2021-2022', 'cybersécurité', 'Cryptanalyse', 'Analyse des malwares', NULL, NULL),
(16, 'UTC', 'amiens', 'L2', '2022-2023', 'réseaux', 'Télécommunications', 'Protocoles IP', NULL, NULL),
(15, 'UTC', 'compiegne', 'L1', '2023-2024', 'informatique', 'Bases de données', 'SQL avancé', NULL, NULL),
(14, 'INSA', 'nantes', 'L3', '2021-2022', 'systèmes embarqués', 'Electronique numérique', 'Microcontrôleurs', NULL, NULL),
(13, 'INSA', 'strasbourg', 'L2', '2022-2023', 'réseaux', 'Sécurité des réseaux', 'Firewall', NULL, NULL),
(12, 'INSA', 'lyon', 'L1', '2023-2024', 'informatique', 'Programmation avancée', 'Java', NULL, NULL),
(11, 'ESEO', 'paris', '5iem', '2021-2022', 'CSS', 'Infrastructure serveurs Microsoft', 'PowerShell avancé', NULL, NULL),
(10, 'ESEO', 'angers', '4iem', '2022-2023', 'EOC', 'Infrastructure serveurs Microsoft', 'Sécurité Windows Server', NULL, NULL),
(9, 'ESEO', 'dijon', '5iem', '2021-2022', 'general', 'Infrastructure serveurs Microsoft', 'Hyper-V', NULL, NULL),
(8, 'ESEO', 'paris', '4iem', '2022-2023', 'CSS', 'Infrastructure serveurs Microsoft', 'Exchange Server', NULL, NULL),
(7, 'ESEO', 'angers', '3iem', '2023-2024', 'EOC', 'Infrastructure serveurs Microsoft', 'Active Directory', NULL, NULL),
(6, 'ESEO', 'dijon', '5iem', '2021-2022', 'CSS', 'Administration des systèmes linux', 'Gestion des utilisateurs', NULL, NULL),
(5, 'ESEO', 'paris', '4iem', '2022-2023', 'general', 'Introduction à la cybersécurité', 'Audit de sécurité', NULL, NULL),
(1, 'ESEO', 'angers', '3iem', '2023-2024', 'general', 'Architecture des Data-Centers', 'Réseaux et virtualisation', NULL, NULL),
(2, 'ESEO', 'paris', '4iem', '2022-2023', 'CSS', 'Architecture des Data-Centers', 'Stockage et sauvegarde', NULL, NULL),
(3, 'ESEO', 'dijon', '5iem', '2021-2022', 'EOC', 'Introduction à la cybersécurité', 'Cryptographie', NULL, NULL),
(4, 'ESEO', 'angers', '3iem', '2023-2024', 'CSS', 'Introduction à la cybersécurité', 'Sécurité réseau', NULL, NULL);



INSERT INTO `utilisateur_admins` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`) VALUES
(1, 'administrateur', 'administrateur', 'administrateur@gmail.com', '$2y$10$kBfnjUpa01sZmrCNR/YxW.Z2oKpz9WIJyRuNFDXl1BqbGi8w4rBCi');