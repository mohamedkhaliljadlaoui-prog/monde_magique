// ============================================================
// API DATA MANAGER - Gestion des données avec la base de données
// ============================================================

class APIDataManager {
    constructor() {
        this.userId = null;
        this.stageNum = null;
        this.isOnline = navigator.onLine;
        this.backupData = {}; // Backup localStorage si pas de connexion
        this.initSession();
    }
    
    async initSession() {
        try {
            const response = await fetch('auth.php?action=check_session', {
                credentials: 'include'
            });
            const data = await response.json();
            
            if (data.authenticated) {
                this.userId = data.user_id;
            } else {
                // Rediriger vers login si pas authentifié
                window.location.href = 'auth-login.php';
            }
        } catch (error) {
            console.error('Session check failed:', error);
        }
    }
    
    setStageNum(num) {
        this.stageNum = num;
    }
    
    // Sauvegarder la progression
    async saveProgress(stepData) {
        if (!this.userId || !this.stageNum) return false;
        
        try {
            const formData = new FormData();
            formData.append('action', 'save_progress');
            formData.append('stage_num', this.stageNum);
            formData.append('current_step', stepData.current_step || 0);
            formData.append('qcm_score', stepData.qcm_score || 0);
            formData.append('diamonds', stepData.diamonds || 0);
            formData.append('coins', stepData.coins || 0);
            
            const response = await fetch('progress_api.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });
            
            const result = await response.json();
            
            // Backup en localStorage aussi
            localStorage.setItem(`stage_${this.stageNum}_backup`, JSON.stringify(stepData));
            
            return result.success || false;
        } catch (error) {
            console.error('Save progress failed:', error);
            // En cas d'erreur, garder les données en localStorage
            localStorage.setItem(`stage_${this.stageNum}_backup`, JSON.stringify(stepData));
            return false;
        }
    }
    
    // Charger la progression
    async loadProgress() {
        if (!this.userId || !this.stageNum) return null;
        
        try {
            const response = await fetch(`progress_api.php?action=load_progress&stage_num=${this.stageNum}`, {
                credentials: 'include'
            });
            
            const result = await response.json();
            
            if (result.success) {
                return result.data;
            }
        } catch (error) {
            console.error('Load progress failed:', error);
        }
        
        // Fallback: chercher en localStorage
        const backup = localStorage.getItem(`stage_${this.stageNum}_backup`);
        return backup ? JSON.parse(backup) : null;
    }
    
    // Sauvegarder les réponses QCM
    async saveQCM(answers, qcmScore, allCorrect) {
        if (!this.userId || !this.stageNum) return false;
        
        try {
            const formData = new FormData();
            formData.append('action', 'save_qcm');
            formData.append('stage_num', this.stageNum);
            
            // Ajouter chaque réponse
            for (const [key, value] of Object.entries(answers)) {
                formData.append(key, value);
            }
            
            const response = await fetch('progress_api.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });
            
            const result = await response.json();
            return result.success || false;
        } catch (error) {
            console.error('Save QCM failed:', error);
            return false;
        }
    }
    
    // Sauvegarder l'essai
    async saveEssay(content, wordCount, score) {
        if (!this.userId || !this.stageNum) return false;
        
        try {
            const formData = new FormData();
            formData.append('action', 'save_essay');
            formData.append('stage_num', this.stageNum);
            formData.append('content', content);
            formData.append('word_count', wordCount);
            formData.append('score', score);
            
            const response = await fetch('progress_api.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });
            
            const result = await response.json();
            return result.success || false;
        } catch (error) {
            console.error('Save essay failed:', error);
            return false;
        }
    }
    
    // Marquer la stage comme complète
    async completeStage(finalScore) {
        if (!this.userId || !this.stageNum) return false;
        
        try {
            const formData = new FormData();
            formData.append('action', 'complete_stage');
            formData.append('stage_num', this.stageNum);
            formData.append('diamonds', 10);
            formData.append('coins', 100);
            
            const response = await fetch('progress_api.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });
            
            const result = await response.json();
            // Retourner l'objet avec success et next_stage
            if (result.success) {
                return { success: true, next_stage: result.next_stage };
            }
            return false;
        } catch (error) {
            console.error('Complete stage failed:', error);
            return false;
        }
    }
    
    // Aller au stage suivant
    goToNextStage(nextStageNum) {
        if (nextStageNum && nextStageNum <= 10) {
            window.location.href = 'stage.php?stage=' + nextStageNum;
        } else {
            this.goToDashboard();
        }
    }
    
    // Obtenir tous les récompenses
    async getRewards() {
        if (!this.userId) return null;
        
        try {
            const response = await fetch('progress_api.php?action=get_rewards', {
                credentials: 'include'
            });
            
            const result = await response.json();
            return result.success ? result.data : null;
        } catch (error) {
            console.error('Get rewards failed:', error);
            return null;
        }
    }
    
    // Retourner au tableau de bord
    goToDashboard() {
        window.location.href = 'dashboard-stages.php';
    }
    
    // Déconnexion
    async logout() {
        try {
            await fetch('auth.php?action=logout', {
                credentials: 'include'
            });
            window.location.href = 'auth-login.php';
        } catch (error) {
            console.error('Logout failed:', error);
        }
    }
}

// Instance globale
const apiManager = new APIDataManager();

// Attendre la session
function waitForSession() {
    return new Promise((resolve) => {
        const checkSession = setInterval(() => {
            if (apiManager.userId) {
                clearInterval(checkSession);
                resolve();
            }
        }, 100);
        setTimeout(() => {
            clearInterval(checkSession);
            window.location.href = 'auth-login.php';
        }, 5000);
    });
}
