-- Datenbank anlegen
CREATE DATABASE IF NOT EXISTS sweDatenbank
    CHARACTER SET UTF8mb4
    COLLATE utf8mb4_unicode_ci;

USE sweDatenbank;

-- Tabelle benutzer
CREATE TABLE IF NOT EXISTS benutzer
(
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rolle ENUM('lehrer', 'schueler') NOT NULL DEFAULT 'schueler'
);

-- Tabelle bereiche
CREATE TABLE IF NOT EXISTS bereiche (
                                        id INT PRIMARY KEY AUTO_INCREMENT,
                                        titel VARCHAR(255) NOT NULL
);

-- Tabelle themen
CREATE TABLE IF NOT EXISTS themen (
                                      id INT PRIMARY KEY AUTO_INCREMENT,
                                      titel VARCHAR(255) NOT NULL,
                                      bereich_id INT NOT NULL,
                                      FOREIGN KEY (bereich_id) REFERENCES bereiche(id)
                                          ON DELETE CASCADE
);

ALTER TABLE themen
    ADD pdf_datei VARCHAR(255) DEFAULT NULL;

-- Tabelle erklaerungen
CREATE TABLE IF NOT EXISTS erklaerungen (
                                            id INT PRIMARY KEY AUTO_INCREMENT,
                                            themaId INT NOT NULL,
                                            text TEXT NOT NULL,
                                            FOREIGN KEY(themaId)
                                                REFERENCES themen(id)
                                                ON DELETE CASCADE
);

-- Tabelle formeln
CREATE TABLE IF NOT EXISTS formeln (
                                       id INT PRIMARY KEY AUTO_INCREMENT,
                                       themaId INT NOT NULL,
                                       text TEXT NOT NULL,
                                       FOREIGN KEY(themaId)
                                           REFERENCES themen(id)
                                           ON DELETE CASCADE
);



-- Tabelle beispiele
CREATE TABLE IF NOT EXISTS beispiele (
                                         id INT PRIMARY KEY AUTO_INCREMENT,
                                         themaId INT NOT NULL,
                                         text TEXT NOT NULL,
                                         bild VARCHAR(255) DEFAULT NULL,
                                         FOREIGN KEY (themaId)
                                             REFERENCES themen(id)
                                             ON DELETE CASCADE
);
