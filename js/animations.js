// ===================================
// ANIMATIONS.JS - GSAP Animation Helpers
// ===================================

class AnimationController {
    constructor() {
        this.animations = new Map();
        this.timelines = new Map();
    }

    /**
     * Animate guide walking
     */
    guideWalk(element, fromX, toX, duration = 2) {
        const timeline = gsap.timeline({
            onComplete: () => {
                element.classList.remove('walking');
            }
        });

        element.classList.add('walking');

        timeline
            .to(element, {
                x: toX,
                duration: duration,
                ease: 'none'
            });

        return timeline;
    }

    /**
     * Guide jump animation
     */
    guideJump(element) {
        return gsap.timeline()
            .to(element, {
                y: -50,
                duration: 0.3,
                ease: 'power2.out'
            })
            .to(element, {
                y: 0,
                duration: 0.3,
                ease: 'bounce.out'
            });
    }

    /**
     * Guide celebration animation
     */
    guideCelebrate(element) {
        return gsap.timeline({ repeat: 2 })
            .to(element, {
                rotation: -15,
                duration: 0.2
            })
            .to(element, {
                rotation: 15,
                duration: 0.2
            })
            .to(element, {
                rotation: 0,
                duration: 0.2
            });
    }

    /**
     * Coin collection animation
     */
    animateCoinCollection(startElement, endElement, amount) {
        const coin = document.createElement('div');
        coin.className = 'animated-coin';
        coin.textContent = '🪙 +' + amount;
        coin.style.cssText = `
            position: fixed;
            font-size: 2rem;
            font-weight: bold;
            color: #f39c12;
            z-index: 10000;
            pointer-events: none;
        `;

        const startRect = startElement.getBoundingClientRect();
        const endRect = endElement.getBoundingClientRect();

        coin.style.left = startRect.left + 'px';
        coin.style.top = startRect.top + 'px';

        document.body.appendChild(coin);

        return gsap.timeline({
            onComplete: () => coin.remove()
        })
            .to(coin, {
                x: endRect.left - startRect.left,
                y: endRect.top - startRect.top,
                scale: 0.5,
                duration: 1,
                ease: 'power2.inOut'
            })
            .to(coin, {
                opacity: 0,
                duration: 0.2
            });
    }

    /**
     * Diamond collection animation
     */
    animateDiamondCollection(startElement, endElement, amount) {
        const diamond = document.createElement('div');
        diamond.className = 'animated-diamond';
        diamond.textContent = '💎 +' + amount;
        diamond.style.cssText = `
            position: fixed;
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
            z-index: 10000;
            pointer-events: none;
        `;

        const startRect = startElement.getBoundingClientRect();
        const endRect = endElement.getBoundingClientRect();

        diamond.style.left = startRect.left + 'px';
        diamond.style.top = startRect.top + 'px';

        document.body.appendChild(diamond);

        return gsap.timeline({
            onComplete: () => diamond.remove()
        })
            .to(diamond, {
                x: endRect.left - startRect.left,
                y: endRect.top - startRect.top,
                scale: 0.5,
                duration: 1,
                ease: 'power2.inOut'
            })
            .to(diamond, {
                opacity: 0,
                duration: 0.2
            });
    }

    /**
     * Level up animation
     */
    animateLevelUp(element, newLevel) {
        const levelUpDiv = document.createElement('div');
        levelUpDiv.className = 'level-up-animation';
        levelUpDiv.innerHTML = `
            <div style="font-size: 3rem; font-weight: bold; color: #f39c12;">
                ⭐ المستوى ${newLevel} ⭐
            </div>
            <div style="font-size: 1.5rem; margin-top: 10px;">
                مستوى جديد!
            </div>
        `;
        levelUpDiv.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            text-align: center;
            z-index: 10000;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.3);
        `;

        document.body.appendChild(levelUpDiv);

        return gsap.timeline({
            onComplete: () => levelUpDiv.remove()
        })
            .to(levelUpDiv, {
                scale: 1,
                duration: 0.5,
                ease: 'back.out(1.7)'
            })
            .to(levelUpDiv, {
                rotation: 360,
                duration: 1,
                ease: 'power2.inOut'
            })
            .to(levelUpDiv, {
                scale: 0,
                opacity: 0,
                duration: 0.3
            }, '+=1');
    }

    /**
     * Stage unlock animation
     */
    animateStageUnlock(markerElement) {
        const particles = [];
        
        // Create particles
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'unlock-particle';
            particle.textContent = '✨';
            particle.style.cssText = `
                position: absolute;
                font-size: 1.5rem;
                pointer-events: none;
            `;
            markerElement.appendChild(particle);
            particles.push(particle);
        }

        const timeline = gsap.timeline({
            onComplete: () => {
                particles.forEach(p => p.remove());
            }
        });

        particles.forEach((particle, i) => {
            const angle = (360 / particles.length) * i;
            const distance = 100;
            const x = Math.cos(angle * Math.PI / 180) * distance;
            const y = Math.sin(angle * Math.PI / 180) * distance;

            timeline.to(particle, {
                x: x,
                y: y,
                opacity: 0,
                scale: 2,
                duration: 1,
                ease: 'power2.out'
            }, 0);
        });

        timeline.to(markerElement, {
            scale: 1.3,
            duration: 0.3,
            yoyo: true,
            repeat: 1
        }, 0);

        return timeline;
    }

    /**
     * Quiz answer feedback animation
     */
    animateCorrectAnswer(element) {
        return gsap.timeline()
            .to(element, {
                backgroundColor: '#4caf50',
                scale: 1.1,
                duration: 0.2
            })
            .to(element, {
                scale: 1,
                duration: 0.2
            });
    }

    animateWrongAnswer(element) {
        return gsap.timeline()
            .to(element, {
                x: -10,
                duration: 0.1
            })
            .to(element, {
                x: 10,
                duration: 0.1
            })
            .to(element, {
                x: -10,
                duration: 0.1
            })
            .to(element, {
                x: 0,
                duration: 0.1
            })
            .to(element, {
                backgroundColor: '#e74c3c',
                duration: 0.2
            });
    }

    /**
     * Page transition animation
     */
    pageTransitionOut() {
        const overlay = document.createElement('div');
        overlay.id = 'page-transition';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
        `;
        overlay.innerHTML = `
            <div style="text-align: center; color: white;">
                <div style="font-size: 3rem;">🚂</div>
                <div style="font-size: 1.5rem; margin-top: 10px;">جار التحميل...</div>
            </div>
        `;

        document.body.appendChild(overlay);

        return gsap.to(overlay, {
            opacity: 1,
            duration: 0.5
        });
    }

    pageTransitionIn() {
        const overlay = document.getElementById('page-transition');
        if (overlay) {
            return gsap.to(overlay, {
                opacity: 0,
                duration: 0.5,
                onComplete: () => overlay.remove()
            });
        }
    }

    /**
     * Floating animation (for idle elements)
     */
    animateFloat(element, duration = 3) {
        return gsap.to(element, {
            y: -20,
            duration: duration,
            ease: 'sine.inOut',
            yoyo: true,
            repeat: -1
        });
    }

    /**
     * Pulse animation
     */
    animatePulse(element) {
        return gsap.to(element, {
            scale: 1.1,
            duration: 0.5,
            ease: 'sine.inOut',
            yoyo: true,
            repeat: -1
        });
    }

    /**
     * Stop all animations
     */
    stopAll() {
        gsap.killTweensOf('*');
        this.timelines.forEach(timeline => timeline.kill());
        this.timelines.clear();
    }
}

// Create global instance
const Animations = new AnimationController();

// Export
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AnimationController;
}
