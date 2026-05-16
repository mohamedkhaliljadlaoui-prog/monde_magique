(function () {
    const tracks = [
        'assets/audio/m1.mp3',
        'assets/audio/m2.mp3',
        'assets/audio/m3.mp3'
    ];
    const storageKey = 'bgMusicEnabled';
    let audio = null;
    let index = 0;
    let enabled = true;
    const invalid = new Set();
    let waiting = false;
    let promptEl = null;

    function getButtons() {
        return document.querySelectorAll('[data-music-toggle]');
    }

    function updateButtons() {
        getButtons().forEach((button) => {
            const onLabel = button.getAttribute('data-music-on-label') || 'Music';
            const offLabel = button.getAttribute('data-music-off-label') || 'Muted';
            button.textContent = enabled ? onLabel : offLabel;
            button.setAttribute('aria-pressed', enabled ? 'true' : 'false');
        });
    }

    function buildAudio() {
        if (audio) {
            return;
        }
        audio = new Audio();
        audio.preload = 'auto';
        audio.volume = 0.25;
        audio.addEventListener('ended', nextTrack);
        audio.addEventListener('error', () => {
            invalid.add(tracks[index]);
            nextTrack();
        });
    }

    function loadTrack(nextIndex) {
        if (!tracks.length) {
            return false;
        }
        index = nextIndex;
        if (invalid.size >= tracks.length) {
            return false;
        }
        let attempts = 0;
        while (invalid.has(tracks[index]) && attempts < tracks.length) {
            index = (index + 1) % tracks.length;
            attempts += 1;
        }
        if (invalid.has(tracks[index])) {
            return false;
        }
        audio.src = tracks[index];
        return true;
    }

    function waitForGesture() {
        if (waiting) {
            return;
        }
        waiting = true;
        showPrompt();
        const handler = () => {
            waiting = false;
            hidePrompt();
            if (!enabled) {
                return;
            }
            playNow();
        };
        window.addEventListener('click', handler, { once: true, passive: true });
        window.addEventListener('keydown', handler, { once: true });
        window.addEventListener('touchstart', handler, { once: true, passive: true });
    }

    function playNow() {
        if (!enabled) {
            return;
        }
        buildAudio();
        if (!loadTrack(index)) {
            return;
        }
        const playPromise = audio.play();
        if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(() => {
                waitForGesture();
            });
        }
    }

    function nextTrack() {
        if (!enabled || !audio) {
            return;
        }
        index = (index + 1) % tracks.length;
        playNow();
    }

    function setEnabled(value) {
        enabled = value;
        localStorage.setItem(storageKey, enabled ? '1' : '0');
        if (enabled) {
            playNow();
        } else if (audio) {
            audio.pause();
        }
        updateButtons();
    }

    function showPrompt() {
        if (promptEl) {
            return;
        }
        promptEl = document.createElement('div');
        promptEl.textContent = 'Clique pour activer la musique';
        promptEl.style.position = 'fixed';
        promptEl.style.left = '50%';
        promptEl.style.bottom = '18px';
        promptEl.style.transform = 'translateX(-50%)';
        promptEl.style.zIndex = '10060';
        promptEl.style.padding = '10px 14px';
        promptEl.style.borderRadius = '14px';
        promptEl.style.background = 'rgba(255, 255, 255, 0.9)';
        promptEl.style.color = '#2d2f38';
        promptEl.style.fontWeight = 'bold';
        promptEl.style.boxShadow = '0 10px 24px rgba(0, 0, 0, 0.18)';
        promptEl.style.pointerEvents = 'none';
        document.body.appendChild(promptEl);
    }

    function hidePrompt() {
        if (promptEl && promptEl.parentNode) {
            promptEl.parentNode.removeChild(promptEl);
        }
        promptEl = null;
    }

    function init() {
        const stored = localStorage.getItem(storageKey);
        if (stored === '0') {
            enabled = false;
        }
        updateButtons();
        getButtons().forEach((button) => {
            button.addEventListener('click', () => {
                setEnabled(!enabled);
            });
        });
        if (enabled) {
            playNow();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
