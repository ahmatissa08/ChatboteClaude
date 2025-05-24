<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot IAM - Assistant Intelligent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --border-color: #e5e7eb;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] {
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --border-color: #374151;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .nav-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .theme-toggle, .auth-btn {
            background: none;
            border: 2px solid var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--primary-color);
            font-weight: 500;
        }

        .theme-toggle:hover, .auth-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        /* Main Container */
        .main-container {
            margin-top: 80px;
            display: flex;
            height: calc(100vh - 80px);
            gap: 1rem;
            padding: 1rem;
        }

        /* Sidebar */
        .sidebar {
            width: 300px;
            background: var(--bg-primary);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 80px;
            padding: 1rem;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .sidebar-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .collapse-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .collapse-btn:hover {
            background: var(--bg-secondary);
            color: var(--primary-color);
        }

        /* Quick Actions */
        .quick-actions {
            margin-bottom: 2rem;
        }

        .action-btn {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Chat History */
        .chat-history {
            flex: 1;
            overflow-y: auto;
        }

        .history-item {
            padding: 0.75rem;
            background: var(--bg-secondary);
            border-radius: 12px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .history-item:hover {
            background: var(--primary-color);
            color: white;
            transform: translateX(5px);
        }

        .history-item.active {
            border-left-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
        }

        /* Chat Container */
        .chat-container {
            flex: 1;
            background: var(--bg-primary);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Chat Header */
        .chat-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .bot-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .bot-info h3 {
            margin-bottom: 0.25rem;
        }

        .bot-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0.9;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            background: var(--success-color);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Messages Area */
        .messages-area {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background: var(--bg-secondary);
        }

        .message {
            display: flex;
            margin-bottom: 1.5rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.user {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 70%;
            padding: 1rem 1.5rem;
            border-radius: 20px;
            position: relative;
            word-wrap: break-word;
        }

        .message.bot .message-bubble {
            background: white;
            color: var(--text-primary);
            box-shadow: var(--shadow);
            border-bottom-left-radius: 5px;
        }

        .message.user .message-bubble {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.5rem;
        }

        /* Typing Indicator */
        .typing-indicator {
            display: none;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 20px;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
        }

        .typing-dots {
            display: flex;
            gap: 0.25rem;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: var(--text-secondary);
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }

        /* Quick Suggestions */
        .suggestions {
            display: flex;
            gap: 0.5rem;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .suggestion-chip {
            padding: 0.5rem 1rem;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .suggestion-chip:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Input Area */
        .input-area {
            padding: 1.5rem;
            background: var(--bg-primary);
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .input-container {
            flex: 1;
            position: relative;
        }

        .message-input {
            width: 100%;
            padding: 1rem 1.5rem;
            border: 2px solid var(--border-color);
            border-radius: 50px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            background: var(--bg-secondary);
        }

        .message-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .send-btn, .attachment-btn {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .send-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .attachment-btn {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
        }

        .send-btn:hover, .attachment-btn:hover {
            transform: scale(1.1);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--bg-primary);
            padding: 2rem;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-secondary);
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--bg-secondary);
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* User Profile */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: var(--bg-secondary);
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .user-info h4 {
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .user-info p {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                padding: 0.5rem;
            }

            .sidebar {
                width: 100%;
                height: auto;
                order: 2;
            }

            .chat-container {
                order: 1;
                height: 70vh;
            }

            .navbar {
                padding: 1rem;
            }

            .suggestions {
                padding: 0 1rem;
            }

            .suggestion-chip {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            z-index: 3000;
            animation: slideInRight 0.3s ease;
        }

        .notification.success {
            background: var(--success-color);
        }

        .notification.error {
            background: var(--error-color);
        }

        .notification.warning {
            background: var(--warning-color);
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body data-theme="light">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            Chatbot IAM
        </div>
        <div class="nav-controls">
            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
            <button class="auth-btn" id="authBtn" onclick="showLoginModal()">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-title">
                    <i class="fas fa-history"></i>
                    <span class="sidebar-text">Historique</span>
                </div>
                <button class="collapse-btn" onclick="toggleSidebar()">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

            <!-- User Profile (when logged in) -->
            <div class="user-profile" id="userProfile" style="display: none;">
                <div class="user-avatar" id="userAvatar">U</div>
                <div class="user-info">
                    <h4 id="userName">Utilisateur</h4>
                    <p id="userType">Étudiant</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="action-btn" onclick="startNewChat()">
                    <i class="fas fa-plus"></i>
                    <span class="sidebar-text">Nouveau chat</span>
                </button>
                <button class="action-btn" onclick="showInscriptionModal()">
                    <i class="fas fa-user-plus"></i>
                    <span class="sidebar-text">Inscription</span>
                </button>
                <button class="action-btn" onclick="showCalendarModal()">
                    <i class="fas fa-calendar"></i>
                    <span class="sidebar-text">Calendrier</span>
                </button>
            </div>

            <!-- Chat History -->
            <div class="chat-history" id="chatHistory">
                <div class="history-item">
                    <i class="fas fa-comment"></i>
                    <span class="sidebar-text">Conversation d'exemple</span>
                </div>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="chat-container">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="bot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="bot-info">
                    <h3>Coumba - Assistant IAM</h3>
                    <div class="bot-status">
                        <div class="status-indicator"></div>
                        <span>En ligne</span>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="messages-area" id="messagesArea">
                <div class="message bot">
                    <div class="message-bubble">
                        <div>Bonjour ! Je suis Coumba, votre assistant virtuel de l'IAM. Comment puis-je vous aider aujourd'hui ?</div>
                        <div class="message-time">Maintenant</div>
                    </div>
                </div>

                <div class="typing-indicator" id="typingIndicator">
                    <div class="typing-dots">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                    <span>Coumba écrit...</span>
                </div>
            </div>

            <!-- Quick Suggestions -->
            <div class="suggestions">
                <div class="suggestion-chip" onclick="sendQuickMessage('Quels sont les programmes disponibles ?')">
                    <i class="fas fa-graduation-cap"></i>
                    Programmes
                </div>
                <div class="suggestion-chip" onclick="sendQuickMessage('Comment s\'inscrire ?')">
                    <i class="fas fa-user-plus"></i>
                    Inscription
                </div>
                <div class="suggestion-chip" onclick="sendQuickMessage('Quels sont les tarifs ?')">
                    <i class="fas fa-money-bill"></i>
                    Tarifs
                </div>
                <div class="suggestion-chip" onclick="sendQuickMessage('Horaires d\'ouverture')">
                    <i class="fas fa-clock"></i>
                    Horaires
                </div>
            </div>

            <!-- Input Area -->
            <div class="input-area">
                <button class="attachment-btn" title="Joindre un fichier">
                    <i class="fas fa-paperclip"></i>
                </button>
                <div class="input-container">
                    <input type="text" class="message-input" id="messageInput" placeholder="Tapez votre message..." />
                </div>
                <button class="send-btn" onclick="sendMessage()" title="Envoyer">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal" id="loginModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Connexion</h2>
                <button class="close-btn" onclick="closeModal('loginModal')">&times;</button>
            </div>
            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" id="email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" class="form-input" id="password" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type d'utilisateur</label>
                    <select class="form-select" id="userType">
                        <option value="etudiant">Étudiant</option>
                        <option value="prospect">Prospect</option>
                        <option value="parent">Parent</option>
                        <option value="enseignant">Enseignant</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Se connecter</button>
            </form>
        </div>
    </div>

    <!-- Registration Modal -->
    <div class="modal" id="inscriptionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Demande d'inscription</h2>
                <button class="close-btn" onclick="closeModal('inscriptionModal')">&times;</button>
            </div>
            <form id="inscriptionForm">
                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Programme souhaité</label>
                    <select class="form-select" required>
                        <option value="">Choisir un programme</option>
                        <option value="bachelor-info">Bachelor Informatique</option>
                        <option value="bachelor-data">Bachelor Data Science</option>
                        <option value="bachelor-droit">Bachelor Droit</option>
                        <option value="bba">BBA</option>
                        <option value="mba">MBA</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Envoyer la demande</button>
            </form>
        </div>
    </div>

    <!-- Calendar Modal -->
    <div class="modal" id="calendarModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Calendrier académique</h2>
                <button class="close-btn" onclick="closeModal('calendarModal')">&times;</button>
            </div>
            <div id="calendarContent">
                <h3>Événements à venir</h3>
                <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px; margin: 1rem 0;">
                    <h4><i class="fas fa-calendar-day"></i> Rentrée académique</h4>
                    <p>15 Septembre 2024 - Accueil des nouveaux étudiants</p>
                </div>
                <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px; margin: 1rem 0;">
                    <h4><i class="fas fa-door-open"></i> Journée Portes Ouvertes</h4>
                    <p>30 Août 2024 - Découvrez nos programmes</p>
                </div>
                <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px; margin: 1rem 0;">
                    <h4><i class="fas fa-graduation-cap"></i> Session d'examens</h4>
                    <p>15-25 Décembre 2024 - Examens du premier semestre</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let currentUser = null;
        let chatHistory = [];
        let currentChatId = null;
        let isTyping = false;

        // Système de réponses avancé
        const knowledgeBase = {
            programmes: {
                keywords: ['programme', 'formation', 'cours', 'bachelor', 'master', 'mba', 'bba', 'filière'],
                responses: [
                    "🎓 **Nos programmes disponibles :**\n\n• **Bachelor Informatique** (3 ans)\n• **Bachelor Data Science** (3 ans)\n• **Bachelor Droit** (3 ans)\n• **BBA - Business Administration** (3 ans)\n• **MBA** (2 ans)\n\nSouhaitez-vous plus de détails sur un programme spécifique ?",
                    "Voici un aperçu de nos formations :\n\n**Programmes Techniques :**\n- Informatique & Développement\n- Data Science & IA\n- Cybersécurité\n\n**Programmes Business :**\n- BBA International\n- MBA Executive\n- Management & Leadership\n\nQuel domaine vous intéresse le plus ?"
                ]
            },
            tarifs: {
                keywords: ['prix', 'coût', 'tarif', 'frais', 'scolarité', 'paiement'],
                responses: [
                    "💰 **Structure tarifaire :**\n\n• **Bachelor :** 150 000 FCFA/mois\n• **BBA :** 175 000 FCFA/mois\n• **MBA :** 250 000 FCFA/mois\n\n✅ Possibilité de paiement échelonné\n✅ Bourses d'excellence disponibles\n\nContactez-nous pour un devis personnalisé !",
                    "Nos tarifs sont compétitifs :\n\n**Frais d'inscription :** 50 000 FCFA\n**Mensualités :** Dès 150 000 FCFA\n\n🎯 **Avantages :**\n- Facilités de paiement\n- Bourses au mérite\n- Réductions famille nombreuse\n\nVoulez-vous simuler votre parcours ?"
                ]
            },
            inscription: {
                keywords: ['inscription', 'inscrire', 'admission', 'candidature', 'dossier'],
                responses: [
                    "📝 **Processus d'inscription :**\n\n1. **Dossier de candidature**\n   - Formulaire en ligne\n   - CV et lettre de motivation\n   - Relevés de notes\n\n2. **Entretien de sélection**\n   - Évaluation du projet professionnel\n   - Test de niveau (si nécessaire)\n\n3. **Confirmation d'admission**\n   - Validation du dossier\n   - Paiement des frais d'inscription\n\n📅 **Dates importantes :** Inscriptions ouvertes jusqu'au 30 août !",
                    "Pour vous inscrire chez nous :\n\n✅ **Étape 1 :** Remplir le formulaire de pré-inscription\n✅ **Étape 2 :** Constituer votre dossier complet\n✅ **Étape 3 :** Passer l'entretien d'admission\n✅ **Étape 4 :** Finaliser votre inscription\n\n🚀 Le processus prend généralement 2-3 semaines. Voulez-vous que je vous aide à démarrer ?"
                ]
            },
            horaires: {
                keywords: ['horaire', 'ouverture', 'fermeture', 'heures', 'accueil', 'contact'],
                responses: [
                    "🕐 **Horaires d'ouverture :**\n\n**Lundi - Vendredi :** 8h00 - 18h00\n**Samedi :** 8h00 - 14h00\n**Dimanche :** Fermé\n\n📍 **Accueil étudiant :**\n- Service scolarité : 8h30 - 17h30\n- Bibliothèque : 8h00 - 20h00\n- Cafétéria : 7h30 - 18h30\n\n📞 **Contact d'urgence :** +221 XX XXX XX XX",
                    "Nous sommes ouverts :\n\n⏰ **Du lundi au vendredi :** 8h - 18h\n⏰ **Samedi matin :** 8h - 14h\n\n🏢 **Services disponibles :**\n- Accueil et information\n- Inscriptions et réinscriptions\n- Consultation académique\n- Support technique\n\nBesoin d'un rendez-vous particulier ?"
                ]
            },
            campus: {
                keywords: ['campus', 'locaux', 'adresse', 'localisation', 'transport', 'accès'],
                responses: [
                    "🏫 **Notre campus :**\n\n📍 **Adresse :** Dakar, Sénégal\n🚌 **Accès :** Bus, taxi, transport privé\n🅿️ **Parking :** Disponible sur site\n\n🏢 **Infrastructures :**\n- Salles de cours modernes\n- Laboratoires informatiques\n- Bibliothèque numérique\n- Espaces de coworking\n- Cafétéria\n- Salle de sport\n\nVoulez-vous planifier une visite ?",
                    "Découvrez notre campus moderne :\n\n🌟 **Équipements de pointe :**\n- 50+ salles de cours climatisées\n- Labs tech dernière génération\n- WiFi haut débit partout\n- Espaces détente\n\n🚗 **Facilités d'accès :**\n- Proche des transports publics\n- Parking sécurisé 200 places\n- Navettes étudiantes\n\nJe peux vous organiser une visite guidée !"
                ]
            },
            aide: {
                keywords: ['aide', 'help', 'assistance', 'support', 'problème', 'question'],
                responses: [
                    "🤝 **Je suis là pour vous aider !**\n\nJe peux vous renseigner sur :\n\n📚 **Académique :**\n- Programmes et formations\n- Processus d'inscription\n- Calendrier académique\n\n💰 **Financier :**\n- Tarifs et frais\n- Modalités de paiement\n- Bourses disponibles\n\n🏢 **Pratique :**\n- Horaires et contact\n- Localisation campus\n- Services étudiants\n\nQue souhaitez-vous savoir ?",
                    "Je suis Coumba, votre guide personnalisé ! 🌟\n\n**Mes spécialités :**\n✨ Orientation académique\n✨ Démarches administratives\n✨ Vie étudiante\n✨ Support technique\n\n**Comment puis-je vous accompagner aujourd'hui ?**\n\nN'hésitez pas à me poser toutes vos questions, même les plus spécifiques !"
                ]
            }
        };

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeChat();
            setupEventListeners();
            loadUserSession();
        });

        function initializeChat() {
            const messagesArea = document.getElementById('messagesArea');
            const welcomeMessage = createBotMessage(
                "Bonjour ! Je suis Coumba, votre assistant virtuel de l'IAM. Comment puis-je vous aider aujourd'hui ?",
                new Date()
            );
            messagesArea.appendChild(welcomeMessage);
        }

        function setupEventListeners() {
            const messageInput = document.getElementById('messageInput');
            const themeToggle = document.getElementById('themeToggle');
            
            // Envoi de message avec Enter
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });

            // Toggle thème
            themeToggle.addEventListener('click', toggleTheme);

            // Forms
            document.getElementById('loginForm').addEventListener('submit', handleLogin);
            document.getElementById('inscriptionForm').addEventListener('submit', handleInscription);
        }

        // Gestion des messages
        function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (!message) return;

            // Ajouter message utilisateur
            addUserMessage(message);
            input.value = '';

            // Montrer indicateur de frappe
            showTypingIndicator();

            // Simuler délai de réponse
            setTimeout(() => {
                const response = generateBotResponse(message);
                hideTypingIndicator();
                addBotMessage(response);
            }, 1500 + Math.random() * 1000);
        }

        function sendQuickMessage(message) {
            const input = document.getElementById('messageInput');
            input.value = message;
            sendMessage();
        }

        function addUserMessage(message) {
            const messagesArea = document.getElementById('messagesArea');
            const messageElement = createUserMessage(message, new Date());
            messagesArea.appendChild(messageElement);
            scrollToBottom();
        }

        function addBotMessage(message) {
            const messagesArea = document.getElementById('messagesArea');
            const messageElement = createBotMessage(message, new Date());
            messagesArea.appendChild(messageElement);
            scrollToBottom();
        }

        function createUserMessage(content, time) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message user';
            messageDiv.innerHTML = `
                <div class="message-bubble">
                    <div>${escapeHtml(content)}</div>
                    <div class="message-time">${formatTime(time)}</div>
                </div>
            `;
            return messageDiv;
        }

        function createBotMessage(content, time) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message bot';
            messageDiv.innerHTML = `
                <div class="message-bubble">
                    <div>${formatBotResponse(content)}</div>
                    <div class="message-time">${formatTime(time)}</div>
                </div>
            `;
            return messageDiv;
        }

        function formatBotResponse(content) {
            // Convertir markdown basique en HTML
            return content
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/\n/g, '<br>')
                .replace(/• /g, '• ')
                .replace(/✅/g, '<span style="color: var(--success-color);">✅</span>')
                .replace(/🎓|📝|💰|🏫|🤝|🌟/g, '<span style="font-size: 1.2em;">$&</span>');
        }

        // IA de réponse avancée
        function generateBotResponse(userMessage) {
            const normalizedMessage = userMessage.toLowerCase();
            
            // Vérifier chaque catégorie de la base de connaissances
            for (const [category, data] of Object.entries(knowledgeBase)) {
                for (const keyword of data.keywords) {
                    if (normalizedMessage.includes(keyword)) {
                        const responses = data.responses;
                        return responses[Math.floor(Math.random() * responses.length)];
                    }
                }
            }

            // Réponses contextuelles intelligentes
            if (normalizedMessage.includes('merci')) {
                return "De rien ! Je suis ravi de pouvoir vous aider. Y a-t-il autre chose que vous souhaiteriez savoir sur l'IAM ? 😊";
            }

            if (normalizedMessage.includes('salut') || normalizedMessage.includes('bonjour') || normalizedMessage.includes('hello')) {
                return "Bonjour ! Content de vous retrouver ! Comment puis-je vous accompagner dans votre projet académique aujourd'hui ? 🌟";
            }

            if (normalizedMessage.includes('au revoir') || normalizedMessage.includes('bye')) {
                return "À bientôt ! N'hésitez pas à revenir me voir si vous avez d'autres questions. Bonne journée ! 👋";
            }

            // Détection d'émotion/sentiment
            if (normalizedMessage.includes('confus') || normalizedMessage.includes('perdu') || normalizedMessage.includes('comprend pas')) {
                return "Je comprends que cela puisse être déroutant. Laissez-moi vous expliquer plus clairement. Quel aspect spécifique vous pose problème ? Je vais simplifier mes explications. 🤝";
            }

            if (normalizedMessage.includes('génial') || normalizedMessage.includes('super') || normalizedMessage.includes('parfait')) {
                return "Fantastique ! Je suis ravi que ces informations vous soient utiles. Continuons à avancer ensemble dans votre projet ! Y a-t-il autre chose que vous aimeriez explorer ? ✨";
            }


            // Réponse par défaut intelligente avec suggestions
            const suggestions = [
                "Je ne suis pas sûr de bien comprendre votre question. Pourriez-vous me donner plus de détails ?",
                "Hmm, je n'ai pas trouvé d'information précise sur ce sujet. Puis-je vous orienter vers un autre domaine ?",
                "Cette question sort un peu de mes compétences actuelles. Laissez-moi vous proposer des alternatives.",
                "Je veux m'assurer de bien vous renseigner. Pouvez-vous reformuler votre question ?",
                "Je n'ai pas d'information spécifique sur ce sujet. Peut-être que je peux vous aider avec autre chose ?",
                "Je ne suis pas certain de comprendre. Pourriez-vous préciser votre question ?",
            ];

            const baseSuggestion = suggestions[Math.floor(Math.random() * suggestions.length)];
            
            return `${baseSuggestion}\n\n💡 **Suggestions de sujets :**\n• Programmes et formations\n• Processus d'inscription\n• Tarifs et financements\n• Campus et services\n• Calendrier académique\n\nQue souhaitez-vous explorer ?`;
        }

        // Indicateurs visuels
        function showTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            indicator.style.display = 'flex';
            scrollToBottom();
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            indicator.style.display = 'none';
        }

        function scrollToBottom() {
            const messagesArea = document.getElementById('messagesArea');
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        // Gestion des modals
        function showLoginModal() {
            document.getElementById('loginModal').style.display = 'flex';
        }

        function showInscriptionModal() {
            document.getElementById('inscriptionModal').style.display = 'flex';
        }

        function showCalendarModal() {
            document.getElementById('calendarModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Gestion des formulaires
        function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const userType = document.getElementById('userType').value;

            // Simulation de connexion
            currentUser = {
                email: email,
                type: userType,
                name: email.split('@')[0]
            };

            updateUserInterface();
            closeModal('loginModal');
            showNotification('Connexion réussie !', 'success');
            
            // Message de bienvenue personnalisé
            setTimeout(() => {
                addBotMessage(`Bienvenue ${currentUser.name} ! Je vois que vous êtes ${userType}. Comment puis-je personnaliser mon assistance pour vous aujourd'hui ? 🎯`);
            }, 1000);
        }

        function handleInscription(e) {
            e.preventDefault();
            showNotification('Demande d\'inscription envoyée avec succès !', 'success');
            closeModal('inscriptionModal');
            
            setTimeout(() => {
                addBotMessage("Parfait ! Votre demande d'inscription a été transmise à notre équipe. Vous recevrez une réponse sous 48h. En attendant, puis-je répondre à d'autres questions ? 📧");
            }, 1000);
        }

        // Interface utilisateur
        function updateUserInterface() {
            if (currentUser) {
                document.getElementById('userProfile').style.display = 'flex';
                document.getElementById('userName').textContent = currentUser.name;
                document.getElementById('userType').textContent = currentUser.type;
                document.getElementById('userAvatar').textContent = currentUser.name.charAt(0).toUpperCase();
                document.getElementById('authBtn').innerHTML = '<i class="fas fa-sign-out-alt"></i> Déconnexion';
                document.getElementById('authBtn').onclick = logout;
            }
        }

        function logout() {
            currentUser = null;
            document.getElementById('userProfile').style.display = 'none';
            document.getElementById('authBtn').innerHTML = '<i class="fas fa-sign-in-alt"></i> Se connecter';
            document.getElementById('authBtn').onclick = showLoginModal;
            showNotification('Déconnexion réussie', 'success');
        }

        // Gestion du thème
        function toggleTheme() {
            const body = document.body;
            const themeToggle = document.getElementById('themeToggle');
            
            if (body.getAttribute('data-theme') === 'dark') {
                body.setAttribute('data-theme', 'light');
                themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
            } else {
                body.setAttribute('data-theme', 'dark');
                themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            }
        }

        // Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }

        function startNewChat() {
            const messagesArea = document.getElementById('messagesArea');
            messagesArea.innerHTML = '';
            
            const welcomeMessage = createBotMessage(
                "Nouvelle conversation démarrée ! Comment puis-je vous aider ? 🚀",
                new Date()
            );
            messagesArea.appendChild(welcomeMessage);
            
            showNotification('Nouvelle conversation démarrée', 'success');
        }

        // Notifications
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Utilitaires
        function formatTime(date) {
            return date.toLocaleTimeString('fr-FR', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function loadUserSession() {
            // Simulation de récupération de session
            const savedTheme = 'light'; // localStorage.getItem('theme') || 'light';
            document.body.setAttribute('data-theme', savedTheme);
        }

        // Fermeture des modals en cliquant à l'extérieur
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        };

        // Gestion des fichiers joints (placeholder)
        document.querySelector('.attachment-btn').addEventListener('click', function() {
            showNotification('Fonctionnalité de pièces jointes bientôt disponible !', 'warning');
        });
    </script>
</body>
</html>