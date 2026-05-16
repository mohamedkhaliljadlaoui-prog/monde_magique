class MapController {
    constructor() {
        this.map = null;
        this.geoStages = null;
        this.user = null;
        this.currentStage = 1;
        this.guideElement = null;
    }

    // Initialiser la carte
    init() {
        this.map = L.map('interactive-map').setView([34.0, 9.0], 7);
        this.geoStages = new GeographicStages();
        
        this.setupMapEvents();
        this.createGuideElement();
        this.loadUserData();
        
        return this;
    }

    // Configurer les événements de la carte
    setupMapEvents() {
        // Zoom sur la région en cliquant
        this.map.on('click', (e) => {
            this.handleMapClick(e.latlng);
        });
        
        // Mettre à jour la position du guide lors du zoom/déplacement
        this.map.on('moveend', () => {
            this.updateGuidePosition();
        });
        
        // Afficher les coordonnées
        this.map.on('mousemove', (e) => {
            this.showCoordinates(e.latlng);
        });
    }

    // Créer l'élément du guide
    createGuideElement() {
        this.guideElement = document.getElementById('animated-guide');
        if (!this.guideElement) {
            this.guideElement = document.createElement('div');
            this.guideElement.id = 'animated-guide';
            this.guideElement.innerHTML = `
                <div class="guide-character" id="guide-character"></div>
                <div class="guide-vehicle" id="guide-vehicle"></div>
            `;
            document.querySelector('.map-container').appendChild(this.guideElement);
        }
    }

    // Charger les données utilisateur
    async loadUserData() {
        try {
            const response = await fetch('/api/user/current');
            this.user = await response.json();
            this.currentStage = this.user.currentStage || 1;
            
            this.updateUI();
            this.loadStage(this.currentStage);
        } catch (error) {
            console.error('Erreur chargement utilisateur:', error);
            this.loadDefaultStage();
        }
    }

    // Charger un stage
    loadStage(stageNumber) {
        this.currentStage = stageNumber;
        
        // Préparer la carte
        const stage = this.geoStages.prepareMapForStage(stageNumber, this.map);
        
        // Mettre à jour l'UI
        this.updateStageUI(stage);
        
        // Animer le guide
        this.animateGuideToStage(stage);
        
        // Sauvegarder la progression
        this.saveCurrentStage(stageNumber);
    }

    // Animer le guide vers le stage
    animateGuideToStage(stage) {
        const guideChar = document.getElementById('guide-character');
        const guideVehicle = document.getElementById('guide-vehicle');
        
        // Mettre à jour l'apparence selon le genre
        guideChar.className = `guide-character ${this.user.gender || 'boy'}`;
        
        // Mettre à jour le véhicule selon le niveau
        this.updateGuideVehicle();
        
        // Position initiale (dernier stage complété ou Tunisie)
        const startLatLng = this.getStartPosition();
        const endLatLng = L.latLng(stage.coordinates[0], stage.coordinates[1]);
        
        // Convertir en pixels
        const startPoint = this.map.latLngToContainerPoint(startLatLng);
        const endPoint = this.map.latLngToContainerPoint(endLatLng);
        
        // Positionner le guide
        this.guideElement.style.left = startPoint.x + 'px';
        this.guideElement.style.top = startPoint.y + 'px';
        this.guideElement.style.display = 'block';
        
        // Animer
        this.animateGuide(startPoint, endPoint, stage);
    }

    // Obtenir la position de départ
    getStartPosition() {
        // Si c'est le premier stage, partir de Tunis
        if (this.currentStage === 1) {
            return L.latLng(36.8, 10.1); // Tunis
        }
        
        // Sinon, partir du stage précédent
        const prevStage = this.geoStages.getStage(this.currentStage - 1);
        return L.latLng(prevStage.coordinates[0], prevStage.coordinates[1]);
    }

    // Mettre à jour le véhicule du guide
    updateGuideVehicle() {
        const vehicle = document.getElementById('guide-vehicle');
        const vehicles = ['bicycle', 'scooter', 'car', 'train', 'plane'];
        const userLevel = this.user.level || 1;
        
        let vehicleType = 'bicycle';
        if (userLevel >= 20) vehicleType = 'plane';
        else if (userLevel >= 15) vehicleType = 'train';
        else if (userLevel >= 10) vehicleType = 'car';
        else if (userLevel >= 5) vehicleType = 'scooter';
        
        vehicle.className = `guide-vehicle vehicle-${vehicleType}`;
    }

    // Animation du guide
    animateGuide(startPoint, endPoint, stage) {
        const duration = 2000; // 2 secondes
        const startTime = Date.now();
        
        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing
            const easedProgress = this.easeInOutCubic(progress);
            
            // Position actuelle
            const currentX = startPoint.x + (endPoint.x - startPoint.x) * easedProgress;
            const currentY = startPoint.y + (endPoint.y - startPoint.y) * easedProgress;
            
            // Mettre à jour
            this.guideElement.style.left = currentX + 'px';
            this.guideElement.style.top = currentY + 'px';
            
            // Rotation vers la destination
            const angle = Math.atan2(endPoint.y - currentY, endPoint.x - currentX) * 180 / Math.PI;
            this.guideElement.style.transform = `rotate(${angle}deg)`;
            
            // Ajouter des traces
            if (Math.random() > 0.7) {
                this.createFootstep(currentX, currentY);
            }
            
            // Continuer ou arrêter
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                this.onGuideArrived(stage);
            }
        };
        
        animate();
    }

    // Créer une trace de pas
    createFootstep(x, y) {
        const container = document.getElementById('footsteps-container') || 
                         this.createFootstepsContainer();
        
        const footstep = document.createElement('div');
        footstep.className = 'footstep';
        footstep.style.left = x + 'px';
        footstep.style.top = y + 'px';
        
        // Couleur aléatoire
        const colors = ['#FFD700', '#FF6B6B', '#4ECDC4', '#45B7D1'];
        footstep.style.background = colors[Math.floor(Math.random() * colors.length)];
        
        container.appendChild(footstep);
        
        // Disparaître
        setTimeout(() => {
            footstep.style.opacity = '0';
            setTimeout(() => footstep.remove(), 1000);
        }, 1000);
    }

    // Quand le guide arrive
    onGuideArrived(stage) {
        // Animation de saut
        this.guideElement.style.transition = 'transform 0.3s';
        this.guideElement.style.transform += ' translateY(-30px)';
        
        setTimeout(() => {
            this.guideElement.style.transform = this.guideElement.style.transform.replace(' translateY(-30px)', '');
        }, 300);
        
        // Dialogue
        this.speakGuideMessage(stage.guideDialogue);
        
        // Afficher le panneau de stage
        this.showStagePanel(stage);
    }

    // Faire parler le guide
    speakGuideMessage(message) {
        const speech = document.getElementById('guide-speech') || this.createSpeechElement();
        speech.textContent = message;
        speech.classList.add('visible');
        
        // Text-to-speech
        if ('speechSynthesis' in window && this.user.settings?.voiceEnabled !== false) {
            const utterance = new SpeechSynthesisUtterance(message);
            utterance.lang = this.user.language === 'ar' ? 'ar-SA' : 'fr-FR';
            utterance.rate = 0.9;
            utterance.pitch = 1.1;
            speechSynthesis.speak(utterance);
        }
        
        // Cacher
        setTimeout(() => {
            speech.classList.remove('visible');
        }, 5000);
    }

    // Afficher le panneau du stage
    showStagePanel(stage) {
        const panel = document.getElementById('stage-panel') || this.createStagePanel();
        
        panel.innerHTML = `
            <div class="stage-panel-content">
                <h3>${stage.title}</h3>
                <p>${stage.description || 'Découvre cette région fascinante !'}</p>
                
                <div class="stage-info">
                    <div class="info-item">
                        <span class="info-label">🗺️ Région:</span>
                        <span class="info-value">${this.getRegionName(stage.region)}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">⭐ Difficulté:</span>
                        <span class="info-value">${this.getDifficulty(stage.id)}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">⏱️ Durée estimée:</span>
                        <span class="info-value">${this.getEstimatedTime(stage.id)}</span>
                    </div>
                </div>
                
                <div class="stage-rewards">
                    <h4>🎁 Récompenses:</h4>
                    <div class="rewards-list">
                        <div class="reward">
                            <span class="reward-icon">💎</span>
                            <span class="reward-amount">${stage.id * 5}</span>
                            <span class="reward-label">Diamants</span>
                        </div>
                        <div class="reward">
                            <span class="reward-icon">🪙</span>
                            <span class="reward-amount">${stage.id * 100}</span>
                            <span class="reward-label">Pièces d'or</span>
                        </div>
                        <div class="reward">
                            <span class="reward-icon">⭐</span>
                            <span class="reward-amount">${stage.id * 500}</span>
                            <span class="reward-label">Points d'XP</span>
                        </div>
                    </div>
                </div>
                
                <button onclick="window.location.href='stage-${stage.id}.html'" 
                        class="btn-start-stage">
                    🚀 Commencer l'aventure
                </button>
                
                <button onclick="this.parentElement.parentElement.classList.remove('visible')" 
                        class="btn-close-panel">
                    ✕ Fermer
                </button>
            </div>
        `;
        
        panel.classList.add('visible');
    }

    // Mettre à jour l'interface utilisateur
    updateUI() {
        // Statistiques
        document.getElementById('diamonds-count').textContent = this.user.diamonds || 0;
        document.getElementById('coins-count').textContent = this.user.coins || 0;
        document.getElementById('level-display').textContent = this.user.level || 1;
        
        // Navigation des stages
        this.buildStageNavigation();
    }

    // Construire la navigation des stages
    buildStageNavigation() {
        const nav = document.getElementById('stage-navigation');
        if (!nav) return;
        
        let html = '';
        for (let i = 1; i <= 10; i++) {
            const stage = this.geoStages.getStage(i);
            const isUnlocked = this.geoStages.isStageUnlocked(i, this.user.progress || []);
            const isActive = i === this.currentStage;
            
            html += `
                <button class="stage-btn ${isActive ? 'active' : ''} ${!isUnlocked ? 'locked' : ''}"
                        ${isUnlocked ? `onclick="mapController.loadStage(${i})"` : ''}
                        title="${stage.title}">
                    ${i}. ${this.getStageIcon(i)}
                </button>
            `;
        }
        
        nav.innerHTML = html;
    }

    // Obtenir l'icône d'un stage
    getStageIcon(stageNumber) {
        const icons = {
            1: '🇹🇳',
            2: '🌍',
            3: '🦁',
            4: '🇪🇺',
            5: '🗾',
            6: '🇺🇸',
            7: '🇧🇷',
            8: '🇦🇺',
            9: '❄️',
            10: '🏆'
        };
        return icons[stageNumber] || '📍';
    }

    // Obtenir le nom de la région
    getRegionName(region) {
        const regions = {
            'tunisia': 'Tunisie',
            'maghreb': 'Maghreb',
            'africa': 'Afrique',
            'europe': 'Europe',
            'asia': 'Asie',
            'north-america': 'Amérique du Nord',
            'south-america': 'Amérique du Sud',
            'oceania': 'Océanie',
            'polar': 'Régions Polaires',
            'world': 'Monde Entier'
        };
        return regions[region] || region;
    }

    // Obtenir la difficulté
    getDifficulty(stageNumber) {
        if (stageNumber <= 3) return 'Facile';
        if (stageNumber <= 6) return 'Moyen';
        if (stageNumber <= 9) return 'Difficile';
        return 'Expert';
    }

    // Obtenir le temps estimé
    getEstimatedTime(stageNumber) {
        const times = [30, 45, 60, 75, 90, 105, 120, 135, 150, 180];
        return `${times[stageNumber - 1]} minutes`;
    }

    // Sauvegarder le stage actuel
    async saveCurrentStage(stageNumber) {
        try {
            await fetch('/api/user/update-stage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.user.token}`
                },
                body: JSON.stringify({ currentStage: stageNumber })
            });
        } catch (error) {
            console.error('Erreur sauvegarde stage:', error);
        }
    }

    // Fonction d'easing
    easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }
}

// Initialisation globale
let mapController;

document.addEventListener('DOMContentLoaded', () => {
    mapController = new MapController().init();
    window.mapController = mapController;
});