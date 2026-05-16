// app.js - Initialisation principale de l'application
class MondeMagiqueApp {
    constructor() {
        this.version = '1.0.0';
        this.environment = 'production';
        this.user = null;
        this.game = null;
        this.currentStage = 1;
        this.language = 'fr';
        
        this.init();
    }
    
    async init() {
        console.log('🚀 Initialisation de Monde Magique...');
        
        // Vérifier la compatibilité du navigateur
        if (!this.checkBrowserCompatibility()) {
            this.showCompatibilityWarning();
            return;
        }
        
        // Charger la configuration
        await this.loadConfig();
        
        // Initialiser les services
        this.initServices();
        
        // Vérifier l'authentification
        await this.checkAuth();
        
        // Initialiser l'interface
        this.initUI();
        
        // Démarrer le jeu
        this.startGame();
        
        console.log('✅ Application initialisée avec succès!');
    }
    
    checkBrowserCompatibility() {
        const requiredFeatures = [
            'localStorage',
            'sessionStorage',
            'fetch',
            'Promise',
            'WebSpeechAPI' in window,
            'requestAnimationFrame' in window
        ];
        
        return requiredFeatures.every(feature => {
            if (typeof feature === 'string') {
                return feature in window;
            }
            return feature;
        });
    }
    
    async loadConfig() {
        try {
            const response = await fetch('config/app-config.json');
            this.config = await response.json();
            
            // Configurer la langue
            this.language = localStorage.getItem('userLanguage') || 
                           navigator.language.split('-')[0] || 
                           'fr';
            
            // Charger les traductions
            await this.loadTranslations();
            
        } catch (error) {
            console.error('Erreur chargement configuration:', error);
            this.config = this.getDefaultConfig();
        }
    }
    
    initServices() {
        // Initialiser les services
        this.authService = new AuthService();
        this.gameService = new GameService();
        this.aiService = new AIService(this.config.ai);
        this.storageService = new StorageService();
        
        // Configurer les événements globaux
        this.setupGlobalEvents();
    }
    
    async checkAuth() {
        const token = localStorage.getItem('authToken');
        
        if (token) {
            try {
                this.user = await this.authService.validateToken(token);
                
                // Charger la progression du joueur
                this.game = await this.gameService.loadGame(this.user.id);
                this.currentStage = this.user.currentStage || 1;
                
            } catch (error) {
                console.warn('Token invalide, déconnexion...');
                this.logout();
            }
        } else {
            // Rediriger vers la page de login
            if (!window.location.pathname.includes('login') && 
                !window.location.pathname.includes('inscription')) {
                window.location.href = 'login.html';
            }
        }
    }
    
    initUI() {
        // Initialiser les composants d'interface
        this.initLanguageSwitcher();
        this.initThemeSwitcher();
        this.initNotifications();
        this.initLoadingScreen();
        
        // Mettre à jour l'interface selon l'utilisateur
        this.updateUI();
    }
    
    startGame() {
        if (this.user && this.game) {
            // Démarrer la musique de fond
            this.startBackgroundMusic();
            
            // Initialiser les animations
            this.initAnimations();
            
            // Vérifier les mises à jour
            this.checkForUpdates();
            
            // Démarrer le suivi analytique
            this.startAnalytics();
        }
    }
    
    logout() {
        localStorage.removeItem('authToken');
        sessionStorage.clear();
        window.location.href = 'index.html';
    }
    
    // Gestion des erreurs globales
    setupGlobalErrorHandling() {
        window.onerror = (message, source, lineno, colno, error) => {
            console.error('Erreur globale:', { message, source, lineno, colno, error });
            
            // Envoyer l'erreur au serveur (si en production)
            if (this.environment === 'production') {
                this.reportError(error);
            }
            
            // Afficher un message d'erreur convivial
            this.showErrorToast('Une erreur est survenue. Le jeu continue...');
            
            return true;
        };
        
        // Gérer les promesses non catchées
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Promesse non gérée:', event.reason);
            event.preventDefault();
        });
    }
    
    showErrorToast(message) {
        const toast = document.createElement('div');
        toast.className = 'error-toast';
        toast.innerHTML = `
            <span>⚠️ ${message}</span>
            <button onclick="this.parentElement.remove()">×</button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
    
    getDefaultConfig() {
        return {
            api: {
                baseUrl: 'http://localhost/monde-magique/api',
                timeout: 30000
            },
            game: {
                stages: 10,
                stationsPerStage: 7,
                dailyChatbotLimit: 50
            },
            features: {
                voiceEnabled: true,
                animationsEnabled: true,
                offlineMode: true
            }
        };
    }
}

// Initialiser l'application au chargement
document.addEventListener('DOMContentLoaded', () => {
    window.app = new MondeMagiqueApp();
});