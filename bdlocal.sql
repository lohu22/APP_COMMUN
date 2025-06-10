CREATE TABLE FAQ (
    id_FAQ INT PRIMARY KEY
);

CREATE TABLE adminisateur (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    prenom VARCHAR(255),
    nom VARCHAR(255),
    mail VARCHAR(255),
    mot_de_passe VARCHAR(255),
    photo TEXT,
    id_FAQ INT,
    FOREIGN KEY(id_FAQ) REFERENCES FAQ(id_FAQ)
);

CREATE TABLE utilisateur (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    prenom VARCHAR(255),
    nom VARCHAR(255),
    mail VARCHAR(255),
    mot_de_passe VARCHAR(255),
    photo TEXT,
    id_FAQ INT,
    FOREIGN KEY(id_FAQ) REFERENCES FAQ(id_FAQ)
);


CREATE TABLE Photo (
    liens VARCHAR(255) PRIMARY KEY,
    numero_photo INT,
    description TEXT,
    nom_galerie VARCHAR(255),
    numero_annonce INT,
    FOREIGN KEY(numero_annonce) REFERENCES Annonce(numero_annonce)
);

CREATE TABLE Reservation (
    id_reservation INT PRIMARY KEY AUTO_INCREMENT,
    date_arrivee DATE,
    date_depart DATE,
    nombre_points INT,
    id_utilisateur INT,
    numero_annonce INT,
    FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY(numero_annonce) REFERENCES Annonce(numero_annonce)
);

CREATE TABLE Messagerie (
    id_messageadmin INT PRIMARY KEY AUTO_INCREMENT,
    Body TEXT,
    id_admin INT,
    id_utilisateur INT,
    FOREIGN KEY(id_admin) REFERENCES administrateur(id_admin),
    FOREIGN KEY(id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

