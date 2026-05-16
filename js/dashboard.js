// ===================================
// DASHBOARD.JS - Dashboard Controller
// ===================================

class DashboardController {
    constructor() {
        this.map = null;
        this.trainAnimation = null;
        this.currentStage = 1;
        this.stages = [];
        this.markers = [];
        this.userData = null;
        this.imageUrl = 'assets/images/map.png';
        this.imageSize = { width: 0, height: 0 };
        this.imageBounds = null;
    }

    /**
     * Initialize dashboard
     */
    async init() {
        // Check authentication
        Auth.protectPage();
        
        // Load user data
        await this.loadUserData();
        
        // Initialize map
        await this.initMap();
        
        // Load stages
        await this.loadStages();
        
        // Initialize train
        this.initTrain();
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Update UI
        this.updateUI();
        
        // Guide greeting
        setTimeout(() => {
            GuideVoice.speakGreeting();
        }, 1000);
    }

    /**
     * Load user data
     */
    async loadUserData() {
        try {
            const response = await API.get('api/progress.php', {
                action: 'getUserData',
                token: Auth.getToken()
            });

            if (response.success) {
                this.userData = response.data;
                this.currentStage = this.userData.current_stage || 1;
            }
        } catch (error) {
            console.error('Error loading user data:', error);
        }
    }

    /**
     * Initialize Leaflet map
     */
    initMap() {
        return new Promise((resolve) => {
            const image = new Image();

            image.onload = () => {
                this.imageSize = {
                    width: image.naturalWidth,
                    height: image.naturalHeight
                };

                this.imageBounds = [[0, 0], [this.imageSize.height, this.imageSize.width]];

                this.map = L.map('world-map', {
                    crs: L.CRS.Simple,
                    minZoom: -2,
                    maxZoom: 2,
                    zoomSnap: 0.25,
                    zoomDelta: 0.25,
                    zoomControl: true,
                    attributionControl: false
                });

                L.imageOverlay(this.imageUrl, this.imageBounds).addTo(this.map);
                this.map.fitBounds(this.imageBounds);
                this.map.setMaxBounds(this.imageBounds);
                this.map.options.maxBoundsViscosity = 1.0;

                resolve();
            };

            image.onerror = () => {
                console.error('Failed to load map image:', this.imageUrl);
                resolve();
            };

            image.src = this.imageUrl;
        });
    }

    /**
     * Load stages data
     */
    async loadStages() {
        try {
            const response = await API.get('api/content.php', {
                action: 'getAllStages'
            });

            if (response.success) {
                this.stages = response.data.map(stage => this.normalizeStage(stage));
                this.renderStageMarkers();
            }
        } catch (error) {
            console.error('Error loading stages:', error);
            // Use default stages
            this.stages = this.getDefaultStages().map(stage => this.normalizeStage(stage));
            this.renderStageMarkers();
        }
    }

    /**
     * Get default stages configuration
     */
    getDefaultStages() {
        return [
            { id: 1, name: 'تونس', icon: '🇹🇳', coordinates: [0.46, 0.692], zoom: 1.3, unlocked: true },
            { id: 2, name: 'المغرب العربي', icon: '🌍', coordinates: [0.506, 0.344], zoom: 1.05, unlocked: false },
            { id: 3, name: 'أفريقيا', icon: '🦁', coordinates: [0.556, 0.5], zoom: 0.9, unlocked: false },
            { id: 4, name: 'أوروبا', icon: '🇪🇺', coordinates: [0.528, 0.222], zoom: 1.2, unlocked: false },
            { id: 5, name: 'آسيا', icon: '🗾', coordinates: [0.792, 0.306], zoom: 1.0, unlocked: false },
            { id: 6, name: 'أمريكا الشمالية', icon: '🇺🇸', coordinates: [0.222, 0.278], zoom: 1.0, unlocked: false },
            { id: 7, name: 'أمريكا الجنوبية', icon: '🇧🇷', coordinates: [0.333, 0.583], zoom: 1.0, unlocked: false },
            { id: 8, name: 'أوقيانوسيا', icon: '🇦🇺', coordinates: [0.875, 0.639], zoom: 1.0, unlocked: false },
            { id: 9, name: 'القطبين', icon: '❄️', coordinates: [0.5, 0.111], zoom: 0.9, unlocked: false },
            { id: 10, name: 'جولة حول العالم', icon: '🏆', coordinates: [0.5, 0.389], zoom: 0.6, unlocked: false }
        ];
    }

    normalizeStage(stage) {
        const normalized = { ...stage };

        if (!normalized.coordinates) {
            if (typeof normalized.lat === 'number' && typeof normalized.lng === 'number') {
                normalized.coordinates = this.convertLatLngToNormalized(normalized.lat, normalized.lng);
            } else {
                normalized.coordinates = [0.5, 0.5];
            }
        }

        if (typeof normalized.zoom !== 'number') {
            normalized.zoom = 0.9;
        }

        return normalized;
    }

    convertLatLngToNormalized(lat, lng) {
        const x = (lng + 180) / 360;
        const y = (90 - lat) / 180;
        return [x, y];
    }

    toMapPoint(normalizedCoords) {
        const x = normalizedCoords[0] * this.imageSize.width;
        const y = normalizedCoords[1] * this.imageSize.height;
        return L.latLng(y, x);
    }

    getStageLatLng(stage) {
        if (!stage || !this.imageSize.width || !this.imageSize.height) {
            return L.latLng(0, 0);
        }

        return this.toMapPoint(stage.coordinates);
    }

    /**
     * Render stage markers on map
     */
    renderStageMarkers() {
        this.stages.forEach(stage => {
            const markerDiv = L.divIcon({
                className: `custom-marker-icon ${stage.unlocked ? '' : 'marker-locked'}`,
                html: stage.icon,
                iconSize: [60, 60]
            });

            const stageLatLng = this.getStageLatLng(stage);
            const marker = L.marker(stageLatLng, {
                icon: markerDiv
            }).addTo(this.map);

            // Popup content
            const popupContent = `
                <div class="stage-popup-content">
                    <div class="stage-popup-icon">${stage.icon}</div>
                    <div class="stage-popup-title">${stage.name}</div>
                    <div class="stage-popup-progress">
                        ${stage.unlocked ? 'متاح' : '🔒 مغلق'}
                    </div>
                    <button class="stage-popup-btn" 
                            onclick="Dashboard.enterStage(${stage.id})"
                            ${!stage.unlocked ? 'disabled' : ''}>
                        ${stage.unlocked ? 'ابدأ المرحلة' : 'مغلق'}
                    </button>
                </div>
            `;

            marker.bindPopup(popupContent);

            // Click event
            if (stage.unlocked) {
                marker.on('click', () => {
                    this.zoomToStage(stage);
                });
            }

            this.markers.push({ stage: stage.id, marker });
        });
    }

    /**
     * Initialize train animation
     */
    initTrain() {
        this.trainAnimation = new TrainAnimation(this.map);
        
        // Start at first stage
        const firstStage = this.stages[0];
        const firstStageLatLng = this.getStageLatLng(firstStage);
        this.trainAnimation.init({
            lat: firstStageLatLng.lat,
            lng: firstStageLatLng.lng
        });
        
        // Add train element to map
        this.map.getContainer().appendChild(this.trainAnimation.trainElement);
    }

    /**
     * Zoom to specific stage
     */
    zoomToStage(stage) {
        const stageLatLng = this.getStageLatLng(stage);
        this.map.flyTo(stageLatLng, stage.zoom || 1.0, {
            duration: 2
        });
        
        // Move train to stage
        if (this.trainAnimation) {
            this.trainAnimation.moveTo({
                lat: stageLatLng.lat,
                lng: stageLatLng.lng
            });
        }
    }

    /**
     * Enter stage
     */
    enterStage(stageId) {
        const stage = this.stages.find(s => s.id === stageId);
        
        if (!stage || !stage.unlocked) {
            return;
        }
        
        // Play transition animation
        Animations.pageTransitionOut().then(() => {
            window.location.href = `stage-${stageId}.html`;
        });
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Navigation buttons
        document.getElementById('btn-shop')?.addEventListener('click', () => {
            window.location.href = 'shop.html';
        });

        document.getElementById('btn-profile')?.addEventListener('click', () => {
            window.location.href = 'profile.html';
        });

        document.getElementById('btn-parent')?.addEventListener('click', () => {
            window.location.href = 'parent-dashboard.html';
        });

        document.getElementById('btn-logout')?.addEventListener('click', () => {
            Auth.logout();
        });
    }

    /**
     * Update UI with user data
     */
    updateUI() {
        if (!this.userData) return;

        // Update stats
        document.getElementById('user-coins').textContent = this.userData.coins || 0;
        document.getElementById('user-diamonds').textContent = this.userData.diamonds || 0;
        document.getElementById('user-xp').textContent = this.userData.xp || 0;
        
        // Update guide info
        document.getElementById('guide-name').textContent = this.userData.guide_name || 'تيو';
        document.getElementById('guide-level').textContent = this.userData.level || 1;
        
        // Update progress bar
        const xpForNextLevel = (this.userData.level || 1) * 100;
        const xpProgress = ((this.userData.xp || 0) % xpForNextLevel) / xpForNextLevel * 100;
        document.getElementById('level-progress-fill').style.width = xpProgress + '%';
        
        // Update guide avatar
        const guideGender = this.userData.guide_gender || 'boy';
        document.getElementById('user-avatar').src = `assets/images/guides/${guideGender}/idle.png`;
    }

    /**
     * Refresh dashboard data
     */
    async refresh() {
        await this.loadUserData();
        this.updateUI();
    }
}

// Create global instance
let Dashboard;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    Dashboard = new DashboardController();
    Dashboard.init();
});

// Export
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardController;
}
