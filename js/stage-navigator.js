/**
 * Navigation entre stages
 * Boutons "Découvrir Stage" qui ouvrent stage-{n}.html
 */

class StageNavigator {
    /**
     * Ouvrir un stage spécifique
     */
    static openStage(stageNumber, variant = 'english') {
        const stageMap = {
            1: 'stage-1-tunisia.html',
            2: 'stage-2-maghreb.html',
            3: variant === 'french' ? 'stage-3-afrique.html' : 'stage-3-africa.html',
            4: 'stage-4-europe.html',
            5: variant === 'french' ? 'stage-5-asie.html' : 'stage-5-asia.html',
            6: variant === 'french' ? 'stage-6-amérique-du-nord.html' : 'stage-6-namerica.html',
            7: variant === 'french' ? 'stage-7-amérique-du-sud.html' : 'stage-7-samerica.html',
            8: variant === 'french' ? 'stage-8-océanie.html' : 'stage-8-oceania.html',
            9: variant === 'french' ? 'stage-9-pôles.html' : 'stage-9-poles.html',
            10: variant === 'french' ? 'stage-10-vue-mondiale.html' : 'stage-10-world.html'
        };

        const file = stageMap[stageNumber];
        if (file) {
            window.location.href = `/monde-magique/${file}`;
        } else {
            console.error('Stage non trouvé:', stageNumber);
        }
    }

    /**
     * Créer un bouton "Découvrir Stage" avec onclick
     */
    static createStageButton(stageNumber, stageName, emoji = '🌍') {
        return `
            <button class="discover-stage-btn" onclick="StageNavigator.openStage(${stageNumber})">
                <span class="stage-btn-emoji">${emoji}</span>
                <span class="stage-btn-text">Découvrir Stage</span>
                <span class="stage-btn-name">${stageName}</span>
            </button>
        `;
    }

    /**
     * Initialiser tous les boutons "Découvrir Stage" dans la page
     */
    static initButtons() {
        const style = `
            <style>
                .discover-stage-btn {
                    background: linear-gradient(135deg, #D4A574 0%, #AA6B45 100%);
                    color: white;
                    border: 2px solid rgba(212, 165, 116, 0.3);
                    border-radius: 12px;
                    padding: 12px 20px;
                    font-size: 1rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 6px;
                    min-width: 140px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                }

                .discover-stage-btn:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
                    background: linear-gradient(135deg, #E8B880 0%, #BB7B52 100%);
                }

                .discover-stage-btn:active {
                    transform: translateY(-1px);
                }

                .stage-btn-emoji {
                    font-size: 1.5rem;
                }

                .stage-btn-text {
                    font-size: 0.85rem;
                    opacity: 0.95;
                }

                .stage-btn-name {
                    font-size: 0.9rem;
                    font-weight: bold;
                }

                @media (max-width: 768px) {
                    .discover-stage-btn {
                        min-width: 120px;
                        padding: 10px 15px;
                        font-size: 0.9rem;
                    }
                }
            </style>
        `;

        if (!document.querySelector('style[data-discover-stage]')) {
            const styleEl = document.createElement('style');
            styleEl.setAttribute('data-discover-stage', 'true');
            styleEl.innerHTML = style.replace('<style>', '').replace('</style>', '');
            document.head.appendChild(styleEl);
        }
    }
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', () => {
    StageNavigator.initButtons();
});

window.StageNavigator = StageNavigator;
