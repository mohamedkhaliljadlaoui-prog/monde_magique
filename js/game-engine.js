class GameEngine {
    constructor() {
        this.user = null;
        this.currentStage = 1;
        this.currentStation = 1;
        this.guide = null;
        this.inventory = [];
        this.achievements = [];
        this.gameState = {
            diamonds: 0,
            coins: 0,
            xp: 0,
            level: 1,
            completedStages: [],
            unlockedVehicles: ['bicycle'],
            guideOutfits: ['basic']
        };
    }

    // Initialisation du jeu
    async init(userData) {
        this.user = userData;
        await this.loadGameState();
        await this.setupGuide();
        await this.loadStage(this.currentStage);
        
        this.startBackgroundMusic();
        this.setupEventListeners();
        
        console.log('🎮 Jeu initialisé pour:', this.user.username);
    }

    // Configuration du guide
    async setupGuide() {
        const guideGender = this.user.gender === 'boy' ? 'male' : 'female';
        const guideData = await this.fetchGuideData(guideGender);
        
        this.guide = {
            ...guideData,
            name: this.user.guideName || guideData.defaultNames[Math.floor(Math.random() * guideData.defaultNames.length)],
            level: 1,
            relationship: 0,
            vehicle: 'bicycle',
            outfit: 'basic',
            mood: 'happy',
            voice: this.user.gender === 'boy' ? 'male_voice' : 'female_voice'
        };
        
        this.guide.speak = (text) => this.textToSpeech(text, this.guide.voice);
    }

    // Chargement d'un stage
    async loadStage(stageNumber) {
        const stageData = await this.fetchStageData(stageNumber);
        
        this.currentStage = {
            number: stageNumber,
            ...stageData,
            stations: this.initializeStations(stageData),
            startTime: new Date(),
            score: 0,
            completed: false
        };
        
        // Mettre à jour l'interface
        this.updateStageUI();
        
        // Guide accueillant
        this.guideWelcomeMessage();
    }

    // Initialisation des stations
    initializeStations(stageData) {
        return stageData.stations.map((station, index) => ({
            number: index + 1,
            ...station,
            completed: false,
            score: 0,
            attempts: 0,
            timeSpent: 0,
            hintsUsed: 0,
            chatbotUses: 0
        }));
    }

    // Gestion des récompenses
    calculateRewards(stage, performance) {
        const baseRewards = {
            diamonds: stage * 5,
            coins: stage * 100,
            xp: stage * 500
        };
        
        // Multiplicateur selon performance
        let multiplier = 1;
        if (performance >= 90) multiplier = 1.5;
        else if (performance >= 80) multiplier = 1.2;
        else if (performance >= 70) multiplier = 1.0;
        else multiplier = 0.8;
        
        // Bonus pour score parfait
        if (performance === 100) {
            baseRewards.specialItem = `stage_${stage}_perfect_badge`;
            baseRewards.diamonds += 10;
        }
        
        // Appliquer multiplicateur
        Object.keys(baseRewards).forEach(key => {
            if (typeof baseRewards[key] === 'number') {
                baseRewards[key] = Math.round(baseRewards[key] * multiplier);
            }
        });
        
        return baseRewards;
    }

    // Achats boutique
    async purchaseItem(itemType, itemId, cost) {
        if (this.gameState[itemType].includes(itemId)) {
            throw new Error('Item déjà possédé');
        }
        
        if (this.gameState.coins < cost) {
            throw new Error('Pièces insuffisantes');
        }
        
        // Débiter les pièces
        this.gameState.coins -= cost;
        
        // Ajouter l'item
        this.gameState[itemType].push(itemId);
        
        // Sauvegarder
        await this.saveGameState();
        
        // Mettre à jour l'interface
        this.updateInventory();
        
        // Feedback guide
        this.guide.speak(`Super! Tu as acheté ${itemId}!`);
        
        return true;
    }

    // Mise à niveau véhicule
    async upgradeVehicle(newVehicle, cost) {
        const vehicleHierarchy = ['bicycle', 'scooter', 'car', 'train', 'plane'];
        const currentIndex = vehicleHierarchy.indexOf(this.guide.vehicle);
        const newIndex = vehicleHierarchy.indexOf(newVehicle);
        
        if (newIndex <= currentIndex) {
            throw new Error('Véhicule déjà débloqué ou inférieur');
        }
        
        if (newIndex !== currentIndex + 1) {
            throw new Error('Doit débloquer les véhicules dans l\'ordre');
        }
        
        await this.purchaseItem('unlockedVehicles', newVehicle, cost);
        this.guide.vehicle = newVehicle;
        
        // Animation spéciale
        this.playUpgradeAnimation(newVehicle);
        
        return true;
    }

    // Chatbot IA
    async askChatbot(question, context) {
        if (this.currentStage.stations[this.currentStation - 1].chatbotUses >= 5) {
            throw new Error('Limite de questions atteinte pour cette station');
        }
        
        // Compter l'utilisation
        this.currentStage.stations[this.currentStation - 1].chatbotUses++;
        
        try {
            const response = await fetch('/api/chatbot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.user.token}`
                },
                body: JSON.stringify({
                    question,
                    context: {
                        stage: this.currentStage.number,
                        station: this.currentStation,
                        topic: this.currentStage.topic,
                        language: this.user.language || 'fr'
                    },
                    previousAnswers: this.getPreviousAnswers(),
                    guidePersonality: this.guide.personality
                })
            });
            
            const data = await response.json();
            
            // Ajouter à l'historique
            this.addToChatHistory({
                sender: 'guide',
                text: data.answer,
                timestamp: new Date()
            });
            
            return data;
        } catch (error) {
            console.error('Chatbot error:', error);
            return {
                answer: "Désolé, je ne peux pas répondre pour le moment. Essaie de reformuler ta question!",
                confidence: 0
            };
        }
    }

    // Sauvegarde progression
    async saveProgress() {
        const progressData = {
            userId: this.user.id,
            stage: this.currentStage.number,
            station: this.currentStation,
            score: this.currentStage.score,
            gameState: this.gameState,
            timestamp: new Date()
        };
        
        try {
            await fetch('/api/save-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.user.token}`
                },
                body: JSON.stringify(progressData)
            });
            
            console.log('💾 Progression sauvegardée');
        } catch (error) {
            console.error('Erreur sauvegarde:', error);
            // Sauvegarde locale
            localStorage.setItem(`progress_${this.user.id}`, JSON.stringify(progressData));
        }
    }

    // Text-to-speech
    textToSpeech(text, voiceType = 'default') {
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(text);
            
            // Sélection voix selon guide
            const voices = speechSynthesis.getVoices();
            const preferredVoice = voices.find(voice => 
                voice.lang.startsWith(this.user.language || 'fr') &&
                voice.name.includes(voiceType === 'male' ? 'Homme' : 'Femme')
            );
            
            if (preferredVoice) {
                utterance.voice = preferredVoice;
            }
            
            utterance.rate = 0.9;
            utterance.pitch = 1.2;
            utterance.volume = 1;
            
            speechSynthesis.speak(utterance);
        }
    }

    // Musique de fond
    startBackgroundMusic() {
        this.backgroundMusic = new Audio('assets/audio/background-music.mp3');
        this.backgroundMusic.loop = true;
        this.backgroundMusic.volume = 0.3;
        
        // Démarrer après interaction utilisateur
        document.addEventListener('click', () => {
            if (this.user.settings.musicEnabled && this.backgroundMusic.paused) {
                this.backgroundMusic.play().catch(console.error);
            }
        }, { once: true });
    }

    // Guide messages
    guideWelcomeMessage() {
        const messages = [
            `Prêt pour l'aventure dans ${this.currentStage.title} ?`,
            `Allons découvrir ${this.currentStage.topic} ensemble !`,
            `Chaque station t'apprendra quelque chose de nouveau !`
        ];
        
        const randomMessage = messages[Math.floor(Math.random() * messages.length)];
        this.guide.speak(randomMessage);
        this.showGuideBubble(randomMessage);
    }

    showGuideBubble(message, duration = 5000) {
        const bubble = document.createElement('div');
        bubble.className = 'guide-speech-bubble show';
        bubble.innerHTML = `
            <div class="guide-avatar-small">
                <img src="${this.guide.avatar}" alt="${this.guide.name}">
            </div>
            <div class="speech-content">
                <p>${message}</p>
            </div>
        `;
        
        document.body.appendChild(bubble);
        
        setTimeout(() => {
            bubble.classList.remove('show');
            setTimeout(() => bubble.remove(), 300);
        }, duration);
    }

    // Gestion des succès
    unlockAchievement(achievementId) {
        if (!this.achievements.includes(achievementId)) {
            this.achievements.push(achievementId);
            
            // Animation succès
            this.showAchievementPopup(achievementId);
            
            // Récompense
            const reward = this.getAchievementReward(achievementId);
            this.gameState.diamonds += reward.diamonds || 0;
            this.gameState.coins += reward.coins || 0;
            
            // Sauvegarder
            this.saveGameState();
        }
    }

    // Export pour utilisation globale
    static getInstance() {
        if (!GameEngine.instance) {
            GameEngine.instance = new GameEngine();
        }
        return GameEngine.instance;
    }

    // Initialiser un stage (appelé depuis les pages stage-*.html)
    static initStage(stageId, stageName) {
        const engine = GameEngine.getInstance();
        engine.currentStage = stageId;
        engine.currentStation = 1;
        
        // Charger le contenu du stage
        engine.loadStageContent(stageId, stageName);
        
        console.log(`🎮 Stage ${stageId} (${stageName}) initialisé`);
        
        return engine;
    }

    // Charger contenu du stage
    loadStageContent(stageId, stageName) {
        const stageContent = document.getElementById('station-content');
        if (!stageContent) return;

        // Afficher la première station (PDF/Lecture)
        this.displayStation(1, stageId, stageName);
        
        // Ajouter les écouteurs pour la navigation des stations
        this.setupStationNavigation(stageId, stageName);
    }

    // Afficher une station
    displayStation(stationNumber, stageId, stageName) {
        const stageContent = document.getElementById('station-content');
        if (!stageContent) return;

        let content = '';
        
        switch(stationNumber) {
            case 1:
                content = this.createPDFStation(stageId, stageName);
                break;
            case 2:
                content = this.createVideoStation(stageId, stageName);
                break;
            case 3:
                content = this.createMatchingGameStation(stageId, stageName);
                break;
            case 4:
                content = this.createImageQCMStation(stageId, stageName);
                break;
            case 5:
                content = this.createFinalQuizStation(stageId, stageName);
                break;
            case 6:
                content = this.createRewardsStation(stageId, stageName);
                break;
            case 7:
                content = this.createChatbotStation(stageId, stageName);
                break;
            default:
                content = '<p>Station non trouvée</p>';
        }
        
        stageContent.innerHTML = content;
        this.currentStation = stationNumber;
    }

    // Station 1: PDF/Lecture
    createPDFStation(stageId, stageName) {
        return `
            <div class="station-container station-1-pdf">
                <div class="station-header">
                    <h2>📚 Station 1: Lecture et Découverte</h2>
                    <p>Lis attentivement le contenu pour découvrir les faits fascinants!</p>
                </div>
                <div class="pdf-content">
                    <div class="learning-section">
                        <h3>🌟 Informations sur ${stageName}</h3>
                        <p>Contenu éducatif pour la stage ${stageId} en cours de chargement...</p>
                        <div class="pdf-embed">
                            <iframe src="assets/pdf/stage-${stageId}.pdf" width="100%" height="400"></iframe>
                        </div>
                    </div>
                    <div class="tips-section">
                        <h4>💡 Astuces d'apprentissage:</h4>
                        <ul>
                            <li>✅ Prends des notes des faits intéressants</li>
                            <li>✅ Imagine-toi visitant ces lieux</li>
                            <li>✅ Prépare 2 questions pour le guide</li>
                        </ul>
                    </div>
                </div>
                <div class="station-controls">
                    <button class="btn-next" onclick="window.GameEngine.displayStation(2, ${stageId}, '${stageName}')">
                        ✅ J'ai lu et compris → Station Suivante
                    </button>
                </div>
            </div>
        `;
    }

    // Station 2: Vidéo
    createVideoStation(stageId, stageName) {
        return `
            <div class="station-container station-2-video">
                <div class="station-header">
                    <h2>🎬 Station 2: Regarder une Vidéo Éducative</h2>
                    <p>Regarde cette courte vidéo pour en savoir plus!</p>
                </div>
                <div class="video-content">
                    <video width="100%" height="400" controls>
                        <source src="assets/vedios/stage-${stageId}.mp4" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture vidéo.
                    </video>
                </div>
                <div class="tips-section">
                    <h4>🎯 Points à Retenir:</h4>
                    <ul>
                        <li>🔹 Les thèmes principaux de la vidéo</li>
                        <li>🔹 Les faits historiques importants</li>
                        <li>🔹 Les points de culture locale</li>
                    </ul>
                </div>
                <div class="station-controls">
                    <button class="btn-prev" onclick="window.GameEngine.displayStation(1, ${stageId}, '${stageName}')">
                        ← Station Précédente
                    </button>
                    <button class="btn-next" onclick="window.GameEngine.displayStation(3, ${stageId}, '${stageName}')">
                        Station Suivante ✅
                    </button>
                </div>
            </div>
        `;
    }

    // Station 3: Jeu de Correspondance
    createMatchingGameStation(stageId, stageName) {
        return `
            <div class="station-container station-3-game">
                <div class="station-header">
                    <h2>🎮 Station 3: Jeu de Correspondance</h2>
                    <p>Associe les images aux descriptions correctes!</p>
                </div>
                <div class="matching-game">
                    <div class="game-instructions">Associe 5 paires pour avancer!</div>
                    <div class="game-area" id="matching-area">
                        <div class="left-items">
                            <div class="item" draggable="true">Image 1</div>
                            <div class="item" draggable="true">Image 2</div>
                            <div class="item" draggable="true">Image 3</div>
                        </div>
                        <div class="right-items">
                            <div class="item">Description A</div>
                            <div class="item">Description B</div>
                            <div class="item">Description C</div>
                        </div>
                    </div>
                </div>
                <div class="station-controls">
                    <button class="btn-prev" onclick="window.GameEngine.displayStation(2, ${stageId}, '${stageName}')">
                        ← Station Précédente
                    </button>
                    <button class="btn-next" onclick="window.GameEngine.displayStation(4, ${stageId}, '${stageName}')">
                        Station Suivante ✅
                    </button>
                </div>
            </div>
        `;
    }

    // Station 4: QCM avec Images
    createImageQCMStation(stageId, stageName) {
        return `
            <div class="station-container station-4-qcm">
                <div class="station-header">
                    <h2>📷 Station 4: Teste ton Apprentissage</h2>
                    <p>Réponds à des questions sur ce que tu as appris!</p>
                </div>
                <div class="quiz-content">
                    <div class="question-card">
                        <h3>?  Quelle est la capitale?</h3>
                        <div class="options">
                            <button class="option">🏙️ Option A</button>
                            <button class="option">🏙️ Option B</button>
                            <button class="option">🏙️ Option C</button>
                            <button class="option">🏙️ Option D</button>
                        </div>
                    </div>
                </div>
                <div class="station-controls">
                    <button class="btn-prev" onclick="window.GameEngine.displayStation(3, ${stageId}, '${stageName}')">
                        ← Station Précédente
                    </button>
                    <button class="btn-next" onclick="window.GameEngine.displayStation(5, ${stageId}, '${stageName}')">
                        Station Suivante ✅
                    </button>
                </div>
            </div>
        `;
    }

    // Station 5: Quiz Final
    createFinalQuizStation(stageId, stageName) {
        return `
            <div class="station-container station-5-final-quiz">
                <div class="station-header">
                    <h2>📝 Station 5: Quiz Final du Stage</h2>
                    <p>Questions ${stageName} pour vérifier tes connaissances!</p>
                </div>
                <div class="quiz-content">
                    <div class="question-card">
                        <h3>?  Question 1/20: Quel est le plus grand...?</h3>
                        <div class="options">
                            <button class="option">✓ Réponse 1</button>
                            <button class="option">✓ Réponse 2</button>
                            <button class="option">✓ Réponse 3</button>
                        </div>
                    </div>
                </div>
                <div class="station-controls">
                    <button class="btn-prev" onclick="window.GameEngine.displayStation(4, ${stageId}, '${stageName}')">
                        ← Station Précédente
                    </button>
                    <button class="btn-next" onclick="window.GameEngine.displayStation(6, ${stageId}, '${stageName}')">
                        Station Suivante ✅
                    </button>
                </div>
            </div>
        `;
    }

    // Station 6: Récompenses
    createRewardsStation(stageId, stageName) {
        return `
            <div class="station-container station-6-rewards">
                <div class="station-header">
                    <h2>🎉 Station 6: Tes Récompenses!</h2>
                    <p>Félicitations pour avoir complété ce stage!</p>
                </div>
                <div class="rewards-content">
                    <div class="reward-item">
                        <span class="reward-icon">💎</span>
                        <p>+50 Diamants</p>
                    </div>
                    <div class="reward-item">
                        <span class="reward-icon">🪙</span>
                        <p>+500 Pièces d'Or</p>
                    </div>
                    <div class="reward-item">
                        <span class="reward-icon">⭐</span>
                        <p>+1000 XP</p>
                    </div>
                </div>
                <div class="station-controls">
                    <button class="btn-prev" onclick="window.GameEngine.displayStation(5, ${stageId}, '${stageName}')">
                        ← Station Précédente
                    </button>
                    <button class="btn-next" onclick="window.GameEngine.displayStation(7, ${stageId}, '${stageName}')">
                        Station Suivante ✅
                    </button>
                </div>
            </div>
        `;
    }

    // Station 7: Chatbot Assistant
    createChatbotStation(stageId, stageName) {
        return `
            <div class="station-container station-7-chatbot">
                <div class="station-header">
                    <h2>🤖 Station 7: Chat avec l'Assistant IA</h2>
                    <p>Pose des questions à propos de ${stageName}!</p>
                </div>
                <div class="chatbot-container">
                    <div class="chat-messages" id="chat-messages">
                        <div class="message bot">
                            Salut! Je suis ton assistant IA. Pose-moi des questions sur ce stage! 🤖
                        </div>
                    </div>
                    <div class="chat-input-area">
                        <input type="text" class="chat-input" placeholder="Saisissez votre question...">
                        <button class="btn-send">Envoyer 📤</button>
                    </div>
                </div>
                <div class="station-controls">
                    <button class="btn-prev" onclick="window.GameEngine.displayStation(6, ${stageId}, '${stageName}')">
                        ← Station Précédente
                    </button>
                    <button class="btn-complete" onclick="window.location.href='dashboard.html'">
                        ✅ Terminer le Stage
                    </button>
                </div>
            </div>
        `;
    }

    // Configuration navigation stations
    setupStationNavigation(stageId, stageName) {
        // Ajouter les écouteurs pour les boutons
        const stationContent = document.getElementById('station-content');
        if (stationContent) {
            stationContent.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-next')) {
                    // Action déjà gérée par onclick
                } else if (e.target.classList.contains('btn-prev')) {
                    // Action déjà gérée par onclick
                }
            });
        }
    }


// Initialisation globale
window.GameEngine = GameEngine;