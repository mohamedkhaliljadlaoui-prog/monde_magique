(function () {
    const THEME_KEY = 'cartoon_theme_mode';
    const SOUND_KEY = 'cartoon_sound';

    const audioState = {
        ctx: null,
        trainOsc: null,
        trainGain: null,
        birdTimer: null
    };

    function getThemeLabel(button, isNight) {
        if (!button) return '';
        const dayLabel = button.getAttribute('data-day-label') || 'Day';
        const nightLabel = button.getAttribute('data-night-label') || 'Night';
        return isNight ? nightLabel : dayLabel;
    }

    function getSoundLabel(button, isOn) {
        if (!button) return '';
        const onLabel = button.getAttribute('data-sound-on-label') || 'Sound On';
        const offLabel = button.getAttribute('data-sound-off-label') || 'Sound Off';
        return isOn ? onLabel : offLabel;
    }

    function applyTheme(mode) {
        const isNight = mode === 'night';
        document.body.classList.toggle('theme-night', isNight);
        localStorage.setItem(THEME_KEY, mode);

        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            button.textContent = getThemeLabel(button, isNight);
        });
    }

    function toggleTheme() {
        const isNight = document.body.classList.contains('theme-night');
        applyTheme(isNight ? 'day' : 'night');
    }

    function ensureAudioContext() {
        if (!audioState.ctx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            audioState.ctx = AudioContext ? new AudioContext() : null;
        }
    }

    function chirpBird() {
        if (!audioState.ctx || audioState.ctx.state !== 'running') return;
        const ctx = audioState.ctx;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(900, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(1300, ctx.currentTime + 0.12);
        gain.gain.setValueAtTime(0.001, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.04, ctx.currentTime + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.18);
        osc.connect(gain).connect(ctx.destination);
        osc.start();
        osc.stop(ctx.currentTime + 0.2);
    }

    function startTrain() {
        if (!audioState.ctx) return;
        const ctx = audioState.ctx;
        audioState.trainOsc = ctx.createOscillator();
        audioState.trainGain = ctx.createGain();
        audioState.trainOsc.type = 'sawtooth';
        audioState.trainOsc.frequency.value = 85;
        audioState.trainGain.gain.value = 0.02;
        audioState.trainOsc.connect(audioState.trainGain).connect(ctx.destination);
        audioState.trainOsc.start();
    }

    function startAudio() {
        ensureAudioContext();
        if (!audioState.ctx) return;
        audioState.ctx.resume();
        if (!audioState.trainOsc) startTrain();
        if (!audioState.birdTimer) {
            audioState.birdTimer = setInterval(chirpBird, 3000);
        }
        localStorage.setItem(SOUND_KEY, 'on');
        updateSoundButtons(true);
    }

    function stopAudio() {
        if (audioState.trainOsc) {
            audioState.trainOsc.stop();
            audioState.trainOsc.disconnect();
            audioState.trainOsc = null;
        }
        if (audioState.trainGain) {
            audioState.trainGain.disconnect();
            audioState.trainGain = null;
        }
        if (audioState.birdTimer) {
            clearInterval(audioState.birdTimer);
            audioState.birdTimer = null;
        }
        if (audioState.ctx) {
            audioState.ctx.suspend();
        }
        localStorage.setItem(SOUND_KEY, 'off');
        updateSoundButtons(false);
    }

    function toggleSound() {
        const isOn = audioState.ctx && audioState.ctx.state === 'running';
        if (isOn) {
            stopAudio();
        } else {
            startAudio();
        }
    }

    function updateSoundButtons(isOn) {
        document.querySelectorAll('[data-sound-toggle]').forEach((button) => {
            button.textContent = getSoundLabel(button, isOn);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem(THEME_KEY) || 'day';
        applyTheme(savedTheme);

        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            button.addEventListener('click', toggleTheme);
        });

        document.querySelectorAll('[data-sound-toggle]').forEach((button) => {
            button.addEventListener('click', toggleSound);
        });

        const soundPref = localStorage.getItem(SOUND_KEY) === 'on';
        updateSoundButtons(soundPref);
    });
})();
