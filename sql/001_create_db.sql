-- base de données gestion etudiants
DROP DATABASE IF EXISTS gestion_etudiants_pdo;
CREATE DATABASE gestion_etudiants_pdo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_etudiants_pdo;

-- table filiere
CREATE TABLE filiere (
  id INT AUTO_INCREMENT PRIMARY KEY, -- id
  code VARCHAR(16) NOT NULL,          -- code filiere
  libelle VARCHAR(100) NOT NULL,       -- nom filiere
  CONSTRAINT uq_filiere_code UNIQUE (code) -- pas de doublon
) ENGINE=InnoDB;

-- table etudiant
CREATE TABLE etudiant (
  id INT AUTO_INCREMENT PRIMARY KEY, -- id etudiant
  cne VARCHAR(20) NOT NULL,           -- CNE
  nom VARCHAR(80) NOT NULL,           -- nom
  prenom VARCHAR(80) NOT NULL,        -- prenom
  email VARCHAR(120) NOT NULL,        -- email
  filiere_id INT NOT NULL,            -- filiere

  FOREIGN KEY (filiere_id) REFERENCES filiere(id), -- relation filiere
  UNIQUE (cne),   -- CNE unique
  UNIQUE (email)  -- email unique
) ENGINE=InnoDB;

-- insertion filieres  
INSERT INTO filiere(code, libelle) VALUES
('BD', 'Big Data'),
('INFO', 'Informatique');

-- insertion etudiants  
INSERT INTO etudiant(cne, nom, prenom, email, filiere_id) VALUES
('CNE1001', 'Salma', 'Salma', 'salma@gmail.com', 1),
('CNE1002', 'Hind', 'Hind', 'hind@gmail.com', 2);