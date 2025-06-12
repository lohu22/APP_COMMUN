-- Création de la base de données
-- Create database
CREATE DATABASE IF NOT EXISTS sensor_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Utiliser la base de données
-- Use database
USE sensor_db;

-- Création de la table des données des capteurs
-- Create sensor data table
CREATE TABLE IF NOT EXISTS sensor_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    HUM FLOAT NOT NULL COMMENT 'Humidité en pourcentage',
    TEMP FLOAT NOT NULL COMMENT 'Température en degrés Celsius',
    Temps DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date et heure de la mesure',
    INDEX idx_temps (Temps)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 