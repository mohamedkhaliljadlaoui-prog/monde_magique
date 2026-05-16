// ===================================
// SHOP.JS - Shop Management System
// ===================================

class ShopManager {
    constructor() {
        this.userCoins = 0;
        this.userDiamonds = 0;
        this.userLevel = 1;
        this.ownedItems = [];
        this.currentTab = 'vehicles';
        this.selectedItem = null;
    }

    /**
     * Initialize shop
     */
    async init() {
        await this.loadUserData();
        this.setupEventListeners();
        this.renderItems('vehicles');
    }

    /**
     * Load user data
     */
    async loadUserData() {
        try {
            const response = await API.get('api/progress.php', {
                action: 'getUserEconomy',
                token: Auth.getToken()
            });

            if (response.success) {
                this.userCoins = response.data.coins;
                this.userDiamonds = response.data.diamonds;
                this.userLevel = response.data.level;
                this.ownedItems = response.data.owned_items || [];
                
                this.updateUI();
            }
        } catch (error) {
            console.error('Error loading user data:', error);
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.switchTab(e.target.dataset.tab);
            });
        });

        // Modal events
        document.getElementById('confirm-purchase').addEventListener('click', () => {
            this.confirmPurchase();
        });

        document.getElementById('cancel-purchase').addEventListener('click', () => {
            this.closeModal();
        });
    }

    /**
     * Switch between tabs
     */
    switchTab(tab) {
        this.currentTab = tab;
        
        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
        
        // Update sections
        document.querySelectorAll('.shop-section').forEach(section => {
            section.classList.remove('active');
        });
        document.getElementById(`${tab}-section`).classList.add('active');
        
        this.renderItems(tab);
    }

    /**
     * Render items for category
     */
    renderItems(category) {
        const grid = document.getElementById(`${category}-grid`);
        const items = SHOP_ITEMS[category];
        
        grid.innerHTML = items.map(item => this.createItemCard(item)).join('');
        
        // Add click events
        grid.querySelectorAll('.shop-item').forEach((card, index) => {
            card.addEventListener('click', () => {
                this.selectItem(items[index]);
            });
        });
    }

    /**
     * Create item card HTML
     */
    createItemCard(item) {
        const isOwned = this.ownedItems.includes(item.id);
        const isLocked = item.level > this.userLevel;
        const canBuy = !isOwned && !isLocked && this.hasEnoughCurrency(item);
        
        let buttonText = 'شراء';
        let buttonClass = 'locked';
        
        if (isOwned) {
            buttonText = 'مملوك ✓';
            buttonClass = 'owned';
        } else if (isLocked) {
            buttonText = `🔒 المستوى ${item.level}`;
        } else if (canBuy) {
            buttonClass = 'can-buy';
        } else {
            buttonText = 'غير كافي';
        }
        
        return `
            <div class="shop-item ${isOwned ? 'owned' : ''} ${isLocked ? 'locked' : ''}" data-id="${item.id}">
                ${isOwned ? '<div class="item-badge">✓</div>' : ''}
                ${isLocked ? '<div class="item-badge locked">🔒</div>' : ''}
                <img src="${item.image}" alt="${item.name}" class="item-image">
                <div class="item-name">${item.name}</div>
                <div class="item-price">
                    ${item.currency === 'coins' ? '🪙' : '💎'} ${item.price}
                </div>
                <div class="item-level">المستوى ${item.level}+</div>
                <button class="btn-buy ${buttonClass}" ${isOwned || isLocked ? 'disabled' : ''}>
                    ${buttonText}
                </button>
            </div>
        `;
    }

    /**
     * Check if user has enough currency
     */
    hasEnoughCurrency(item) {
        if (item.currency === 'coins') {
            return this.userCoins >= item.price;
        } else {
            return this.userDiamonds >= item.price;
        }
    }

    /**
     * Select item for purchase
     */
    selectItem(item) {
        if (this.ownedItems.includes(item.id) || item.level > this.userLevel) {
            return;
        }
        
        if (!this.hasEnoughCurrency(item)) {
            this.showNotification('ليس لديك عملة كافية!', 'error');
            return;
        }
        
        this.selectedItem = item;
        this.showPurchaseModal(item);
    }

    /**
     * Show purchase confirmation modal
     */
    showPurchaseModal(item) {
        const modal = document.getElementById('purchase-modal');
        const preview = document.getElementById('modal-item-preview');
        const price = document.getElementById('modal-item-price');
        
        preview.innerHTML = `
            <img src="${item.image}" alt="${item.name}" style="width: 150px; height: auto;">
            <h4>${item.name}</h4>
        `;
        
        price.textContent = `السعر: ${item.currency === 'coins' ? '🪙' : '💎'} ${item.price}`;
        
        modal.classList.add('active');
    }

    /**
     * Confirm purchase
     */
    async confirmPurchase() {
        if (!this.selectedItem) return;
        
        try {
            const response = await API.post('api/shop.php', {
                action: 'purchaseItem',
                token: Auth.getToken(),
                item_id: this.selectedItem.id,
                category: this.currentTab
            });

            if (response.success) {
                this.userCoins = response.data.coins;
                this.userDiamonds = response.data.diamonds;
                this.ownedItems.push(this.selectedItem.id);
                
                this.updateUI();
                this.renderItems(this.currentTab);
                this.closeModal();
                
                this.showNotification('تم الشراء بنجاح! 🎉', 'success');
                
                // Play success sound
                this.playSound('success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            console.error('Purchase error:', error);
            this.showNotification('خطأ في عملية الشراء', 'error');
        }
    }

    /**
     * Close modal
     */
    closeModal() {
        document.getElementById('purchase-modal').classList.remove('active');
        this.selectedItem = null;
    }

    /**
     * Update UI with current data
     */
    updateUI() {
        document.getElementById('user-coins').textContent = this.userCoins;
        document.getElementById('user-diamonds').textContent = this.userDiamonds;
        document.getElementById('guide-level').textContent = this.userLevel;
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#4caf50' : '#e74c3c'};
            color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    /**
     * Play sound effect
     */
    playSound(soundName) {
        const audio = new Audio(`assets/audio/effects/${soundName}.mp3`);
        audio.volume = 0.5;
        audio.play().catch(e => console.log('Audio play failed:', e));
    }
}

// Initialize when DOM is ready
let shopManager;
document.addEventListener('DOMContentLoaded', () => {
    shopManager = new ShopManager();
});

// Export
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ShopManager;
}
