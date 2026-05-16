/**
 * Gestion des diamants, pièces et progression utilisateur
 * Synchronisation avec la base de données
 */

class ResourceManager {
    constructor(userId) {
        this.userId = userId;
        this.diamants = localStorage.getItem(`diamants_${userId}`) || 10;
        this.pieces = localStorage.getItem(`pieces_${userId}`) || 0;
        this.progression = localStorage.getItem(`progression_${userId}`) || 0;
    }

    /**
     * Charger ressources depuis la BD
     */
    async loadFromDB() {
        try {
            const response = await fetch(`/monde-magique/php/api/users.php?id=${this.userId}`);
            const data = await response.json();
            this.diamants = data.diamants;
            this.pieces = data.pieces;
            this.progression = data.progression;
            this.saveLocal();
            this.updateUI();
            return true;
        } catch (error) {
            console.error('Erreur chargement ressources:', error);
            return false;
        }
    }

    /**
     * Ajouter ressources
     */
    add(diamants = 0, pieces = 0) {
        this.diamants = parseInt(this.diamants) + diamants;
        this.pieces = parseInt(this.pieces) + pieces;
        this.saveLocal();
        this.updateUI();
    }

    /**
     * Utiliser diamants (ex: aide chatbot)
     */
    useDiamants(amount) {
        if (this.diamants >= amount) {
            this.diamants -= amount;
            this.saveLocal();
            this.updateUI();
            return true;
        }
        return false;
    }

    /**
     * Sauvegarder localement
     */
    saveLocal() {
        localStorage.setItem(`diamants_${this.userId}`, this.diamants);
        localStorage.setItem(`pieces_${this.userId}`, this.pieces);
        localStorage.setItem(`progression_${this.userId}`, this.progression);
    }

    /**
     * Synchroniser avec BD
     */
    async syncToDB() {
        try {
            await fetch(`/monde-magique/php/api/users.php/update-resources/${this.userId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    diamants: 0,
                    pieces: 0
                })
            });
            return true;
        } catch (error) {
            console.error('Erreur sync BD:', error);
            return false;
        }
    }

    /**
     * Mettre à jour affichage dans le DOM
     */
    updateUI() {
        const diamantsEl = document.getElementById('diamonds-count');
        const coinsEl = document.getElementById('coins-count');
        
        if (diamantsEl) diamantsEl.textContent = Math.floor(this.diamants);
        if (coinsEl) coinsEl.textContent = Math.floor(this.pieces);
    }

    /**
     * Obtenir affichage formaté
     */
    getDisplay() {
        return {
            diamants: Math.floor(this.diamants),
            pieces: Math.floor(this.pieces),
            progression: Math.floor(this.progression)
        };
    }
}

// Initialiser et exposer globalement
let resourceManager = null;

function initResourceManager(userId) {
    resourceManager = new ResourceManager(userId);
    resourceManager.loadFromDB();
    return resourceManager;
}

window.ResourceManager = ResourceManager;
window.initResourceManager = initResourceManager;
