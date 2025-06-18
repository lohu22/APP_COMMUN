-- Création de la base de données PostgreSQL
-- Create PostgreSQL database
-- Note: La base de données app_db doit être créée manuellement par l'administrateur
-- Note: The app_db database must be created manually by the administrator

-- Création de la table des données des capteurs
-- Create sensor data table
CREATE TABLE IF NOT EXISTS capteur_hum_temp (
    id SERIAL PRIMARY KEY,
    HUM FLOAT NOT NULL,
    TEMP FLOAT NOT NULL,
    Temps TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Création de l'index pour optimiser les requêtes par temps
-- Create index to optimize time-based queries
CREATE INDEX IF NOT EXISTS idx_temps ON capteur_hum_temp(Temps);

-- Ajout de commentaires sur les colonnes
-- Add column comments
COMMENT ON COLUMN capteur_hum_temp.HUM IS 'Humidité en pourcentage';
COMMENT ON COLUMN capteur_hum_temp.TEMP IS 'Température en degrés Celsius';
COMMENT ON COLUMN capteur_hum_temp.Temps IS 'Date et heure de la mesure'; 