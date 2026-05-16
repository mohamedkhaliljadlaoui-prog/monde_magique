// ===================================
// TTS.JS - Text-to-Speech System
// ===================================

class TextToSpeech {
    constructor() {
        this.synth = window.speechSynthesis;
        this.voices = [];
        this.currentLanguage = 'ar-SA';
        this.rate = 1.0;
        this.pitch = 1.0;
        this.volume = 1.0;
        this.enabled = true;
        
        this.init();
    }

    /**
     * Initialize TTS system
     */
    init() {
        if ('speechSynthesis' in window) {
            // Load voices
            this.loadVoices();
            
            // Reload voices on change (Chrome)
            if (this.synth.onvoiceschanged !== undefined) {
                this.synth.onvoiceschanged = () => this.loadVoices();
            }
        } else {
            console.warn('Text-to-Speech not supported in this browser');
            this.enabled = false;
        }
    }

    /**
     * Load available voices
     */
    loadVoices() {
        this.voices = this.synth.getVoices();
        console.log('Available voices:', this.voices.length);
    }

    /**
     * Speak text
     */
    speak(text, options = {}) {
        if (!this.enabled || !text) return;
        
        // Cancel any ongoing speech
        this.stop();
        
        const utterance = new SpeechSynthesisUtterance(text);
        
        // Apply settings
        utterance.lang = options.lang || this.currentLanguage;
        utterance.rate = options.rate || this.rate;
        utterance.pitch = options.pitch || this.pitch;
        utterance.volume = options.volume || this.volume;
        
        // Select voice
        const voice = this.getVoice(utterance.lang);
        if (voice) {
            utterance.voice = voice;
        }
        
        // Callbacks
        if (options.onStart) {
            utterance.onstart = options.onStart;
        }
        
        if (options.onEnd) {
            utterance.onend = options.onEnd;
        }
        
        if (options.onError) {
            utterance.onerror = options.onError;
        }
        
        // Speak
        this.synth.speak(utterance);
    }

    /**
     * Get appropriate voice for language
     */
    getVoice(lang) {
        // Try to find exact match
        let voice = this.voices.find(v => v.lang === lang);
        
        // Fallback to language prefix match
        if (!voice) {
            const langPrefix = lang.split('-')[0];
            voice = this.voices.find(v => v.lang.startsWith(langPrefix));
        }
        
        // Final fallback to any voice
        if (!voice && this.voices.length > 0) {
            voice = this.voices[0];
        }
        
        return voice;
    }

    /**
     * Stop speaking
     */
    stop() {
        if (this.synth.speaking) {
            this.synth.cancel();
        }
    }

    /**
     * Pause speaking
     */
    pause() {
        if (this.synth.speaking) {
            this.synth.pause();
        }
    }

    /**
     * Resume speaking
     */
    resume() {
        if (this.synth.paused) {
            this.synth.resume();
        }
    }

    /**
     * Check if currently speaking
     */
    isSpeaking() {
        return this.synth.speaking;
    }

    /**
     * Set language
     */
    setLanguage(lang) {
        this.currentLanguage = lang;
    }

    /**
     * Set speech rate
     */
    setRate(rate) {
        this.rate = Math.max(0.1, Math.min(10, rate));
    }

    /**
     * Set pitch
     */
    setPitch(pitch) {
        this.pitch = Math.max(0, Math.min(2, pitch));
    }

    /**
     * Set volume
     */
    setVolume(volume) {
        this.volume = Math.max(0, Math.min(1, volume));
    }

    /**
     * Enable/disable TTS
     */
    setEnabled(enabled) {
        this.enabled = enabled;
        if (!enabled) {
            this.stop();
        }
    }

    /**
     * Get list of available languages
     */
    getAvailableLanguages() {
        const languages = new Set();
        this.voices.forEach(voice => {
            languages.add(voice.lang);
        });
        return Array.from(languages);
    }
}

// Guide Speech Manager
class GuideSpeechManager {
    constructor() {
        this.tts = new TextToSpeech();
        this.guideGender = localStorage.getItem('guide_gender') || 'boy';
        this.guideName = localStorage.getItem('guide_name') || 'تيو';
        
        // Adjust voice based on gender
        if (this.guideGender === 'girl') {
            this.tts.setPitch(1.2); // Higher pitch for female voice
        } else {
            this.tts.setPitch(0.9); // Lower pitch for male voice
        }
    }

    /**
     * Guide speaks greeting
     */
    speakGreeting() {
        const greetings = [
            `مرحباً! أنا ${this.guideName}، مرشدك في هذه المغامرة`,
            `أهلاً بك! دعنا نستكشف العالم معاً`,
            `مرحباً صديقي! مستعد للمغامرة؟`
        ];
        
        const greeting = greetings[Math.floor(Math.random() * greetings.length)];
        this.speak(greeting);
    }

    /**
     * Guide speaks stage introduction
     */
    speakStageIntro(stageName) {
        const text = `مرحباً بك في مرحلة ${stageName}! هل أنت مستعد للتعلم والاستكشاف؟`;
        this.speak(text);
    }

    /**
     * Guide speaks encouragement
     */
    speakEncouragement() {
        const messages = [
            'أحسنت! استمر في التقدم!',
            'رائع جداً! أنت تتعلم بسرعة!',
            'ممتاز! أنا فخور بك!',
            'عمل رائع! استمر هكذا!'
        ];
        
        const message = messages[Math.floor(Math.random() * messages.length)];
        this.speak(message);
    }

    /**
     * Guide speaks congratulations
     */
    speakCongratulations() {
        const messages = [
            'مبروك! لقد أكملت المرحلة بنجاح!',
            'رائع! أنت مستكشف ممتاز!',
            'أحسنت! لقد فزت بالمكافآت!'
        ];
        
        const message = messages[Math.floor(Math.random() * messages.length)];
        this.speak(message);
    }

    /**
     * Guide speaks hint
     */
    speakHint(hint) {
        const text = `دعني أساعدك: ${hint}`;
        this.speak(text);
    }

    /**
     * Speak with guide's voice
     */
    speak(text) {
        this.tts.speak(text, {
            lang: 'ar-SA',
            onStart: () => {
                this.showSpeakingAnimation();
            },
            onEnd: () => {
                this.hideSpeakingAnimation();
            }
        });
    }

    /**
     * Show guide speaking animation
     */
    showSpeakingAnimation() {
        const guideElement = document.querySelector('.guide-character');
        if (guideElement) {
            guideElement.classList.add('speaking');
        }
    }

    /**
     * Hide guide speaking animation
     */
    hideSpeakingAnimation() {
        const guideElement = document.querySelector('.guide-character');
        if (guideElement) {
            guideElement.classList.remove('speaking');
        }
    }

    /**
     * Stop speaking
     */
    stop() {
        this.tts.stop();
        this.hideSpeakingAnimation();
    }
}

// Initialize global TTS instances
const TTS = new TextToSpeech();
const GuideVoice = new GuideSpeechManager();

// Export
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { TextToSpeech, GuideSpeechManager };
}
