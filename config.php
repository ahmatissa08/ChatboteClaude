<?php
// =======================================================
// CONFIG/DATABASE.PHP - Configuration de la base de données
// =======================================================

class Database {
    private $host = 'localhost';
    private $db_name = 'chatbot_iam';
    private $username = 'chatbot_user'; // ou 'root' pour le développement
    private $password = 'mot_de_passe_securise'; // à personnaliser
    private $charset = 'utf8mb4';
    private $pdo;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Erreur d'exécution de la requête: " . $e->getMessage());
        }
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function execute($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

// =======================================================
// CLASSES/USER.PHP - Gestion des utilisateurs
// =======================================================

class User {
    private $db;
    
    public function __construct(Database $database) {
        $this->db = $database;
    }

    public function register($data) {
        $sql = "INSERT INTO utilisateurs (nom_complet, email, telephone, mot_de_passe, type_utilisateur) 
                VALUES (:nom_complet, :email, :telephone, :mot_de_passe, :type_utilisateur)";
        
        $params = [
            ':nom_complet' => $data['nom_complet'],
            ':email' => $data['email'],
            ':telephone' => $data['telephone'] ?? null,
            ':mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            ':type_utilisateur' => $data['type_utilisateur'] ?? 'prospect'
        ];

        try {
            $this->db->execute($sql, $params);
            return ['success' => true, 'user_id' => $this->db->lastInsertId()];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email AND statut = 'actif'";
        $user = $this->db->fetch($sql, [':email' => $email]);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Mettre à jour la dernière connexion
            $this->updateLastLogin($user['id']);
            
            // Créer une session
            $token = $this->createSession($user['id']);
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'nom_complet' => $user['nom_complet'],
                    'email' => $user['email'],
                    'type_utilisateur' => $user['type_utilisateur'],
                    'preferences_theme' => $user['preferences_theme']
                ],
                'token' => $token
            ];
        }

        return ['success' => false, 'error' => 'Identifiants incorrects'];
    }

    private function updateLastLogin($userId) {
        $sql = "UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = :id";
        $this->db->execute($sql, [':id' => $userId]);
    }

    private function createSession($userId) {
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO sessions_utilisateur (utilisateur_id, token, ip_address, user_agent, date_expiration) 
                VALUES (:user_id, :token, :ip, :user_agent, DATE_ADD(NOW(), INTERVAL 24 HOUR))";
        
        $params = [
            ':user_id' => $userId,
            ':token' => $token,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];

        $this->db->execute($sql, $params);
        return $token;
    }

    public function validateSession($token) {
        $sql = "SELECT u.* FROM utilisateurs u 
                JOIN sessions_utilisateur s ON u.id = s.utilisateur_id 
                WHERE s.token = :token AND s.date_expiration > NOW() AND s.actif = 1";
        
        return $this->db->fetch($sql, [':token' => $token]);
    }
}

// =======================================================
// CLASSES/CHAT.PHP - Gestion des conversations
// =======================================================

class Chat {
    private $db;
    
    public function __construct(Database $database) {
        $this->db = $database;
    }

    public function createConversation($userId, $titre = null) {
        $titre = $titre ?? 'Nouvelle conversation - ' . date('d/m/Y H:i');
        
        $sql = "INSERT INTO conversations (utilisateur_id, titre) VALUES (:user_id, :titre)";
        $this->db->execute($sql, [':user_id' => $userId, ':titre' => $titre]);
        
        return $this->db->lastInsertId();
    }

    public function addMessage($conversationId, $userId, $typeMessage, $contenu, $metadata = null) {
        $sql = "INSERT INTO messages (conversation_id, utilisateur_id, type_message, contenu, metadata) 
                VALUES (:conv_id, :user_id, :type, :contenu, :metadata)";
        
        $params = [
            ':conv_id' => $conversationId,
            ':user_id' => $userId,
            ':type' => $typeMessage,
            ':contenu' => $contenu,
            ':metadata' => $metadata ? json_encode($metadata) : null
        ];

        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function getConversationMessages($conversationId, $limit = 50) {
        $sql = "SELECT m.*, u.nom_complet, u.type_utilisateur 
                FROM messages m 
                LEFT JOIN utilisateurs u ON m.utilisateur_id = u.id 
                WHERE m.conversation_id = :conv_id 
                ORDER BY m.date_envoi ASC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [':conv_id' => $conversationId, ':limit' => $limit]);
    }

    public function getUserConversations($userId, $limit = 20) {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id) as nb_messages,
                (SELECT contenu FROM messages WHERE conversation_id = c.id ORDER BY date_envoi DESC LIMIT 1) as dernier_message
                FROM conversations c 
                WHERE c.utilisateur_id = :user_id AND c.statut = 'active'
                ORDER BY c.date_modification DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [':user_id' => $userId, ':limit' => $limit]);
    }

    public function generateBotResponse($message) {
        // Ici vous pouvez intégrer votre logique IA/NLP
        // Pour l'instant, on utilise la même logique que dans le HTML
        
        $message = strtolower($message);
        $responses = $this->getResponseFromKeywords($message);
        
        if (!empty($responses)) {
            return $responses[array_rand($responses)];
        }

        return "Je ne suis pas sûr de bien comprendre votre question. Pourriez-vous me donner plus de détails ?";
    }

    private function getResponseFromKeywords($message) {
        $knowledgeBase = [
            'programmes' => [
                'keywords' => ['programme', 'formation', 'cours', 'bachelor', 'master', 'mba', 'bba', 'filière'],
                'responses' => [
                    "🎓 **Nos programmes disponibles :**\n\n• **Bachelor Informatique** (3 ans)\n• **Bachelor Data Science** (3 ans)\n• **Bachelor Droit** (3 ans)\n• **BBA - Business Administration** (3 ans)\n• **MBA** (2 ans)\n\nSouhaitez-vous plus de détails sur un programme spécifique ?"
                ]
            ],
            'tarifs' => [
                'keywords' => ['prix', 'coût', 'tarif', 'frais', 'scolarité', 'paiement'],
                'responses' => [
                    "💰 **Structure tarifaire :**\n\n• **Bachelor :** 150 000 FCFA/mois\n• **BBA :** 175 000 FCFA/mois\n• **MBA :** 250 000 FCFA/mois\n\n✅ Possibilité de paiement échelonné\n✅ Bourses d'excellence disponibles"
                ]
            ]
            // Ajoutez d'autres catégories...
        ];

        foreach ($knowledgeBase as $category => $data) {
            foreach ($data['keywords'] as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    return $data['responses'];
                }
            }
        }

        return [];
    }
}

// =======================================================
// CLASSES/PROGRAMME.PHP - Gestion des programmes
// =======================================================

class Programme {
    private $db;
    
    public function __construct(Database $database) {
        $this->db = $database;
    }

    public function getAllProgrammes() {
        $sql = "SELECT * FROM programmes WHERE statut = 'actif' ORDER BY niveau, nom";
        return $this->db->fetchAll($sql);
    }

    public function getProgrammeById($id) {
        $sql = "SELECT * FROM programmes WHERE id = :id";
        return $this->db->fetch($sql, [':id' => $id]);
    }

    public function searchProgrammes($query) {
        $sql = "SELECT * FROM programmes 
                WHERE (nom LIKE :query OR description LIKE :query OR domaine LIKE :query) 
                AND statut = 'actif'
                ORDER BY nom";
        
        return $this->db->fetchAll($sql, [':query' => "%$query%"]);
    }
}

// =======================================================
// CLASSES/NOTIFICATION.PHP - Gestion des notifications
// =======================================================  
