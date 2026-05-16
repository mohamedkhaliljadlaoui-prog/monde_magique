/**
 * Assistant Animé - Personnage guide avec text-to-speech
 * Affiche des messages, parle en français, guide l'utilisateur
 */

class AnimatedAssistant {
    constructor() {
        this.isVisible = false;
        this.isSpeaking = false;
        this.language = localStorage.getItem('assistantLanguage') || 'fr'; // 'fr' or 'ar'
        
        this.messages = {
            welcome: {
                fr: "Bienvenue! Je suis ton assistant. Clique sur un stage pour commencer!",
                ar: "أهلا وسهلا! أنا مساعدك. انقر على مرحلة للبدء!"
            },
            stage_start: {
                fr: "Tu commences un nouveau stage. Bonne chance!",
                ar: "تبدأ مرحلة جديدة. حظا موفقا!"
            },
            video_done: {
                fr: "Super! Tu as regardé la vidéo. Passe à l'étape suivante!",
                ar: "ممتاز! لقد شاهدت الفيديو. انتقل إلى الخطوة التالية!"
            },
            quiz_start: {
                fr: "Voici un quiz. Réponds aux questions!",
                ar: "إليك اختبار. أجب على الأسئلة!"
            },
            quiz_win: {
                fr: "Excellent! Tu as réussi!",
                ar: "ممتاز! لقد نجحت!"
            },
            quiz_fail: {
                fr: "Pas grave, tu peux réessayer!",
                ar: "لا بأس، يمكنك المحاولة مرة أخرى!"
            },
            stage_complete: {
                fr: "Tu as terminé le stage! Tu as gagné des diamants!",
                ar: "لقد أنهيت المرحلة! لقد حصلت على الماس!"
            },
            help: {
                fr: "Besoin d'aide? Je peux te donner un indice!",
                ar: "هل تحتاج إلى مساعدة؟ يمكنني إعطاؤك تلميح!"
            }
        };
    }
    
    /**
     * Changer la langue (fr ou ar)
     */
    setLanguage(lang) {
        if (lang === 'fr' || lang === 'ar') {
            this.language = lang;
            localStorage.setItem('assistantLanguage', lang);
        }
    }
    
    /**
     * Obtenir le message dans la langue courante
     */
    getMessage(messageKey) {
        const msg = this.messages[messageKey];
        if (!msg) return messageKey;
        return msg[this.language] || msg.fr;
    }

    /**
     * Initialiser l'assistant dans la page
     */
    init() {
        if (document.getElementById('assistant-bubble')) return; // Déjà initié

        const html = `
            <div id="assistant-bubble" class="assistant-container" style="display: none;">
                <div class="assistant-head">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='45' fill='%236366F1'/%3E%3Ccircle cx='35' cy='40' r='5' fill='white'/%3E%3Ccircle cx='65' cy='40' r='5' fill='white'/%3E%3Cpath d='M 30 60 Q 50 75 70 60' stroke='white' stroke-width='2' fill='none'/%3E%3C/svg%3E" alt="Assistant">
                </div>
                <div class="assistant-bubble">
                    <p class="assistant-text"></p>
                    <button class="assistant-btn-speak" title="Écouter">🔊</button>
                    <button class="assistant-btn-close" title="Fermer">✕</button>
                </div>
            </div>

            <button id="assistant-toggle-btn" class="assistant-toggle" title="Assistant">
                💬
            </button>

            <style>
                .assistant-container {
                    position: fixed;
                    bottom: 80px;
                    right: 20px;
                    z-index: 1000;
                    display: flex;
                    gap: 10px;
                    align-items: flex-end;
                    animation: slideUp 0.3s ease-out;
                }

                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .assistant-head {
                    width: 60px;
                    height: 60px;
                    flex-shrink: 0;
                }

                .assistant-head img {
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    animation: bounce 2s infinite;
                }

                @keyframes bounce {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-8px); }
                }

                .assistant-bubble {
                    background: linear-gradient(135deg, #E8F5E9 0%, #F1F8E9 100%);
                    border: 2px solid #4CAF50;
                    border-radius: 15px;
                    padding: 12px 15px;
                    max-width: 250px;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                }

                .assistant-text {
                    margin: 0;
                    font-size: 0.9rem;
                    color: #333;
                    line-height: 1.4;
                }

                .assistant-bubble button {
                    background: #4CAF50;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    padding: 6px 12px;
                    cursor: pointer;
                    font-size: 0.85rem;
                    transition: all 0.2s;
                }

                .assistant-bubble button:hover {
                    background: #45a049;
                    transform: scale(1.05);
                }

                .assistant-toggle {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
                    color: white;
                    border: none;
                    font-size: 1.5rem;
                    cursor: pointer;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                    transition: all 0.3s;
                    z-index: 999;
                }

                .assistant-toggle:hover {
                    transform: scale(1.1);
                    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
                }

                .assistant-toggle.active {
                    background: #FF9800;
                }
            </style>
        `;

        document.body.insertAdjacentHTML('beforeend', html);

        // Event listeners
        document.getElementById('assistant-toggle-btn').addEventListener('click', () => this.toggle());
        document.querySelector('.assistant-btn-close').addEventListener('click', () => this.hide());
        document.querySelector('.assistant-btn-speak').addEventListener('click', () => this.speak());
    }

    /**
     * Afficher message
     */
    show(messageKey) {
        if (!document.getElementById('assistant-bubble')) {
            this.init();
        }

        const message = this.getMessage(messageKey);
        const bubble = document.getElementById('assistant-bubble');
        const textEl = document.querySelector('.assistant-text');

        textEl.textContent = message;
        textEl.dir = this.language === 'ar' ? 'rtl' : 'ltr';
        bubble.style.display = 'flex';
        this.isVisible = true;

        // Auto-hide après 5 secondes
        setTimeout(() => this.hide(), 5000);
    }

    /**
     * Afficher/masquer
     */
    toggle() {
        if (this.isVisible) {
            this.hide();
        } else {
            this.show('help');
        }
    }

    /**
     * Masquer
     */
    hide() {
        const bubble = document.getElementById('assistant-bubble');
        if (bubble) bubble.style.display = 'none';
        this.isVisible = false;
    }

    /**
     * Parler le message (text-to-speech)
     */
    speak() {
        if (this.isSpeaking) return;

        const text = document.querySelector('.assistant-text').textContent;
        const utterance = new SpeechSynthesisUtterance(text);
        
        // Utiliser la langue appropriée
        utterance.lang = this.language === 'ar' ? 'ar-SA' : 'fr-FR';
        utterance.rate = 1;
        utterance.pitch = this.language === 'ar' ? 1 : 1.2;
        utterance.volume = 1;

        utterance.onstart = () => {
            this.isSpeaking = true;
            document.querySelector('.assistant-btn-speak').style.opacity = '0.5';
        };

        utterance.onend = () => {
            this.isSpeaking = false;
            document.querySelector('.assistant-btn-speak').style.opacity = '1';
        };

        window.speechSynthesis.speak(utterance);
    }
}

// Initialiser assistant global
window.assistant = new AnimatedAssistant();
window.assistant.init();
