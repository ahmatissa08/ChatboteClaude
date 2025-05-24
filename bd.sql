-- =======================================================
-- BASE DE DONNÉES CHATBOT IAM
-- =======================================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS chatbot_iam 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE chatbot_iam;

-- =======================================================
-- TABLE DES UTILISATEURS
-- =======================================================
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_complet VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    mot_de_passe VARCHAR(255) NOT NULL,
    type_utilisateur ENUM('etudiant', 'prospect', 'parent', 'enseignant', 'admin') NOT NULL DEFAULT 'prospect',
    statut ENUM('actif', 'inactif', 'suspendu') NOT NULL DEFAULT 'actif',
    photo_profil VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL,
    preferences_theme ENUM('light', 'dark') DEFAULT 'light',
    langue ENUM('fr', 'en') DEFAULT 'fr',
    INDEX idx_email (email),
    INDEX idx_type (type_utilisateur),
    INDEX idx_statut (statut)
);

-- =======================================================
-- TABLE DES PROGRAMMES/FORMATIONS
-- =======================================================
CREATE TABLE programmes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    duree_mois INT NOT NULL,
    prix_mensuel DECIMAL(10,2) NOT NULL,
    frais_inscription DECIMAL(10,2) DEFAULT 50000,
    niveau ENUM('bachelor', 'master', 'mba', 'certification') NOT NULL,
    domaine VARCHAR(50) NOT NULL,
    statut ENUM('actif', 'inactif', 'complet') DEFAULT 'actif',
    places_disponibles INT DEFAULT 0,
    prerequis TEXT,
    objectifs TEXT,
    debouches TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_niveau (niveau),
    INDEX idx_domaine (domaine),
    INDEX idx_statut (statut)
);

-- =======================================================
-- TABLE DES CONVERSATIONS
-- =======================================================
CREATE TABLE conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT,
    titre VARCHAR(150),
    statut ENUM('active', 'archivee', 'supprimee') DEFAULT 'active',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_statut (statut),
    INDEX idx_date (date_creation)
);

-- =======================================================
-- TABLE DES MESSAGES
-- =======================================================
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT,
    utilisateur_id INT NULL,
    type_message ENUM('user', 'bot', 'system') NOT NULL,
    contenu TEXT NOT NULL,
    metadata JSON, -- Pour stocker des données supplémentaires (pièces jointes, etc.)
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_conversation (conversation_id),
    INDEX idx_type (type_message),
    INDEX idx_date (date_envoi)
);

-- =======================================================
-- TABLE DES DEMANDES D'INSCRIPTION
-- =======================================================
CREATE TABLE demandes_inscription (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_complet VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    programme_id INT,
    niveau_etude VARCHAR(50),
    experience_professionnelle TEXT,
    motivation TEXT,
    statut ENUM('en_attente', 'acceptee', 'refusee', 'en_cours') DEFAULT 'en_attente',
    notes_admin TEXT,
    utilisateur_id INT NULL, -- Lié si l'utilisateur s'est inscrit
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_traitement TIMESTAMP NULL,
    traite_par INT NULL, -- ID de l'admin qui a traité
    FOREIGN KEY (programme_id) REFERENCES programmes(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    FOREIGN KEY (traite_par) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_statut (statut),
    INDEX idx_programme (programme_id),
    INDEX idx_date (date_demande)
);

-- =======================================================
-- TABLE DES ÉVÉNEMENTS/CALENDRIER
-- =======================================================
CREATE TABLE evenements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    type_evenement ENUM('rentree', 'examens', 'portes_ouvertes', 'conference', 'autre') NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE,
    heure_debut TIME,
    heure_fin TIME,
    lieu VARCHAR(100),
    public_cible ENUM('tous', 'etudiants', 'prospects', 'parents', 'enseignants') DEFAULT 'tous',
    statut ENUM('prevu', 'en_cours', 'termine', 'annule') DEFAULT 'prevu',
    places_limitees BOOLEAN DEFAULT FALSE,
    nombre_places INT NULL,
    inscriptions_ouvertes BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (type_evenement),
    INDEX idx_date (date_debut),
    INDEX idx_public (public_cible),
    INDEX idx_statut (statut)
);

-- =======================================================
-- TABLE DES INSCRIPTIONS AUX ÉVÉNEMENTS
-- =======================================================
CREATE TABLE inscriptions_evenements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    evenement_id INT NOT NULL,
    utilisateur_id INT,
    nom_complet VARCHAR(100), -- Pour les non-inscrits
    email VARCHAR(100),
    telephone VARCHAR(20),
    statut ENUM('confirme', 'en_attente', 'annule') DEFAULT 'confirme',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_inscription (evenement_id, utilisateur_id),
    INDEX idx_evenement (evenement_id),
    INDEX idx_statut (statut)
);

-- =======================================================
-- TABLE DES SESSIONS/TOKENS
-- =======================================================
CREATE TABLE sessions_utilisateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_expiration TIMESTAMP NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_expiration (date_expiration)
);

-- =======================================================
-- TABLE DES STATISTIQUES/ANALYTICS
-- =======================================================
CREATE TABLE statistiques_chat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT,
    type_interaction ENUM('message_envoye', 'question_posee', 'inscription_demandee', 'evenement_consulte') NOT NULL,
    categorie VARCHAR(50), -- programmes, tarifs, inscription, etc.
    details JSON,
    date_interaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    session_id VARCHAR(100),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_type (type_interaction),
    INDEX idx_categorie (categorie),
    INDEX idx_date (date_interaction),
    INDEX idx_utilisateur (utilisateur_id)
);

-- =======================================================
-- TABLE DE CONFIGURATION DU SYSTÈME
-- =======================================================
CREATE TABLE configuration_systeme (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cle_config VARCHAR(100) UNIQUE NOT NULL,
    valeur TEXT,
    description TEXT,
    type_donnee ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    modifiable BOOLEAN DEFAULT TRUE,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cle (cle_config)
);

-- =======================================================
-- DONNÉES D'EXEMPLE
-- =======================================================

-- Insertion des programmes
INSERT INTO programmes (nom, code, description, duree_mois, prix_mensuel, niveau, domaine, places_disponibles, objectifs, debouches) VALUES
('Bachelor Informatique', 'BACH-INFO', 'Formation complète en développement logiciel et systèmes informatiques', 36, 150000, 'bachelor', 'Informatique', 50, 'Maîtriser les langages de programmation, bases de données, et architecture logicielle', 'Développeur, Analyste programmeur, Chef de projet IT'),
('Bachelor Data Science', 'BACH-DATA', 'Spécialisation en analyse de données et intelligence artificielle', 36, 175000, 'bachelor', 'Data Science', 30, 'Expertise en machine learning, big data et visualisation', 'Data Scientist, Data Analyst, Consultant BI'),
('Bachelor Droit', 'BACH-DROIT', 'Formation juridique généraliste avec spécialisations', 36, 125000, 'bachelor', 'Droit', 40, 'Maîtrise du droit des affaires, pénal et civil', 'Avocat, Juriste d\'entreprise, Magistrat'),
('BBA - Business Administration', 'BBA', 'Formation en gestion et administration des entreprises', 36, 175000, 'bachelor', 'Business', 35, 'Leadership, gestion stratégique et entrepreneuriat', 'Manager, Consultant, Entrepreneur'),
('MBA Executive', 'MBA-EXEC', 'Master en administration des affaires pour cadres', 24, 250000, 'mba', 'Management', 25, 'Management avancé et leadership stratégique', 'Directeur général, Consultant senior, Entrepreneur');

-- Insertion des événements
INSERT INTO evenements (titre, description, type_evenement, date_debut, date_fin, heure_debut, heure_fin, lieu, public_cible) VALUES
('Rentrée Académique 2024', 'Accueil des nouveaux étudiants et présentation des programmes', 'rentree', '2024-09-15', '2024-09-15', '08:00:00', '17:00:00', 'Campus Principal', 'etudiants'),
('Journée Portes Ouvertes', 'Découvrez nos programmes et visitez nos installations', 'portes_ouvertes', '2024-08-30', '2024-08-30', '09:00:00', '16:00:00', 'Campus Principal', 'tous'),
('Session d\'Examens S1', 'Examens du premier semestre', 'examens', '2024-12-15', '2024-12-25', '08:00:00', '18:00:00', 'Campus Principal', 'etudiants'),
('Conférence sur l\'IA', 'L\'Intelligence Artificielle dans le monde professionnel', 'conference', '2024-10-20', '2024-10-20', '14:00:00', '17:00:00', 'Amphithéâtre A', 'tous');

-- Configuration système
INSERT INTO configuration_systeme (cle_config, valeur, description, type_donnee) VALUES
('site_nom', 'IAM - Institut Africain de Management', 'Nom de l\'établissement', 'string'),
('contact_email', 'contact@iam.sn', 'Email de contact principal', 'string'),
('contact_telephone', '+221 XX XXX XX XX', 'Téléphone principal', 'string'),
('adresse', 'Dakar, Sénégal', 'Adresse de l\'établissement', 'string'),
('horaires_ouverture', '{"lundi_vendredi": "8h-18h", "samedi": "8h-14h", "dimanche": "fermé"}', 'Horaires d\'ouverture', 'json'),
('frais_inscription_defaut', '50000', 'Frais d\'inscription par défaut', 'integer'),
('max_conversations_par_utilisateur', '10', 'Nombre maximum de conversations par utilisateur', 'integer'),
('chatbot_nom', 'Coumba', 'Nom du chatbot', 'string'),
('maintenance_mode', 'false', 'Mode maintenance activé', 'boolean');

-- =======================================================
-- VUES UTILES
-- =======================================================

-- Vue pour les statistiques des programmes
CREATE VIEW vue_stats_programmes AS
SELECT 
    p.id,
    p.nom,
    p.code,
    p.prix_mensuel,
    COUNT(di.id) as nombre_demandes,
    COUNT(CASE WHEN di.statut = 'acceptee' THEN 1 END) as demandes_acceptees,
    COUNT(CASE WHEN di.statut = 'en_attente' THEN 1 END) as demandes_en_attente,
    p.places_disponibles
FROM programmes p
LEFT JOIN demandes_inscription di ON p.id = di.programme_id
GROUP BY p.id;

-- Vue pour l'historique des conversations utilisateur
CREATE VIEW vue_conversations_utilisateur AS
SELECT 
    c.id as conversation_id,
    c.titre,
    u.nom_complet,
    u.email,
    COUNT(m.id) as nombre_messages,
    c.date_creation,
    c.date_modification,
    c.statut
FROM conversations c
JOIN utilisateurs u ON c.utilisateur_id = u.id
LEFT JOIN messages m ON c.id = m.conversation_id
GROUP BY c.id;

-- =======================================================
-- PROCÉDURES STOCKÉES
-- =======================================================

DELIMITER $$

-- Procédure pour créer une nouvelle conversation
CREATE PROCEDURE CreerNouvelleConversation(
    IN p_utilisateur_id INT,
    IN p_titre VARCHAR(150),
    OUT p_conversation_id INT
)
BEGIN
    INSERT INTO conversations (utilisateur_id, titre) 
    VALUES (p_utilisateur_id, p_titre);
    SET p_conversation_id = LAST_INSERT_ID();
END$$

-- Procédure pour ajouter un message
CREATE PROCEDURE AjouterMessage(
    IN p_conversation_id INT,
    IN p_utilisateur_id INT,
    IN p_type_message ENUM('user', 'bot', 'system'),
    IN p_contenu TEXT,
    IN p_metadata JSON
)
BEGIN
    INSERT INTO messages (conversation_id, utilisateur_id, type_message, contenu, metadata)
    VALUES (p_conversation_id, p_utilisateur_id, p_type_message, p_contenu, p_metadata);
    
    -- Mettre à jour la date de modification de la conversation
    UPDATE conversations 
    SET date_modification = CURRENT_TIMESTAMP 
    WHERE id = p_conversation_id;
END$$

-- Procédure pour traiter une demande d'inscription
CREATE PROCEDURE TraiterDemandeInscription(
    IN p_demande_id INT,
    IN p_statut ENUM('acceptee', 'refusee'),
    IN p_notes_admin TEXT,
    IN p_admin_id INT
)
BEGIN
    UPDATE demandes_inscription 
    SET 
        statut = p_statut,
        notes_admin = p_notes_admin,
        date_traitement = CURRENT_TIMESTAMP,
        traite_par = p_admin_id
    WHERE id = p_demande_id;
END$$

DELIMITER ;

-- =======================================================
-- INDEX POUR OPTIMISATION
-- =======================================================

-- Index composites pour les requêtes fréquentes
CREATE INDEX idx_messages_conversation_date ON messages(conversation_id, date_envoi);
CREATE INDEX idx_demandes_statut_date ON demandes_inscription(statut, date_demande);
CREATE INDEX idx_stats_utilisateur_date ON statistiques_chat(utilisateur_id, date_interaction);
CREATE INDEX idx_evenements_date_public ON evenements(date_debut, public_cible);

-- =======================================================
-- TRIGGERS
-- =======================================================

DELIMITER $$

-- Trigger pour nettoyer les sessions expirées
CREATE TRIGGER cleanup_expired_sessions
BEFORE INSERT ON sessions_utilisateur
FOR EACH ROW
BEGIN
    DELETE FROM sessions_utilisateur 
    WHERE date_expiration < NOW();
END$$

-- Trigger pour enregistrer les statistiques
CREATE TRIGGER log_user_interaction
AFTER INSERT ON messages
FOR EACH ROW
BEGIN
    IF NEW.type_message = 'user' THEN
        INSERT INTO statistiques_chat (utilisateur_id, type_interaction, details, session_id)
        VALUES (NEW.utilisateur_id, 'message_envoye', JSON_OBJECT('message_id', NEW.id), CONCAT('session_', NEW.utilisateur_id, '_', UNIX_TIMESTAMP()));
    END IF;
END$$

DELIMITER ;

-- =======================================================
-- UTILISATEUR ADMIN PAR DÉFAUT
-- =======================================================

-- Mot de passe: admin123 (à changer en production)
INSERT INTO utilisateurs (nom_complet, email, mot_de_passe, type_utilisateur) VALUES
('Administrateur Système', 'admin@iam.sn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =======================================================
-- PERMISSIONS ET SÉCURITÉ
-- =======================================================

-- Créer un utilisateur spécifique pour l'application
-- CREATE USER 'chatbot_user'@'localhost' IDENTIFIED BY 'mot_de_passe_securise';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON chatbot_iam.* TO 'chatbot_user'@'localhost';
-- FLUSH PRIVILEGES;