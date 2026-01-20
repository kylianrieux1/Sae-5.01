SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS Groupes (
    NoGroupe INT NOT NULL UNIQUE,
    NomGroupe VARCHAR(50) NOT NULL UNIQUE,
    TailleGroupe INT NOT NULL CHECK (TailleGroupe >= 1 AND TailleGroupe <= 14),
    PRIMARY KEY (NoGroupe)
);

CREATE TABLE IF NOT EXISTS Etudiants (
    NoEtu INT AUTO_INCREMENT,
    Nom VARCHAR(100) NOT NULL,
    NoGroupe INT NOT NULL,
    PRIMARY KEY (NoEtu),
    CONSTRAINT fk_NoGroupe FOREIGN KEY (NoGroupe)
        REFERENCES Groupes (NoGroupe)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO Groupes (NoGroupe, NomGroupe, TailleGroupe) VALUES
(1, 'A1', 4),
(2, 'A2', 6),
(3, 'B1', 8),
(4, 'B2', 11),
(5, 'C1', 3),
(6, 'C2', 7),
(7, 'D1', 2),
(8, 'D2', 13),
(9, 'E1', 1),
(10, 'E2', 14);

INSERT INTO Etudiants (NoEtu, Nom, NoGroupe) VALUES
(1, 'Jacques', 1),
(2, 'Marc', 4),
(4, 'Pierre', 2),
(5, 'Luc', 10),
(6, 'Claude', 9),
(7, 'Michelle', 6),
(8, 'Camille', 1),
(9, 'Nina', 3),
(10, 'Lucie', 3),
(12, 'alban', 2),
(15, 'JUL', 4),
(16, 'alonso', 5),
(17, 'naps', 2),
(18, 'rieux', 1),
(19, 'ethan', 1),
(21, 'elvis', 1),
(22, 'Dylan', 1),
(23, 'ninon', 4),
(29, 'JC2', 5),
(30, 'Johnny Hallyday', 1);

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
