// chatbot.js - Système de chatbot IA
class ChatbotSystem {
    constructor(config) {
        this.config = config || {
            apiUrl: '/api/chatbot',
            maxQuestionsPerSession: 5,
            language: 'fr',
            guidePersonality: 'friendly_guide'
        };
        
        this.conversation = [];
        this.questionsAsked = 0;
        this.isTyping = false;
        this.sessionId = this.generateSessionId();
        
        this.init();
    }
    
    init() {
        this.loadConversationHistory();
        this.setupEventListeners();
        this.showWelcomeMessage();
    }
    
    generateSessionId() {
        return 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    showWelcomeMessage() {
        const welcomeMessages = {
            'fr': [
                "Salut ! Je suis ton guide IA. Pose-moi toutes tes questions sur ce stage !",
                "Bonjour ! Prêt à apprendre de nouvelles choses ? Je suis là pour t'aider !",
                "Hey ! Moi c'est Téo, ton guide virtuel. Demande-moi ce que tu veux savoir !"
            ],
            'ar': [
                "مرحبا! أنا مرشدك الذكي. اسألني كل أسئلتك حول هذه المرحلة!",
                "أهلا! مستعد لتعلم أشياء جديدة؟ أنا هنا لمساعدتك!",
                "مرحبا! أنا تيو، مرشدك الافتراضي. اسألني ما تريد معرفته!"
            ]
        };
        
        const messages = welcomeMessages[this.config.language] || welcomeMessages.fr;
        const randomMessage = messages[Math.floor(Math.random() * messages.length)];
        
        this.addBotMessage(randomMessage);
    }
    
    async sendMessage(message) {
        if (!message.trim()) return;
        
        // Vérifier la limite de questions
        if (this.questionsAsked >= this.config.maxQuestionsPerSession) {
            this.showLimitReachedMessage();
            return;
        }
        
        // Ajouter le message de l'utilisateur
        this.addUserMessage(message);
        this.questionsAsked++;
        
        // Afficher l'indicateur de frappe
        this.showTypingIndicator();
        
        try {
            // Préparer le contexte
            const context = this.getCurrentContext();
            
            // Envoyer à l'API
            const response = await this.callChatbotAPI(message, context);
            
            // Retirer l'indicateur de frappe
            this.removeTypingIndicator();
            
            // Ajouter la réponse du bot
            this.addBotMessage(response.answer);
            
            // Mettre à jour le compteur
            this.updateQuestionsCounter();
            
            // Sauvegarder la conversation
            this.saveConversation();
            
        } catch (error) {
            console.error('Erreur chatbot:', error);
            this.removeTypingIndicator();
            this.addBotMessage(this.getFallbackResponse());
        }
    }
    
    async callChatbotAPI(message, context) {
        const userData = JSON.parse(localStorage.getItem('user_data') || '{}');
        const userId = userData.id || 1;
        const requestData = {
            question: message,
            user_id: userId,
            session_id: this.sessionId,
            context: {
                stage: context.stage,
                station: context.station,
                topic: context.topic,
                stage_key: context.stage_key,
                stage_title: context.stage_title,
                language: this.config.language
            },
            language: this.config.language,
            conversation_history: this.getRecentTurnsForAPI()
        };

        const response = await fetch(this.config.apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            // Fallback côté serveur (pas de token côté navigateur)
            return await this.callServerFallback(message, context);
            throw new Error('API error: ' + response.status);
        }

        const data = await response.json();

        // Adapter le format de réponse
        if (data.success && data.answer) {
            return { answer: data.answer, remaining_questions: data.remaining_questions };
        }

        throw new Error('Invalid response format');
    }

    async callServerFallback(message, context) {
        const fallbackUrl = (this.config.fallbackUrl || '').trim();
        if (!fallbackUrl) {
            return { answer: this.getFallbackResponse(), remaining_questions: 0 };
        }

        const response = await fetch(fallbackUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message,
                language: this.config.language,
                context: {
                    stage: context.stage,
                    station: context.station,
                    topic: context.topic,
                    stage_key: context.stage_key,
                    stage_title: context.stage_title,
                    language: this.config.language
                },
                history: this.getRecentTurnsForAPI()
            })
        });

        const data = await response.json().catch(() => ({}));
        if (data && data.success && data.message) {
            return { answer: data.message, remaining_questions: 0 };
        }
        return { answer: this.getFallbackResponse(), remaining_questions: 0 };
    }

    getRecentTurnsForAPI() {
        return this.conversation
            .slice(-8)
            .map(m => {
                if (m.sender === 'user') return { role: 'user', content: m.text };
                if (m.sender === 'bot') return { role: 'assistant', content: m.text };
                return null;
            })
            .filter(Boolean);
    }
    
    getCurrentContext() {
        const stageId = this.getCurrentStageId() || 1;
        const stageTitle = this.getCurrentTopic();
        const stageKey = this.getCurrentStageKey();
        return {
            stage: stageId,
            stage_key: stageKey,
            stage_title: stageTitle,
            station: this.getCurrentStationId() || 1,
            topic: stageTitle,
            previous_messages: this.conversation.slice(-5), // 5 derniers messages
            user_level: window.app?.user?.level || 1,
            learning_style: this.detectLearningStyle()
        };
    }
    
    detectLearningStyle() {
        // Analyser le style d'apprentissage basé sur les questions
        const userMessages = this.conversation.filter(msg => msg.sender === 'user');
        
        if (userMessages.length < 3) return 'balanced';
        
        const lastQuestions = userMessages.slice(-3).map(msg => msg.text.toLowerCase());
        
        let visualCount = 0;
        let auditoryCount = 0;
        let kinestheticCount = 0;
        
        const visualKeywords = ['voir', 'regarder', 'image', 'photo', 'couleur', 'dessin'];
        const auditoryKeywords = ['écouter', 'entendre', 'son', 'musique', 'parler', 'dire'];
        const kinestheticKeywords = ['faire', 'toucher', 'sentir', 'bouger', 'pratiquer', 'essayer'];
        
        lastQuestions.forEach(question => {
            if (visualKeywords.some(keyword => question.includes(keyword))) visualCount++;
            if (auditoryKeywords.some(keyword => question.includes(keyword))) auditoryCount++;
            if (kinestheticKeywords.some(keyword => question.includes(keyword))) kinestheticCount++;
        });
        
        if (visualCount > auditoryCount && visualCount > kinestheticCount) return 'visual';
        if (auditoryCount > visualCount && auditoryCount > kinestheticCount) return 'auditory';
        if (kinestheticCount > visualCount && kinestheticCount > auditoryCount) return 'kinesthetic';
        
        return 'balanced';
    }
    
    addUserMessage(text) {
        const message = {
            id: this.generateMessageId(),
            sender: 'user',
            text: text,
            timestamp: new Date().toISOString(),
            avatar: window.app?.user?.avatar || 'default-avatar.png'
        };
        
        this.conversation.push(message);
        this.displayMessage(message);
    }
    
    addBotMessage(text) {
        const message = {
            id: this.generateMessageId(),
            sender: 'bot',
            text: text,
            timestamp: new Date().toISOString(),
            avatar: 'assets/images/guides/bot-avatar.png'
        };
        
        this.conversation.push(message);
        this.displayMessage(message);
    }
    
    displayMessage(message) {
        const conversationElement = document.getElementById('chat-conversation');
        if (!conversationElement) return;
        
        const messageElement = this.createMessageElement(message);
        conversationElement.appendChild(messageElement);
        
        // Faire défiler vers le bas
        conversationElement.scrollTop = conversationElement.scrollHeight;
        
        // Animation
        setTimeout(() => {
            messageElement.classList.add('visible');
        }, 10);
    }
    
    createMessageElement(message) {
        const div = document.createElement('div');
        div.className = `chat-message ${message.sender}`;
        div.dataset.messageId = message.id;
        
        const avatar = message.sender === 'user' 
            ? (window.app?.user?.avatar || 'default-avatar.png')
            : 'assets/images/guides/bot-avatar.png';
        
        const time = new Date(message.timestamp).toLocaleTimeString([], { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        div.innerHTML = `
            <div class="message-avatar">
                <img src="${avatar}" alt="${message.sender === 'user' ? 'Vous' : 'Guide'}">
            </div>
            <div class="message-content">
                <div class="message-text">${this.formatMessageText(message.text)}</div>
                <div class="message-time">${time}</div>
            </div>
        `;
        
        return div;
    }
    
    formatMessageText(text) {
        // Formater les liens, emojis, etc.
        const escaped = String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        let formatted = escaped
            .replace(/\n/g, '<br>')
            .replace(/(https?:\/\/[^\s<]+)/g, '<a href="$1" target="_blank" rel="noreferrer noopener">$1</a>')
            .replace(/:\w+:/g, match => {
                const emoji = this.getEmoji(match);
                return emoji ? `<span class="emoji">${emoji}</span>` : match;
            });
        
        // Surligner les termes importants
        const highlightWords = this.getHighlightWords();
        highlightWords.forEach(word => {
            const regex = new RegExp(`\\b${word}\\b`, 'gi');
            formatted = formatted.replace(regex, `<span class="highlight">$&</span>`);
        });
        
        return formatted;
    }
    
    showTypingIndicator() {
        const conversationElement = document.getElementById('chat-conversation');
        if (!conversationElement) return;
        
        const typingElement = document.createElement('div');
        typingElement.className = 'chat-message bot typing';
        typingElement.id = 'typing-indicator';
        typingElement.innerHTML = `
            <div class="message-avatar">
                <img src="assets/images/guides/bot-avatar.png" alt="Guide">
            </div>
            <div class="message-content">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;
        
        conversationElement.appendChild(typingElement);
        conversationElement.scrollTop = conversationElement.scrollHeight;
    }
    
    removeTypingIndicator() {
        const typingElement = document.getElementById('typing-indicator');
        if (typingElement) {
            typingElement.remove();
        }
    }
    
    showLimitReachedMessage() {
        const messages = {
            'fr': "Tu as utilisé tes 5 questions gratuites ! Utilise un 💎 diamant pour poser 3 questions supplémentaires.",
            'ar': "لقد استخدمت أسئلتك الخمسة المجانية! استخدم 💎 ماسة لطرح 3 أسئلة إضافية."
        };
        
        const message = messages[this.config.language] || messages.fr;
        this.addBotMessage(message);
        
        // Afficher le bouton d'achat
        this.showPurchaseOption();
    }
    
    showPurchaseOption() {
        const purchaseDiv = document.createElement('div');
        purchaseDiv.className = 'purchase-option';
        purchaseDiv.innerHTML = `
            <div class="purchase-info">
                <span class="price">💎 1 diamant = 3 questions</span>
                <button class="btn-purchase" onclick="chatbot.purchaseQuestions()">
                    Acheter plus de questions
                </button>
            </div>
        `;
        
        const conversationElement = document.getElementById('chat-conversation');
        if (conversationElement) {
            conversationElement.appendChild(purchaseDiv);
        }
    }
    
    async purchaseQuestions() {
        if (!window.app?.user) {
            this.addBotMessage("Tu dois être connecté pour acheter des questions.");
            return;
        }
        
        if (window.app.user.diamonds < 1) {
            this.addBotMessage("Tu n'as pas assez de diamants ! Gagne-en en complétant des stages.");
            return;
        }
        
        try {
            const response = await fetch('/api/shop/purchase-chatbot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${window.app.user.token}`
                },
                body: JSON.stringify({
                    user_id: window.app.user.id,
                    item_type: 'chatbot_questions',
                    quantity: 3,
                    cost_diamonds: 1
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Mettre à jour le compteur local
                window.app.user.diamonds -= 1;
                this.config.maxQuestionsPerSession += 3;
                
                this.addBotMessage("Parfait ! Tu as maintenant " + this.config.maxQuestionsPerSession + " questions disponibles !");
                this.updateQuestionsCounter();
                
                // Mettre à jour l'affichage des diamants
                this.updateDiamondsDisplay();
            } else {
                this.addBotMessage("Oups ! L'achat a échoué. Réessaie plus tard.");
            }
            
        } catch (error) {
            console.error('Erreur achat questions:', error);
            this.addBotMessage("Erreur lors de l'achat. Vérifie ta connexion.");
        }
    }
    
    getFallbackResponse() {
        const fallbackResponses = {
            'fr': [
                "Je n'ai pas compris ta question. Peux-tu la reformuler ?",
                "Désolé, je ne peux pas répondre à ça pour le moment. Essaie une autre question !",
                "Hmm, intéressant ! Mais je ne suis pas sûr de la réponse. Demande-moi autre chose !"
            ],
            'ar': [
                "لم أفهم سؤالك. هل يمكنك إعادة صياغته؟",
                "عذرًا، لا يمكنني الإجابة على هذا الآن. جرب سؤالاً آخر!",
                "همم، مثير للاهتمام! لكنني لست متأكدًا من الإجابة. اسألني شيئًا آخر!"
            ]
        };
        
        const responses = fallbackResponses[this.config.language] || fallbackResponses.fr;
        return responses[Math.floor(Math.random() * responses.length)];
    }
    
    updateQuestionsCounter() {
        const counterElement = document.getElementById('questions-left');
        if (counterElement) {
            const remaining = this.config.maxQuestionsPerSession - this.questionsAsked;
            counterElement.textContent = `${remaining}/${this.config.maxQuestionsPerSession}`;
            
            // Changer la couleur selon le nombre restant
            if (remaining <= 1) {
                counterElement.classList.add('critical');
            } else if (remaining <= 2) {
                counterElement.classList.add('warning');
            } else {
                counterElement.classList.remove('warning', 'critical');
            }
        }
    }
    
    updateDiamondsDisplay() {
        const diamondsElement = document.getElementById('user-diamonds');
        if (diamondsElement && window.app?.user) {
            diamondsElement.textContent = window.app.user.diamonds;
        }
    }
    
    loadConversationHistory() {
        try {
            const saved = localStorage.getItem(`chat_session_${this.sessionId}`);
            if (saved) {
                this.conversation = JSON.parse(saved);
                this.questionsAsked = this.conversation.filter(msg => msg.sender === 'user').length;
                
                // Afficher l'historique
                this.displayConversationHistory();
            }
        } catch (error) {
            console.warn('Erreur chargement historique conversation:', error);
        }
    }
    
    saveConversation() {
        try {
            localStorage.setItem(`chat_session_${this.sessionId}`, JSON.stringify(this.conversation));
        } catch (error) {
            console.warn('Erreur sauvegarde conversation:', error);
        }
    }
    
    displayConversationHistory() {
        const conversationElement = document.getElementById('chat-conversation');
        if (!conversationElement) return;
        
        // Effacer le contenu actuel (sauf le message de bienvenue)
        conversationElement.innerHTML = '';
        
        // Afficher tous les messages
        this.conversation.forEach(message => {
            this.displayMessage(message);
        });
    }
    
    setupEventListeners() {
        // Entrée pour envoyer le message
        const inputElement = document.getElementById('chat-input');
        const sendButton = document.getElementById('btn-send');
        
        if (inputElement && sendButton) {
            inputElement.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage(inputElement.value);
                    inputElement.value = '';
                }
            });
            
            sendButton.addEventListener('click', () => {
                if (inputElement.value.trim()) {
                    this.sendMessage(inputElement.value);
                    inputElement.value = '';
                }
            });
        }
        
        // Suggestions
        document.querySelectorAll('.suggestion-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                const question = chip.textContent;
                this.sendMessage(question);
            });
        });
    }
    
    // Utilitaires
    generateMessageId() {
        return 'msg_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    getCurrentStageId() {
        // Récupérer depuis l'URL ou le localStorage
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('stage') || 
               localStorage.getItem('currentStage') || 
               1;
    }

    getCurrentStageKey() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('stage_key') || localStorage.getItem('currentStageKey') || '';
    }
    
    getCurrentStationId() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('station') || 1;
    }
    
    getCurrentTopic() {
        // Basé sur le stage actuel
        const stageId = this.getCurrentStageId();
        const topics = {
            1: 'المجال الجغرافي',
            2: 'المجال الجغرافي للمغرب العربي',
            3: 'المجال الجغرافي للبلاد التونسية',
            4: 'المشهد الريفي المحلي',
            5: 'المشهد الحضري',
            6: 'العلاقة بين الريف و المدينة',
            7: 'البلاد التونسية:التوزع الجغرافي للسكان',
            8: 'التوزّع الفلاحي للبلاد التونسية',
            9: 'الصناعة في البلاد التونسية',
            10: 'التجارة الخارجية التونسية'
        };
        
        return topics[stageId] || 'Général';
    }
    
    getHighlightWords() {
        const topic = this.getCurrentTopic();
        const highlightWords = {
            'Tunisie': ['Tunisie', 'Tunis', 'Carthage', 'désert', 'médina', 'couscous'],
            'Europe': ['Europe', 'France', 'Allemagne', 'Italie', 'Espagne', 'UE'],
            'Asie': ['Asie', 'Chine', 'Japon', 'Inde', 'dragon', 'sushi']
        };
        
        return highlightWords[topic] || [];
    }
    
    getEmoji(code) {
        const emojiMap = {
            ':smile:': '😊',
            ':thumbsup:': '👍',
            ':star:': '⭐',
            ':bulb:': '💡',
            ':book:': '📚',
            ':globe:': '🌍',
            ':trophy:': '🏆',
            ':diamond:': '💎',
            ':coin:': '🪙'
        };
        
        return emojiMap[code] || code;
    }
}

// Initialisation globale
let chatbot = null;

document.addEventListener('DOMContentLoaded', () => {
    // Injecter le widget si non présent
    injectChatbotWidget();
    if (document.getElementById('chat-conversation')) {
        const basePath = getAppBasePath();
        chatbot = new ChatbotSystem({
            language: (JSON.parse(localStorage.getItem('user_data')||'{}').language) || 'fr',
            maxQuestionsPerSession: 5,
            apiUrl: basePath + 'php/api/chatbot.php',
            fallbackUrl: basePath + 'php/hf-api.php'
        });
        window.chatbot = chatbot;
    }
});

function getAppBasePath() {
    const path = String(window.location.pathname || '/');
    const lower = path.toLowerCase();
    const marker = '/monde-magique/';
    const idx = lower.indexOf(marker);
    if (idx !== -1) {
        return path.slice(0, idx + marker.length);
    }
    return '/';
}

// Widget flottant style robot
function injectChatbotWidget() {
    if (document.getElementById('chatbot-widget')) return;
    const widget = document.createElement('div');
    widget.id = 'chatbot-widget';
    widget.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:1200;';
    widget.innerHTML = `
        <style>
            .chatbot-toggle{width:56px;height:56px;border-radius:50%;background:#764ba2;color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 16px rgba(0,0,0,.25);cursor:pointer;font-size:26px;}
            .chatbot-panel{position:fixed;bottom:90px;right:20px;width:320px;max-height:420px;background:#fff;border-radius:12px;box-shadow:0 10px 24px rgba(0,0,0,.25);display:none;flex-direction:column;overflow:hidden;border:2px solid #667eea}
            .chatbot-header{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:10px 12px;display:flex;align-items:center;justify-content:space-between}
            .chatbot-body{padding:10px;height:280px;overflow-y:auto;background:#f8f9fa}
            .chatbot-footer{padding:8px;display:flex;gap:6px;border-top:1px solid #eee;background:#fff}
            .chat-input{flex:1;padding:8px;border:1px solid #ddd;border-radius:8px}
            .btn-send{padding:8px 10px;background:#667eea;color:#fff;border:none;border-radius:8px;cursor:pointer}
            .questions-counter{font-size:12px;color:#fff;margin-left:8px}
            .chat-message{display:flex;gap:8px;margin-bottom:8px;opacity:.0;transition:opacity .2s}
            .chat-message.visible{opacity:1}
            .chat-message.user .message-content{background:#dff0ff}
            .chat-message.bot .message-content{background:#fff}
            .message-avatar img{width:28px;height:28px;border-radius:50%}
            .message-content{padding:8px 10px;border-radius:10px;max-width:220px}
        </style>
        <div class="chatbot-toggle" id="btn-chatbot">🤖</div>
        <div class="chatbot-panel" id="chatbot-panel">
            <div class="chatbot-header">
                <div>مساعد ذكي</div>
                <div class="questions-counter">أسئلة: <span id="questions-left">5/5</span> · 💎 <span id="user-diamonds">${(JSON.parse(localStorage.getItem('user_data')||'{}').diamonds)||0}</span></div>
            </div>
            <div class="chatbot-body" id="chat-conversation"></div>
            <div class="chatbot-footer">
                <input id="chat-input" class="chat-input" placeholder="اكتب سؤالك..." />
                <button id="btn-send" class="btn-send">إرسال</button>
            </div>
        </div>`;
    document.body.appendChild(widget);
    const btn = document.getElementById('btn-chatbot');
    const panel = document.getElementById('chatbot-panel');
    btn.addEventListener('click', () => {
        panel.style.display = panel.style.display === 'none' || panel.style.display === '' ? 'flex' : 'none';
    });
}

// Déduire un diamant par message envoyé (après le 5e)
const originalSend = ChatbotSystem.prototype.sendMessage;
ChatbotSystem.prototype.sendMessage = async function(message){
    // Si dépassement, tenter achat auto d'1 diamant -> +3 questions
    const remaining = this.config.maxQuestionsPerSession - this.questionsAsked;
    if (remaining <= 0) {
        if (window.Economy?.deductDiamonds(1)) {
            this.config.maxQuestionsPerSession += 3;
            this.updateDiamondsDisplay();
        }
    }
    return originalSend.call(this, message);
};