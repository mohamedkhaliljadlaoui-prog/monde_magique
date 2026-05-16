// 🏆 Système de Badges & Titres - Monde Magique

const BADGES = {
    // Badges d'exploration
    'tunisia_master': {
        id: 'tunisia_master',
        name: 'سيد تونس',
        icon: '🏜️',
        description: 'أكمل مرحلة تونس بنجاح',
        rarity: 'common',
        color: '#2ecc71'
    },
    'maghreb_explorer': {
        id: 'maghreb_explorer',
        name: 'مستكشف المغرب العربي',
        icon: '🐪',
        description: 'أكمل جميع دول المغرب العربي',
        rarity: 'uncommon',
        color: '#f39c12'
    },
    'african_adventurer': {
        id: 'african_adventurer',
        name: 'مغامر أفريقيا',
        icon: '🦁',
        description: 'اكتشف جميع مناطق أفريقيا',
        rarity: 'rare',
        color: '#e74c3c'
    },
    
    // Badges de compétences
    'geography_genius': {
        id: 'geography_genius',
        name: 'عبقري الجغرافيا',
        icon: '🧠',
        description: 'أجب عن 500 سؤال بشكل صحيح',
        rarity: 'epic',
        color: '#9b59b6'
    },
    'quiz_champion': {
        id: 'quiz_champion',
        name: 'بطل الأسئلة',
        icon: '🏆',
        description: 'احصل على 100% في 5 مراحل',
        rarity: 'rare',
        color: '#f1c40f'
    },
    'speed_master': {
        id: 'speed_master',
        name: 'سيد السرعة',
        icon: '⚡',
        description: 'أجب عن 10 أسئلة في أقل من دقيقة',
        rarity: 'uncommon',
        color: '#3498db'
    },
    
    // Badges légendaires
    'world_explorer': {
        id: 'world_explorer',
        name: 'مستكشف العالم',
        icon: '🌍',
        description: 'أكمل جولة حول العالم السحرية',
        rarity: 'legendary',
        color: '#e67e22'
    },
    'legendary_explorer': {
        id: 'legendary_explorer',
        name: 'المستكشف الأسطوري',
        icon: '👑',
        description: 'احصل على 100% في جميع المراحل',
        rarity: 'legendary',
        color: '#c0392b'
    },
    
    // Badges de collection
    'coin_collector': {
        id: 'coin_collector',
        name: 'جامع النقود',
        icon: '💰',
        description: 'اجمع 10,000 قطعة نقدية',
        rarity: 'rare',
        color: '#f39c12'
    },
    'diamond_hunter': {
        id: 'diamond_hunter',
        name: 'صياد الماس',
        icon: '💎',
        description: 'اجمع 500 ماسة',
        rarity: 'epic',
        color: '#3498db'
    },
    
    // Badges sociaux
    'friend_inviter': {
        id: 'friend_inviter',
        name: 'مدعو الأصدقاء',
        icon: '👥',
        description: 'ادعُ 5 أصدقاء للعب',
        rarity: 'uncommon',
        color: '#1abc9c'
    },
    'helpful_friend': {
        id: 'helpful_friend',
        name: 'صديق مفيد',
        icon: '🤝',
        description: 'ساعد 10 أصدقاء في التحديات',
        rarity: 'rare',
        color: '#16a085'
    },
    
    // Badges de temps
    'daily_player': {
        id: 'daily_player',
        name: 'لاعب يومي',
        icon: '📅',
        description: 'العب 7 أيام متتالية',
        rarity: 'uncommon',
        color: '#27ae60'
    },
    'dedicated_learner': {
        id: 'dedicated_learner',
        name: 'متعلم مخلص',
        icon: '📚',
        description: 'العب 30 يوماً متتالياً',
        rarity: 'epic',
        color: '#8e44ad'
    }
};

const TITLES = {
    'beginner': { name: 'مبتدئ', color: '#95a5a6', minLevel: 0 },
    'explorer': { name: 'مستكشف', color: '#3498db', minLevel: 5 },
    'adventurer': { name: 'مغامر', color: '#2ecc71', minLevel: 10 },
    'navigator': { name: 'ملاح', color: '#f39c12', minLevel: 20 },
    'champion': { name: 'بطل', color: '#e74c3c', minLevel: 30 },
    'master': { name: 'خبير', color: '#9b59b6', minLevel: 40 },
    'legend': { name: 'أسطورة', color: '#e67e22', minLevel: 50 }
};

class BadgesSystem {
    constructor() {
        this.unlockedBadges = this.loadBadges();
        this.currentTitle = this.loadTitle();
    }

    loadBadges() {
        return JSON.parse(localStorage.getItem('badges') || '[]');
    }

    loadTitle() {
        return localStorage.getItem('currentTitle') || 'beginner';
    }

    saveBadges() {
        localStorage.setItem('badges', JSON.stringify(this.unlockedBadges));
    }

    saveTitle(title) {
        this.currentTitle = title;
        localStorage.setItem('currentTitle', title);
    }

    // فتح شارة
    unlockBadge(badgeId) {
        if (this.unlockedBadges.includes(badgeId)) {
            return false;
        }

        this.unlockedBadges.push(badgeId);
        this.saveBadges();
        
        // عرض إشعار فتح الشارة
        this.showBadgeUnlock(badgeId);
        
        // تأثير صوتي
        playSound('badge_unlock');
        
        return true;
    }

    // عرض فتح الشارة
    showBadgeUnlock(badgeId) {
        const badge = BADGES[badgeId];
        if (!badge) return;

        const modal = document.createElement('div');
        modal.className = 'badge-unlock-modal';
        modal.innerHTML = `
            <div class="badge-unlock-content animate__animated animate__zoomIn">
                <div class="badge-unlock-bg ${badge.rarity}"></div>
                <div class="badge-icon-large" style="color: ${badge.color}">${badge.icon}</div>
                <h2>🎉 شارة جديدة!</h2>
                <h3>${badge.name}</h3>
                <p>${badge.description}</p>
                <div class="badge-rarity ${badge.rarity}">
                    ${this.getRarityText(badge.rarity)}
                </div>
                <button onclick="closeBadgeModal()" class="btn-primary">رائع! 🌟</button>
            </div>
        `;

        document.body.appendChild(modal);
        
        // إضافة confetti
        this.showConfetti();
    }

    getRarityText(rarity) {
        const rarityNames = {
            'common': 'شائع',
            'uncommon': 'غير شائع',
            'rare': 'نادر',
            'epic': 'ملحمي',
            'legendary': 'أسطوري'
        };
        return rarityNames[rarity] || 'شائع';
    }

    // عرض جميع الشارات
    renderBadges() {
        const container = document.getElementById('badges-container');
        if (!container) return;

        let html = '<div class="badges-grid">';

        Object.values(BADGES).forEach(badge => {
            const unlocked = this.unlockedBadges.includes(badge.id);
            html += `
                <div class="badge-item ${unlocked ? 'unlocked' : 'locked'} ${badge.rarity}">
                    <div class="badge-icon" style="color: ${badge.color}">
                        ${unlocked ? badge.icon : '🔒'}
                    </div>
                    <div class="badge-name">${badge.name}</div>
                    <div class="badge-desc">${badge.description}</div>
                    ${unlocked ? '<div class="badge-unlocked-mark">✓</div>' : ''}
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    // عرض الألقاب
    renderTitles() {
        const container = document.getElementById('titles-container');
        if (!container) return;

        const userLevel = parseInt(localStorage.getItem('userLevel') || 1);
        
        let html = '<div class="titles-list">';

        Object.entries(TITLES).forEach(([key, title]) => {
            const unlocked = userLevel >= title.minLevel;
            const isCurrent = key === this.currentTitle;
            
            html += `
                <div class="title-item ${unlocked ? 'unlocked' : 'locked'} ${isCurrent ? 'current' : ''}" 
                     onclick="${unlocked ? `badgesSystem.selectTitle('${key}')` : ''}">
                    <div class="title-name" style="color: ${title.color}">
                        ${title.name}
                    </div>
                    <div class="title-requirement">
                        المستوى ${title.minLevel}+
                    </div>
                    ${isCurrent ? '<div class="title-check">✓ مستخدم</div>' : ''}
                    ${!unlocked ? '<div class="title-lock">🔒</div>' : ''}
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    // اختيار لقب
    selectTitle(titleKey) {
        this.saveTitle(titleKey);
        this.renderTitles();
        showNotification(`تم اختيار اللقب: ${TITLES[titleKey].name}`, 'success');
    }

    // عرض confetti
    showConfetti() {
        const colors = ['#f39c12', '#e74c3c', '#3498db', '#2ecc71', '#9b59b6'];
        for (let i = 0; i < 50; i++) {
            setTimeout(() => {
                this.createConfettiPiece(colors[Math.floor(Math.random() * colors.length)]);
            }, i * 20);
        }
    }

    createConfettiPiece(color) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.backgroundColor = color;
        confetti.style.animationDuration = (Math.random() * 2 + 1) + 's';
        document.body.appendChild(confetti);
        
        setTimeout(() => confetti.remove(), 3000);
    }

    // الحصول على اللقب الحالي
    getCurrentTitle() {
        return TITLES[this.currentTitle];
    }

    // إحصائيات
    getBadgesStats() {
        const total = Object.keys(BADGES).length;
        const unlocked = this.unlockedBadges.length;
        const percentage = Math.round((unlocked / total) * 100);
        
        return {
            total,
            unlocked,
            percentage,
            locked: total - unlocked
        };
    }
}

// Instance globale
const badgesSystem = new BadgesSystem();

// Fonctions globales
function closeBadgeModal() {
    const modal = document.querySelector('.badge-unlock-modal');
    if (modal) modal.remove();
}

function playSound(soundName) {
    // À implémenter avec vos fichiers audio
    console.log('Playing sound:', soundName);
}

// Initialiser
document.addEventListener('DOMContentLoaded', () => {
    badgesSystem.renderBadges();
    badgesSystem.renderTitles();
});
