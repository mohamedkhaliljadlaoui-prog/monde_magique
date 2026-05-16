// 🎯 Système de Missions & Quêtes - Monde Magique

class MissionsSystem {
    constructor() {
        this.missions = {
            daily: [
                {
                    id: 'daily_1',
                    title: 'زر 3 دول',
                    description: 'قم بزيارة 3 دول مختلفة اليوم',
                    icon: '🌍',
                    type: 'visit',
                    target: 3,
                    progress: 0,
                    rewards: { coins: 50, xp: 100 },
                    completed: false
                },
                {
                    id: 'daily_2',
                    title: 'أجب عن 5 أسئلة',
                    description: 'أجب بشكل صحيح عن 5 أسئلة',
                    icon: '🧠',
                    type: 'quiz',
                    target: 5,
                    progress: 0,
                    rewards: { coins: 100, xp: 200 },
                    completed: false
                },
                {
                    id: 'daily_3',
                    title: 'اجمع 200 قطعة نقدية',
                    description: 'اجمع 200 قطعة نقدية اليوم',
                    icon: '💰',
                    type: 'collect',
                    target: 200,
                    progress: 0,
                    rewards: { diamonds: 5, xp: 150 },
                    completed: false
                }
            ],
            quests: [
                {
                    id: 'quest_1',
                    title: 'جولة حول العالم السحرية',
                    description: 'أكمل جميع مراحل الرحلة',
                    icon: '🗺️',
                    stages: ['tunisia', 'maghreb', 'africa', 'europe', 'asia', 'namerica', 'samerica', 'oceania', 'poles', 'world'],
                    progress: 0,
                    rewards: { coins: 5000, diamonds: 100, xp: 10000, badge: 'world_explorer' },
                    completed: false
                },
                {
                    id: 'quest_2',
                    title: 'المستكشف الأسطوري',
                    description: 'احصل على 100% في جميع المراحل',
                    icon: '🏆',
                    type: 'perfect',
                    target: 10,
                    progress: 0,
                    rewards: { coins: 10000, diamonds: 200, xp: 20000, badge: 'legendary_explorer' },
                    completed: false
                },
                {
                    id: 'quest_3',
                    title: 'عبقري الجغرافيا',
                    description: 'أجب بشكل صحيح عن 500 سؤال',
                    icon: '🎓',
                    type: 'total_quiz',
                    target: 500,
                    progress: 0,
                    rewards: { coins: 3000, diamonds: 50, xp: 5000, badge: 'geography_genius' },
                    completed: false
                }
            ]
        };
    }

    // تحميل المهام من localStorage
    loadMissions() {
        const saved = localStorage.getItem('missions');
        if (saved) {
            const data = JSON.parse(saved);
            this.missions = data;
            this.checkDailyReset();
        }
    }

    // حفظ المهام
    saveMissions() {
        localStorage.setItem('missions', JSON.stringify(this.missions));
        localStorage.setItem('lastMissionCheck', new Date().toDateString());
    }

    // إعادة تعيين المهام اليومية
    checkDailyReset() {
        const lastCheck = localStorage.getItem('lastMissionCheck');
        const today = new Date().toDateString();
        
        if (lastCheck !== today) {
            this.missions.daily.forEach(mission => {
                mission.progress = 0;
                mission.completed = false;
            });
            this.saveMissions();
        }
    }

    // تحديث تقدم المهمة
    updateProgress(type, amount = 1) {
        let updated = false;

        // تحديث المهام اليومية
        this.missions.daily.forEach(mission => {
            if (!mission.completed && mission.type === type) {
                mission.progress += amount;
                if (mission.progress >= mission.target) {
                    mission.completed = true;
                    this.completeMission(mission);
                }
                updated = true;
            }
        });

        // تحديث المهام الكبرى
        this.missions.quests.forEach(quest => {
            if (!quest.completed && quest.type === type) {
                quest.progress += amount;
                if (quest.progress >= quest.target) {
                    quest.completed = true;
                    this.completeQuest(quest);
                }
                updated = true;
            }
        });

        if (updated) {
            this.saveMissions();
            this.renderMissions();
        }
    }

    // إكمال مهمة
    completeMission(mission) {
        // إضافة المكافآت
        if (mission.rewards.coins) {
            addCoins(mission.rewards.coins);
        }
        if (mission.rewards.diamonds) {
            addDiamonds(mission.rewards.diamonds);
        }
        if (mission.rewards.xp) {
            addXP(mission.rewards.xp);
        }

        // إشعار
        showNotification(`✅ مهمة مكتملة: ${mission.title}!`, 'success');
        
        // تأثير صوتي
        playSound('mission_complete');
    }

    // إكمال مهمة كبرى
    completeQuest(quest) {
        this.completeMission(quest);
        
        // منح الشارة
        if (quest.rewards.badge) {
            this.unlockBadge(quest.rewards.badge);
        }

        // احتفال خاص
        showCelebration('🎉 تهانينا! أكملت مهمة أسطورية! 🏆');
    }

    // فتح شارة
    unlockBadge(badgeId) {
        const badges = JSON.parse(localStorage.getItem('badges') || '[]');
        if (!badges.includes(badgeId)) {
            badges.push(badgeId);
            localStorage.setItem('badges', JSON.stringify(badges));
            showBadgeUnlock(badgeId);
        }
    }

    // عرض المهام
    renderMissions() {
        const container = document.getElementById('missions-container');
        if (!container) return;

        let html = `
            <div class="missions-section">
                <h3>🎯 المهام اليومية</h3>
                <div class="daily-missions">
                    ${this.missions.daily.map(m => this.renderMissionCard(m)).join('')}
                </div>
            </div>
            
            <div class="missions-section">
                <h3>🗺️ المهام الكبرى</h3>
                <div class="quests">
                    ${this.missions.quests.map(q => this.renderMissionCard(q, true)).join('')}
                </div>
            </div>
        `;

        container.innerHTML = html;
    }

    // بطاقة المهمة
    renderMissionCard(mission, isQuest = false) {
        const percentage = Math.min(100, (mission.progress / mission.target) * 100);
        const statusClass = mission.completed ? 'completed' : 'active';
        
        return `
            <div class="mission-card ${statusClass} ${isQuest ? 'quest-card' : ''}">
                <div class="mission-icon">${mission.icon}</div>
                <div class="mission-info">
                    <h4>${mission.title}</h4>
                    <p>${mission.description}</p>
                    <div class="mission-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${percentage}%"></div>
                        </div>
                        <span>${mission.progress} / ${mission.target}</span>
                    </div>
                    <div class="mission-rewards">
                        ${mission.rewards.coins ? `<span>💰 ${mission.rewards.coins}</span>` : ''}
                        ${mission.rewards.diamonds ? `<span>💎 ${mission.rewards.diamonds}</span>` : ''}
                        ${mission.rewards.xp ? `<span>⭐ ${mission.rewards.xp}</span>` : ''}
                    </div>
                </div>
                ${mission.completed ? '<div class="mission-check">✅</div>' : ''}
            </div>
        `;
    }
}

// Instance globale
const missionsSystem = new MissionsSystem();

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', () => {
    missionsSystem.loadMissions();
    missionsSystem.renderMissions();
});
