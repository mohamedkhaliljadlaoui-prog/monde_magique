// ===================================
// AUTH.JS - Authentication System
// ===================================

class AuthManager {
    constructor() {
        this.API_URL = 'php/api/auth/login.php';
        this.token = localStorage.getItem('user_token');
        this.user = JSON.parse(localStorage.getItem('user_data') || 'null');
        
        // MODE TEST - Utilisateur factice pour test sans login
        this.TEST_MODE = true; // Mettre à false pour activer l'authentification
        if (this.TEST_MODE && !this.user) {
            this.user = {
                id: 999,
                username: 'TestUser',
                email: 'test@example.com',
                gender: 'boy',
                level: 5,
                xp: 2500,
                coins: 5000,
                diamonds: 50,
                current_stage: 3,
                avatar_url: 'assets/images/guides/boy/default.png',
                guide_name: 'تيو',
                language: 'ar',
                unlocked_vehicles: ['bicycle', 'scooter', 'car'],
                guide_outfits: ['basic', 'explorer', 'scientist']
            };
            this.token = 'test_token_' + Date.now();
            localStorage.setItem('user_token', this.token);
            localStorage.setItem('user_data', JSON.stringify(this.user));
        }
    }

    /**
     * Login user
     */
    async login(username, password) {
        try {
            const response = await fetch('php/api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    username,
                    password
                })
            });

            const data = await response.json();

            if (data.success) {
                this.token = data.token;
                this.user = data.user;
                
                // Store in localStorage
                localStorage.setItem('user_token', data.token);
                localStorage.setItem('user_data', JSON.stringify(data.user));
                
                return { success: true, user: data.user };
            } else {
                return { success: false, error: data.message || 'خطأ في تسجيل الدخول' };
            }
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, error: 'خطأ في الاتصال بالخادم' };
        }
    }

    /**
     * Register new user
     */
    async register(userData) {
        try {
            const response = await fetch('php/api/auth/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });

            const data = await response.json();

            if (data.success) {
                return { success: true, message: data.message, token: data.token, user: data.user };
            } else {
                return { success: false, error: data.message };
            }
        } catch (error) {
            console.error('Registration error:', error);
            return { success: false, error: 'خطأ في الاتصال بالخادم' };
        }
    }

    /**
     * Logout user
     */
    logout() {
        this.token = null;
        this.user = null;
        localStorage.removeItem('user_token');
        localStorage.removeItem('user_data');
        window.location.href = 'index.html';
    }

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        // En mode test, toujours authentifié
        if (this.TEST_MODE) return true;
        return this.token !== null;
    }

    /**
     * Get current user
     */
    getCurrentUser() {
        return this.user;
    }

    /**
     * Get authentication token
     */
    getToken() {
        return this.token;
    }

    /**
     * Verify token validity
     */
    async verifyToken() {
        if (!this.token) return false;

        try {
            const response = await fetch(this.API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.token}`
                },
                body: JSON.stringify({ action: 'verify' })
            });

            const data = await response.json();
            return data.valid === true;
        } catch (error) {
            console.error('Token verification error:', error);
            return false;
        }
    }

    /**
     * Protect page (redirect if not authenticated)
     */
    protectPage() {
        // En mode test, ne pas rediriger
        if (this.TEST_MODE) {
            console.log('🧪 MODE TEST ACTIVÉ - Authentification désactivée');
            return;
        }
        if (!this.isAuthenticated()) {
            window.location.href = 'login.html';
        }
    }
}

// Form validation utilities
class FormValidator {
    static validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    static validateUsername(username) {
        return username.length >= 3 && username.length <= 20;
    }

    static validatePassword(password) {
        return password.length >= 6;
    }

    static validateAge(age) {
        return age >= 8 && age <= 12;
    }

    static showError(element, message) {
        const errorDiv = element.parentElement.querySelector('.error-message') || 
                         document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        
        if (!element.parentElement.querySelector('.error-message')) {
            element.parentElement.appendChild(errorDiv);
        }
        
        element.classList.add('error');
    }

    static clearError(element) {
        const errorDiv = element.parentElement.querySelector('.error-message');
        if (errorDiv) errorDiv.remove();
        element.classList.remove('error');
    }
}

// Initialize global auth manager
const Auth = new AuthManager();

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { AuthManager, FormValidator };
}
