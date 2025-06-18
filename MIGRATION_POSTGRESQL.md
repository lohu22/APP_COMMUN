# Migration de MySQL vers PostgreSQL

## Overview

Ce projet a été migré de MySQL vers PostgreSQL pour utiliser la base de données suivante :
This project has been migrated from MySQL to PostgreSQL to use the following database:

- **Hôte / Host**: app.garageisep.com
- **Port**: 5408
- **Utilisateur / User**: app_user
- **Mot de passe / Password**: appg8
- **Base de données / Database**: app_db

## Changements effectués / Changes Made

### 1. Configuration de la base de données / Database Configuration

**Fichier modifié / Modified file**: `models/Database.php`

- Changement du driver PDO de `mysql` vers `pgsql`
- Mise à jour des paramètres de connexion
- Suppression de `set names utf8` (non nécessaire pour PostgreSQL)

### 2. Nom de la table / Table Name

**Fichier modifié / Modified file**: `models/SensorData.php`

- Changement du nom de table de `sensor_data` vers `capteur_hum_temp`

### 3. Script de création de base de données / Database Creation Script

**Fichier modifié / Modified file**: `database/create_database.sql`

- Adaptation de la syntaxe MySQL vers PostgreSQL
- Utilisation de `SERIAL` au lieu de `AUTO_INCREMENT`
- Utilisation de `TIMESTAMP` au lieu de `DATETIME`
- Séparation de la création d'index et des commentaires

## Différences de syntaxe / Syntax Differences

| MySQL | PostgreSQL |
|-------|------------|
| `AUTO_INCREMENT` | `SERIAL` |
| `DATETIME` | `TIMESTAMP` |
| `COMMENT 'text'` | `COMMENT ON COLUMN` |
| `ENGINE=InnoDB` | Non nécessaire |
| `CHARACTER SET` | Non nécessaire |

## Test de connexion / Connection Test

Un script de test a été créé pour vérifier la connexion PostgreSQL :
A test script has been created to verify the PostgreSQL connection:

```bash
php test_postgresql_connection.php
```

Ce script teste :
This script tests:
- La connexion à la base de données / Database connection
- La création de la table / Table creation
- L'insertion de données / Data insertion
- La lecture de données / Data reading

## Prérequis / Prerequisites

1. Extension PHP PDO PostgreSQL activée
2. Base de données `app_db` créée sur le serveur PostgreSQL
3. Utilisateur `app_user` avec les permissions appropriées

## Notes importantes / Important Notes

- La base de données `app_db` doit être créée manuellement par l'administrateur
- Les permissions de l'utilisateur `app_user` doivent inclure CREATE, INSERT, SELECT sur la base de données
- L'extension `pdo_pgsql` doit être activée dans la configuration PHP

## Vérification / Verification

Après la migration, vérifiez que :
After migration, verify that:

1. La connexion PostgreSQL fonctionne
2. La table `capteur_hum_temp` est créée correctement
3. Les données peuvent être insérées et lues
4. Toutes les fonctionnalités de l'application fonctionnent normalement 