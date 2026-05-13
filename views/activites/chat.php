<?php
// views/activites/chat.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant IA - Recommandation d'Activités Sportives</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .chat-container {
            width: 100%;
            max-width: 600px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            height: 80vh;
            max-height: 700px;
        }

        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chat-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .chat-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            animation: slideIn 0.3s ease-in-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.user {
            justify-content: flex-end;
        }

        .message.bot {
            justify-content: flex-start;
        }

        .message-content {
            max-width: 70%;
            padding: 12px 15px;
            border-radius: 12px;
            word-wrap: break-word;
            line-height: 1.5;
        }

        .user .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 2px;
        }

        .bot .message-content {
            background: #e9ecef;
            color: #333;
            border-bottom-left-radius: 2px;
        }

        .recommendation {
            background: white;
            border-left: 4px solid #667eea;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .recommendation-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .recommendation-desc {
            font-size: 13px;
            color: #666;
        }

        .input-container {
            padding: 20px;
            background: white;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 10px;
            border-radius: 0 0 15px 15px;
        }

        .input-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 25px;
            padding: 5px 15px;
            border: 2px solid #e0e0e0;
            transition: border-color 0.3s;
        }

        .input-wrapper:focus-within {
            border-color: #667eea;
        }

        .input-wrapper input {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            font-size: 14px;
            padding: 10px 5px;
        }

        .send-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, box-shadow 0.2s;
            font-size: 18px;
        }

        .send-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .send-btn:active {
            transform: scale(0.95);
        }

        .loading {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dot {
            width: 8px;
            height: 8px;
            background: #667eea;
            border-radius: 50%;
            animation: bounce 1.4s infinite;
        }

        .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {
            0%, 80%, 100% {
                opacity: 0.3;
                transform: translateY(0);
            }
            40% {
                opacity: 1;
                transform: translateY(-10px);
            }
        }

        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #764ba2;
        }

        .scroll-down {
            text-align: center;
            color: #999;
            font-size: 12px;
            padding: 10px;
        }

        @media (max-width: 600px) {
            .chat-container {
                height: 100vh;
                max-height: 100vh;
                border-radius: 0;
            }

            .message-content {
                max-width: 85%;
            }

            .back-link {
                display: none;
            }
        }
    </style>
</head>
<body>
    <a href="index.php?c=activite&action=index" class="back-link">← Retour</a>
    
    <div class="chat-container">
        <div class="chat-header">
            <h1>🎯 Assistant IA</h1>
            <p>Trouvez l'activité sportive parfaite pour vous !</p>
        </div>

        <div class="messages-container" id="messagesContainer">
            <div class="message bot">
                <div class="message-content">
                    Bonjour ! 👋 Je suis votre assistant IA spécialisé dans les recommandations d'activités sportives. 
                    <br><br>
                    Décrivez-moi votre objectif ou votre problème (par exemple : "Je veux perdre du poids", "Je cherche à développer ma musculature", "Je suis stressé et j'ai besoin de me détendre"), 
                    et je vous recommanderai les activités les plus adaptées !
                </div>
            </div>
        </div>

        <div class="input-container">
            <div class="input-wrapper">
                <input 
                    type="text" 
                    id="userInput" 
                    placeholder="Décrivez votre besoin..." 
                    autocomplete="off"
                >
            </div>
            <button class="send-btn" id="sendBtn" title="Envoyer">
                ➤
            </button>
        </div>
    </div>

    <script>
        const messagesContainer = document.getElementById('messagesContainer');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');

        // Fonction pour ajouter un message au chat
        function addMessage(text, isUser = false, isLoading = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user' : 'bot'}`;

            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';

            if (isLoading) {
                contentDiv.innerHTML = `
                    <div class="loading">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                `;
            } else {
                contentDiv.innerHTML = text;
            }

            messageDiv.appendChild(contentDiv);
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            return messageDiv;
        }

        // Fonction pour envoyer un message
        async function sendMessage() {
            const message = userInput.value.trim();

            if (!message) return;

            // Ajouter le message utilisateur
            addMessage(message, true);
            userInput.value = '';

            // Ajouter l'indicateur de chargement
            const loadingMsg = addMessage('', false, true);

            try {
                // Envoyer la requête au serveur
                const response = await fetch('index.php?c=activite&action=chatbot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message
                    })
                });

                const data = await response.json();

                // Retirer le message de chargement
                loadingMsg.remove();

                if (data.success) {
                    // Créer la réponse du bot
                    let botResponse = '';

                    if (data.recommendations.length > 0) {
                        botResponse += '<div style="margin-bottom: 10px;"><strong>✅ Voici mes recommandations :</strong></div>';
                        
                        data.recommendations.forEach((activity, index) => {
                            botResponse += `
                                <div class="recommendation">
                                    <div class="recommendation-title">${index + 1}. ${activity.nom}</div>
                                    <div class="recommendation-desc">${activity.description || 'Activité recommandée'}</div>
                                </div>
                            `;
                        });
                    } else {
                        botResponse = '😕 Je n\'ai pas trouvé d\'activité correspondant à votre description. Pouvez-vous me donner plus de détails ?';
                    }

                    addMessage(botResponse, false);
                } else {
                    addMessage('❌ Une erreur s\'est produite. Veuillez réessayer.', false);
                }
            } catch (error) {
                loadingMsg.remove();
                addMessage('❌ Erreur de connexion au serveur.', false);
                console.error('Erreur:', error);
            }

            userInput.focus();
        }

        // Événements
        sendBtn.addEventListener('click', sendMessage);
        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Focus initial
        userInput.focus();
    </script>
</body>
</html>
