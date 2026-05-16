// ===================================
// TRAIN.JS - Train Animation System
// ===================================

class TrainAnimation {
    constructor(mapInstance) {
        this.map = mapInstance;
        this.trainElement = null;
        this.currentPosition = null;
        this.isMoving = false;
        this.speed = 2000; // milliseconds per segment
    }

    /**
     * Initialize train on map
     */
    init(startPosition) {
        this.trainElement = document.createElement('div');
        this.trainElement.className = 'train-marker';
        this.trainElement.innerHTML = `
            <img src="assets/images/vehicles/train.png" alt="Train" style="width: 100px; height: 60px;">
        `;
        
        this.currentPosition = startPosition;
        this.updatePosition(startPosition);
    }

    /**
     * Move train to destination
     */
    async moveTo(destination, callback) {
        if (this.isMoving) return;
        
        this.isMoving = true;
        const path = this.calculatePath(this.currentPosition, destination);
        
        for (let i = 0; i < path.length; i++) {
            await this.animateToPoint(path[i]);
        }
        
        this.currentPosition = destination;
        this.isMoving = false;
        
        if (callback) callback();
    }

    /**
     * Calculate bezier curve path
     */
    calculatePath(start, end) {
        const steps = 20;
        const path = [];
        
        // Create curved path using quadratic bezier
        const controlLat = (start.lat + end.lat) / 2 + (Math.random() - 0.5) * 10;
        const controlLng = (start.lng + end.lng) / 2 + (Math.random() - 0.5) * 10;
        
        for (let t = 0; t <= 1; t += 1/steps) {
            const lat = Math.pow(1-t, 2) * start.lat + 
                       2 * (1-t) * t * controlLat + 
                       Math.pow(t, 2) * end.lat;
            
            const lng = Math.pow(1-t, 2) * start.lng + 
                       2 * (1-t) * t * controlLng + 
                       Math.pow(t, 2) * end.lng;
            
            path.push({ lat, lng });
        }
        
        return path;
    }

    /**
     * Animate train to specific point
     */
    animateToPoint(point) {
        return new Promise(resolve => {
            const duration = this.speed / 20;
            
            // Calculate rotation angle
            if (this.currentPosition) {
                const angle = this.calculateAngle(this.currentPosition, point);
                this.trainElement.style.transform = `rotate(${angle}deg)`;
            }
            
            // Smooth transition
            this.trainElement.style.transition = `all ${duration}ms ease-in-out`;
            this.updatePosition(point);
            
            setTimeout(resolve, duration);
        });
    }

    /**
     * Calculate rotation angle between two points
     */
    calculateAngle(from, to) {
        const dy = to.lat - from.lat;
        const dx = to.lng - from.lng;
        return Math.atan2(dy, dx) * 180 / Math.PI;
    }

    /**
     * Update train position on map
     */
    updatePosition(position) {
        if (this.map && this.trainElement) {
            const point = this.map.latLngToContainerPoint([position.lat, position.lng]);
            this.trainElement.style.left = point.x + 'px';
            this.trainElement.style.top = point.y + 'px';
        }
    }

    /**
     * Show train with animation
     */
    show() {
        if (this.trainElement) {
            this.trainElement.style.display = 'block';
            this.trainElement.style.animation = 'trainAppear 0.5s ease-out';
        }
    }

    /**
     * Hide train
     */
    hide() {
        if (this.trainElement) {
            this.trainElement.style.display = 'none';
        }
    }

    /**
     * Update train vehicle type
     */
    setVehicle(vehicleType) {
        const vehicleImages = {
            bicycle: 'bicycle.png',
            scooter: 'scooter.png',
            car: 'car.png',
            train: 'train.png',
            plane: 'plane.png'
        };
        
        const imgElement = this.trainElement.querySelector('img');
        if (imgElement && vehicleImages[vehicleType]) {
            imgElement.src = `assets/images/vehicles/${vehicleImages[vehicleType]}`;
        }
    }

    /**
     * Add smoke effect
     */
    addSmokeEffect() {
        const smoke = document.createElement('div');
        smoke.className = 'train-smoke';
        smoke.style.cssText = `
            position: absolute;
            width: 20px;
            height: 20px;
            background: rgba(200, 200, 200, 0.6);
            border-radius: 50%;
            animation: smokeRise 2s ease-out forwards;
        `;
        
        this.trainElement.appendChild(smoke);
        
        setTimeout(() => smoke.remove(), 2000);
    }
}

// Keyframe animations for train
const trainAnimationStyles = `
    @keyframes trainAppear {
        from {
            opacity: 0;
            transform: scale(0.5);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes smokeRise {
        from {
            opacity: 0.6;
            transform: translateY(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateY(-50px) scale(2);
        }
    }
    
    .train-marker {
        position: absolute;
        z-index: 1000;
        pointer-events: none;
        transform-origin: center;
    }
`;

// Inject styles
const styleSheet = document.createElement('style');
styleSheet.textContent = trainAnimationStyles;
document.head.appendChild(styleSheet);

// Export
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TrainAnimation;
}
