class GeographicStages {
    constructor() {
        this.stages = {
            1: {
                id: 1,
                title: "Découverte de la Tunisie",
                title_ar: "اكتشاف تونس",
                region: "tunisia",
                coordinates: [34.0, 9.0],
                zoom: 7,
                mapStyle: "tunisia-style",
                logos: ["tunisia-flag", "carthage", "medina"],
                decorations: [
                    { type: "palm-tree", coordinates: [36.8, 10.1] },
                    { type: "camel", coordinates: [34.0, 8.0] },
                    { type: "olive-tree", coordinates: [35.0, 9.5] }
                ],
                guideDialogue: "Allons découvrir notre beau pays la Tunisie !",
                guidePath: [[34.0, 9.0], [36.8, 10.1], [35.7, 10.9]]
            },
            2: {
                id: 2,
                title: "Voyage au Maghreb",
                title_ar: "رحلة إلى المغرب العربي",
                region: "maghreb",
                coordinates: [32.0, 0.0],
                zoom: 5,
                mapStyle: "maghreb-style",
                countries: ["Tunisie", "Algérie", "Maroc", "Libye", "Mauritanie"],
                logos: ["maghreb-logo", "atlas-mountains", "sahara"],
                decorations: [
                    { type: "atlas-mountain", coordinates: [31.0, -7.0] },
                    { type: "sahara-dunes", coordinates: [25.0, 0.0] },
                    { type: "kasbah", coordinates: [31.6, -7.9] }
                ],
                guideDialogue: "Explorons le Maghreb, terre de déserts et de montagnes !",
                guidePath: [[32.0, 0.0], [31.0, -7.0], [36.7, 3.0]]
            },
            3: {
                id: 3,
                title: "L'Afrique Magnifique",
                title_ar: "أفريقيا الرائعة",
                region: "africa",
                coordinates: [0.0, 20.0],
                zoom: 3,
                mapStyle: "africa-style",
                logos: ["africa-continent", "kilimanjaro", "pyramids"],
                decorations: [
                    { type: "pyramid", coordinates: [29.9, 31.2] },
                    { type: "kilimanjaro", coordinates: [-3.0, 37.3] },
                    { type: "savanna", coordinates: [-2.0, 34.0] },
                    { type: "victoria-falls", coordinates: [-17.9, 25.8] }
                ],
                guideDialogue: "Bienvenue en Afrique, berceau de l'humanité !",
                guidePath: [[0.0, 20.0], [29.9, 31.2], [-3.0, 37.3]]
            },
            4: {
                id: 4,
                title: "L'Europe et ses Cultures",
                title_ar: "أوروبا وثقافاتها",
                region: "europe",
                coordinates: [50.0, 10.0],
                zoom: 4,
                mapStyle: "europe-style",
                logos: ["europe-flag", "eiffel-tower", "colosseum"],
                decorations: [
                    { type: "eiffel-tower", coordinates: [48.8584, 2.2945] },
                    { type: "colosseum", coordinates: [41.8902, 12.4922] },
                    { type: "big-ben", coordinates: [51.5007, -0.1246] },
                    { type: "sagrada-familia", coordinates: [41.4036, 2.1744] }
                ],
                guideDialogue: "Direction l'Europe, continent des arts et de l'histoire !",
                guidePath: [[50.0, 10.0], [48.8584, 2.2945], [41.8902, 12.4922]]
            },
            5: {
                id: 5,
                title: "Les Mystères de l'Asie",
                title_ar: "أسرار آسيا",
                region: "asia",
                coordinates: [30.0, 100.0],
                zoom: 3,
                mapStyle: "asia-style",
                logos: ["asia-continent", "great-wall", "taj-mahal"],
                decorations: [
                    { type: "great-wall", coordinates: [40.4, 116.0] },
                    { type: "taj-mahal", coordinates: [27.1751, 78.0421] },
                    { type: "mount-fuji", coordinates: [35.3606, 138.7274] },
                    { type: "angkor-wat", coordinates: [13.4125, 103.8660] }
                ],
                guideDialogue: "Partons à la découverte de l'Asie, terre de traditions !",
                guidePath: [[30.0, 100.0], [27.1751, 78.0421], [35.3606, 138.7274]]
            },
            6: {
                id: 6,
                title: "L'Amérique du Nord",
                title_ar: "أمريكا الشمالية",
                region: "north-america",
                coordinates: [45.0, -100.0],
                zoom: 3,
                mapStyle: "north-america-style",
                logos: ["north-america", "statue-liberty", "grand-canyon"],
                decorations: [
                    { type: "statue-liberty", coordinates: [40.6892, -74.0445] },
                    { type: "grand-canyon", coordinates: [36.0566, -112.1251] },
                    { type: "niagara-falls", coordinates: [43.0799, -79.0747] },
                    { type: "chichen-itza", coordinates: [20.6843, -88.5678] }
                ],
                guideDialogue: "En route pour l'Amérique du Nord, terre de grands espaces !",
                guidePath: [[45.0, -100.0], [40.6892, -74.0445], [36.0566, -112.1251]]
            },
            7: {
                id: 7,
                title: "L'Amérique du Sud",
                title_ar: "أمريكا الجنوبية",
                region: "south-america",
                coordinates: [-20.0, -60.0],
                zoom: 3,
                mapStyle: "south-america-style",
                logos: ["south-america", "machu-picchu", "amazon"],
                decorations: [
                    { type: "machu-picchu", coordinates: [-13.1631, -72.5450] },
                    { type: "christ-redeemer", coordinates: [-22.9519, -43.2105] },
                    { type: "amazon-river", coordinates: [-3.0, -60.0] },
                    { type: "andes-mountains", coordinates: [-32.0, -70.0] }
                ],
                guideDialogue: "Direction l'Amérique du Sud, continent de la biodiversité !",
                guidePath: [[-20.0, -60.0], [-13.1631, -72.5450], [-22.9519, -43.2105]]
            },
            8: {
                id: 8,
                title: "L'Océanie",
                title_ar: "أوقيانوسيا",
                region: "oceania",
                coordinates: [-25.0, 135.0],
                zoom: 3,
                mapStyle: "oceania-style",
                logos: ["oceania", "sydney-opera", "great-barrier-reef"],
                decorations: [
                    { type: "sydney-opera", coordinates: [-33.8568, 151.2153] },
                    { type: "great-barrier-reef", coordinates: [-18.0, 147.0] },
                    { type: "uluru", coordinates: [-25.3444, 131.0369] },
                    { type: "fiordland", coordinates: [-45.0, 167.0] }
                ],
                guideDialogue: "Partons pour l'Océanie, terre des kangourous et des koalas !",
                guidePath: [[-25.0, 135.0], [-33.8568, 151.2153], [-18.0, 147.0]]
            },
            9: {
                id: 9,
                title: "Les Pôles et l'Antarctique",
                title_ar: "القطبين وأنتاركتيكا",
                region: "polar",
                coordinates: [-75.0, 0.0],
                zoom: 2,
                mapStyle: "polar-style",
                logos: ["antarctica", "penguin", "iceberg"],
                decorations: [
                    { type: "penguin-colony", coordinates: [-77.5, 166.0] },
                    { type: "iceberg", coordinates: [-65.0, -60.0] },
                    { type: "northern-lights", coordinates: [78.0, 15.0] },
                    { type: "polar-bear", coordinates: [78.0, -40.0] }
                ],
                guideDialogue: "Brrr... découvrons les régions polaires et leurs animaux !",
                guidePath: [[-75.0, 0.0], [-77.5, 166.0], [78.0, -40.0]]
            },
            10: {
                id: 10,
                title: "Tour du Monde Final",
                title_ar: "رحلة حول العالم النهائية",
                region: "world",
                coordinates: [20.0, 0.0],
                zoom: 2,
                mapStyle: "world-final-style",
                logos: ["world-globe", "compass", "trophy"],
                decorations: [
                    { type: "all-flags", coordinates: [20.0, 0.0] },
                    { type: "golden-compass", coordinates: [0.0, 0.0] },
                    { type: "victory-trophy", coordinates: [40.0, 0.0] }
                ],
                guideDialogue: "Félicitations ! Tu as fait le tour du monde !",
                guidePath: [[20.0, 0.0], [0.0, 180.0], [-20.0, -180.0], [20.0, 0.0]]
            }
        };
    }

    // Obtenir les données d'un stage
    getStage(stageNumber) {
        return this.stages[stageNumber] || this.stages[1];
    }

    // Obtenir le stage suivant
    getNextStage(currentStage) {
        const nextStage = currentStage + 1;
        return nextStage <= 10 ? this.stages[nextStage] : null;
    }

    // Vérifier si un stage est débloqué
    isStageUnlocked(stageNumber, userProgress) {
        if (stageNumber === 1) return true;
        
        const previousStage = stageNumber - 1;
        const previousProgress = userProgress.find(p => p.stage === previousStage);
        
        return previousProgress && previousProgress.completed;
    }

    // Préparer la carte pour un stage
    prepareMapForStage(stageNumber, mapInstance) {
        const stage = this.getStage(stageNumber);
        
        // Zoom automatique sur la région
        mapInstance.setView(stage.coordinates, stage.zoom);
        
        // Appliquer le style de la région
        this.applyMapStyle(stage.region, mapInstance);
        
        // Ajouter les logos et décorations
        this.addDecorations(stage, mapInstance);
        
        // Animer le guide vers la région
        this.animateGuideToRegion(stage, mapInstance);
        
        return stage;
    }

    // Appliquer le style de la carte selon la région
    applyMapStyle(region, mapInstance) {
        // Supprimer les anciennes couches
        if (this.currentTileLayer) {
            mapInstance.removeLayer(this.currentTileLayer);
        }

        // Ajouter une nouvelle couche selon la région
        const tileLayers = {
            'tunisia': L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap - Tunisie'
            }),
            'maghreb': L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap - Maghreb'
            }),
            'africa': L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap - Afrique'
            }),
            'europe': L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap - Europe'
            }),
            'asia': L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png', {
                attribution: '© Stadia Maps - Asie'
            }),
            'north-america': L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© CARTO - Amérique du Nord'
            }),
            'south-america': L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenTopoMap - Amérique du Sud'
            }),
            'oceania': L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: '© Esri - Océanie'
            }),
            'polar': L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap - Pôles'
            }),
            'world': L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap - Monde'
            })
        };

        this.currentTileLayer = tileLayers[region] || tileLayers.world;
        this.currentTileLayer.addTo(mapInstance);

        // Ajouter une surcouche de région
        this.addRegionOverlay(region, mapInstance);
    }

    // Ajouter une surcouche pour la région
    addRegionOverlay(region, mapInstance) {
        // Définir les coordonnées des régions
        const regionBounds = {
            'tunisia': [[32.0, 7.0], [37.5, 12.0]],
            'maghreb': [[15.0, -20.0], [38.0, 20.0]],
            'africa': [[-35.0, -20.0], [38.0, 52.0]],
            'europe': [[35.0, -25.0], [72.0, 50.0]],
            'asia': [[-10.0, 25.0], [55.0, 180.0]],
            'north-america': [[15.0, -170.0], [72.0, -50.0]],
            'south-america': [[-56.0, -85.0], [13.0, -30.0]],
            'oceania': [[-50.0, 110.0], [0.0, 180.0]],
            'polar': [[-90.0, -180.0], [90.0, 180.0]]
        };

        const bounds = regionBounds[region];
        if (bounds) {
            // Ajouter un rectangle pour la région
            L.rectangle(bounds, {
                color: "#ff7800",
                weight: 3,
                fillOpacity: 0.1,
                className: `region-highlight ${region}`
            }).addTo(mapInstance);
        }
    }

    // Ajouter les décorations sur la carte
    addDecorations(stage, mapInstance) {
        // Supprimer les anciennes décorations
        if (this.decorationLayers) {
            this.decorationLayers.forEach(layer => mapInstance.removeLayer(layer));
        }

        this.decorationLayers = [];

        // Ajouter les logos de la région
        this.addRegionLogos(stage, mapInstance);

        // Ajouter les décorations spécifiques
        stage.decorations.forEach(decoration => {
            const icon = this.createDecorationIcon(decoration.type);
            const marker = L.marker(decoration.coordinates, { icon: icon });
            
            marker.addTo(mapInstance);
            this.decorationLayers.push(marker);

            // Ajouter un popup informatif
            marker.bindPopup(this.getDecorationPopup(decoration.type));
        });
    }

    // Créer des icônes de décoration
    createDecorationIcon(type) {
        const iconSize = [40, 40];
        const iconUrl = this.getDecorationIconUrl(type);
        
        return L.icon({
            iconUrl: iconUrl,
            iconSize: iconSize,
            iconAnchor: [20, 40],
            popupAnchor: [0, -40],
            className: `decoration-icon ${type}`
        });
    }

    // Obtenir l'URL de l'icône de décoration
    getDecorationIconUrl(type) {
        const iconMap = {
            'palm-tree': 'assets/icons/decorations/palm-tree.png',
            'camel': 'assets/icons/decorations/camel.png',
            'olive-tree': 'assets/icons/decorations/olive-tree.png',
            'atlas-mountain': 'assets/icons/decorations/mountain.png',
            'sahara-dunes': 'assets/icons/decorations/dunes.png',
            'kasbah': 'assets/icons/decorations/kasbah.png',
            'pyramid': 'assets/icons/decorations/pyramid.png',
            'kilimanjaro': 'assets/icons/decorations/kilimanjaro.png',
            'savanna': 'assets/icons/decorations/savanna.png',
            'victoria-falls': 'assets/icons/decorations/waterfall.png',
            'eiffel-tower': 'assets/icons/decorations/eiffel.png',
            'colosseum': 'assets/icons/decorations/colosseum.png',
            'big-ben': 'assets/icons/decorations/big-ben.png',
            'sagrada-familia': 'assets/icons/decorations/sagrada.png',
            'great-wall': 'assets/icons/decorations/great-wall.png',
            'taj-mahal': 'assets/icons/decorations/taj-mahal.png',
            'mount-fuji': 'assets/icons/decorations/fuji.png',
            'angkor-wat': 'assets/icons/decorations/angkor.png',
            'statue-liberty': 'assets/icons/decorations/statue.png',
            'grand-canyon': 'assets/icons/decorations/canyon.png',
            'niagara-falls': 'assets/icons/decorations/niagara.png',
            'chichen-itza': 'assets/icons/decorations/pyramid-maya.png',
            'machu-picchu': 'assets/icons/decorations/machu.png',
            'christ-redeemer': 'assets/icons/decorations/christ.png',
            'amazon-river': 'assets/icons/decorations/river.png',
            'andes-mountains': 'assets/icons/decorations/andes.png',
            'sydney-opera': 'assets/icons/decorations/opera.png',
            'great-barrier-reef': 'assets/icons/decorations/reef.png',
            'uluru': 'assets/icons/decorations/uluru.png',
            'fiordland': 'assets/icons/decorations/fiord.png',
            'penguin-colony': 'assets/icons/decorations/penguin.png',
            'iceberg': 'assets/icons/decorations/iceberg.png',
            'northern-lights': 'assets/icons/decorations/aurora.png',
            'polar-bear': 'assets/icons/decorations/polar-bear.png',
            'all-flags': 'assets/icons/decorations/flags.png',
            'golden-compass': 'assets/icons/decorations/compass.png',
            'victory-trophy': 'assets/icons/decorations/trophy.png'
        };

        return iconMap[type] || 'assets/icons/decorations/default.png';
    }

    // Obtenir le contenu du popup de décoration
    getDecorationPopup(type) {
        const popupTexts = {
            'palm-tree': "🌴 Palmier tunisien - Symbole du littoral méditerranéen",
            'camel': "🐫 Chameau du désert - Le 'vaisseau du désert'",
            'olive-tree': "🫒 Olivier - Arbre emblématique de la Tunisie",
            'pyramid': "🔺 Pyramides d'Égypte - Merveilles du monde antique",
            'eiffel-tower': "🗼 Tour Eiffel - Symbole de Paris et de la France",
            'taj-mahal': "🏰 Taj Mahal - Monument d'amour en Inde",
            'great-wall': "🧱 Grande Muraille de Chine - Plus longue construction humaine",
            'statue-liberty': "🗽 Statue de la Liberté - Symbole de New York",
            'machu-picchu': "⛰️ Machu Picchu - Cité perdue des Incas",
            'sydney-opera': "🎭 Opéra de Sydney - Chef-d'œuvre architectural",
            'penguin-colony': "🐧 Colonie de manchots - Habitants de l'Antarctique"
        };

        return popupTexts[type] || "Point d'intérêt géographique";
    }

    // Ajouter les logos de la région
    addRegionLogos(stage, mapInstance) {
        // Logo principal de la région
        const logoIcon = L.icon({
            iconUrl: `assets/logos/${stage.region}-logo.png`,
            iconSize: [100, 100],
            iconAnchor: [50, 100],
            className: 'region-logo'
        });

        const logoMarker = L.marker(stage.coordinates, { 
            icon: logoIcon,
            zIndexOffset: 1000
        });

        logoMarker.addTo(mapInstance);
        this.decorationLayers.push(logoMarker);

        // Ajouter un popup avec informations
        logoMarker.bindPopup(`
            <div class="region-popup">
                <h3>${stage.title}</h3>
                <p>${stage.guideDialogue}</p>
                <div class="region-facts">
                    ${this.getRegionFacts(stage.region)}
                </div>
            </div>
        `);
    }

    // Obtenir les faits sur la région
    getRegionFacts(region) {
        const facts = {
            'tunisia': `
                <ul>
                    <li>🇹🇳 Capitale: Tunis</li>
                    <li>🏖️ 1300 km de côtes</li>
                    <li>🌅 Site de Carthage (UNESCO)</li>
                    <li>🫒 3ème producteur d'huile d'olive</li>
                </ul>
            `,
            'maghreb': `
                <ul>
                    <li>🌍 Région d'Afrique du Nord</li>
                    <li>🏜️ Désert du Sahara</li>
                    <li>⛰️ Chaîne de l'Atlas</li>
                    <li>📜 Civilisation berbère</li>
                </ul>
            `,
            'europe': `
                <ul>
                    <li>🇪🇺 Union Européenne</li>
                    <li>🏛️ Berceau de la démocratie</li>
                    <li>🎨 Renaissance artistique</li>
                    <li>⚙️ Révolution industrielle</li>
                </ul>
            `
            // Ajouter les autres régions...
        };

        return facts[region] || "<p>Découvre les secrets de cette région !</p>";
    }

    // Animer le guide vers la région
    animateGuideToRegion(stage, mapInstance) {
        const guideElement = document.getElementById('animated-guide');
        if (!guideElement) return;

        // Position initiale (Tunisie)
        const startLatLng = L.latLng(34.0, 9.0);
        
        // Position finale (région du stage)
        const endLatLng = L.latLng(stage.coordinates[0], stage.coordinates[1]);
        
        // Convertir les coordonnées en pixels
        const startPoint = mapInstance.latLngToContainerPoint(startLatLng);
        const endPoint = mapInstance.latLngToContainerPoint(endLatLng);
        
        // Positionner le guide
        guideElement.style.left = startPoint.x + 'px';
        guideElement.style.top = startPoint.y + 'px';
        guideElement.style.display = 'block';

        // Animation du trajet
        this.animateGuideMovement(guideElement, startPoint, endPoint, stage);
    }

    // Animer le mouvement du guide
    animateGuideMovement(guideElement, startPoint, endPoint, stage) {
        const duration = 3000; // 3 secondes
        const startTime = Date.now();
        
        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Courbe d'animation (ease-in-out)
            const easedProgress = this.easeInOutQuad(progress);
            
            // Position actuelle
            const currentX = startPoint.x + (endPoint.x - startPoint.x) * easedProgress;
            const currentY = startPoint.y + (endPoint.y - startPoint.y) * easedProgress;
            
            // Mettre à jour la position
            guideElement.style.left = currentX + 'px';
            guideElement.style.top = currentY + 'px';
            
            // Faire tourner le guide vers la destination
            const angle = Math.atan2(endPoint.y - currentY, endPoint.x - currentX) * 180 / Math.PI;
            guideElement.style.transform = `rotate(${angle}deg)`;
            
            // Ajouter des traces de pas
            if (progress < 1) {
                this.createFootstep(currentX, currentY);
                requestAnimationFrame(animate);
            } else {
                // Arrivée à destination
                this.guideArrived(stage);
            }
        };
        
        animate();
    }

    // Créer des traces de pas
    createFootstep(x, y) {
        const footsteps = document.getElementById('footsteps-container') || 
                         this.createFootstepsContainer();
        
        const footstep = document.createElement('div');
        footstep.className = 'footstep';
        footstep.style.left = x + 'px';
        footstep.style.top = y + 'px';
        footstep.style.opacity = '0.7';
        
        footsteps.appendChild(footstep);
        
        // Disparaître progressivement
        setTimeout(() => {
            footstep.style.opacity = '0';
            setTimeout(() => footstep.remove(), 1000);
        }, 1000);
    }

    createFootstepsContainer() {
        const container = document.createElement('div');
        container.id = 'footsteps-container';
        container.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
        `;
        
        document.querySelector('.map-container').appendChild(container);
        return container;
    }

    // Animation d'arrivée
    guideArrived(stage) {
        const guideElement = document.getElementById('animated-guide');
        
        // Animation de saut
        guideElement.style.transition = 'transform 0.3s';
        guideElement.style.transform += ' translateY(-20px)';
        
        setTimeout(() => {
            guideElement.style.transform = guideElement.style.transform.replace(' translateY(-20px)', '');
        }, 300);
        
        // Dialogue du guide
        this.speakGuideDialogue(stage.guideDialogue);
        
        // Afficher les informations de la région
        this.showRegionInfo(stage);
    }

    // Faire parler le guide
    speakGuideDialogue(text) {
        const guideSpeech = document.getElementById('guide-speech') || 
                           this.createGuideSpeechElement();
        
        guideSpeech.textContent = text;
        guideSpeech.classList.add('visible');
        
        // Text-to-speech
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'fr-FR';
            utterance.rate = 0.9;
            speechSynthesis.speak(utterance);
        }
        
        // Cacher après 5 secondes
        setTimeout(() => {
            guideSpeech.classList.remove('visible');
        }, 5000);
    }

    createGuideSpeechElement() {
        const speech = document.createElement('div');
        speech.id = 'guide-speech';
        speech.style.cssText = `
            position: absolute;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 15px 25px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            max-width: 300px;
            text-align: center;
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s;
        `;
        
        document.querySelector('.map-container').appendChild(speech);
        return speech;
    }

    // Afficher les informations de la région
    showRegionInfo(stage) {
        const infoPanel = document.getElementById('region-info') || 
                         this.createRegionInfoPanel();
        
        infoPanel.innerHTML = `
            <div class="region-info-content">
                <h3>${stage.title}</h3>
                <p>${stage.guideDialogue}</p>
                <div class="region-stats">
                    ${this.getRegionStats(stage.region)}
                </div>
                <button onclick="startStage(${stage.id})" class="btn-start-stage">
                    🚀 Commencer ce stage
                </button>
            </div>
        `;
        
        infoPanel.classList.add('visible');
    }

    createRegionInfoPanel() {
        const panel = document.createElement('div');
        panel.id = 'region-info';
        panel.style.cssText = `
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.3);
            width: 300px;
            z-index: 2000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s;
        `;
        
        document.querySelector('.map-container').appendChild(panel);
        return panel;
    }

    // Obtenir les statistiques de la région
    getRegionStats(region) {
        const stats = {
            'tunisia': `
                <div class="stat-item">
                    <span class="stat-label">Superficie</span>
                    <span class="stat-value">163,610 km²</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Population</span>
                    <span class="stat-value">12 millions</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Langue</span>
                    <span class="stat-value">Arabe tunisien</span>
                </div>
            `,
            'europe': `
                <div class="stat-item">
                    <span class="stat-label">Superficie</span>
                    <span class="stat-value">10,180,000 km²</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Pays</span>
                    <span class="stat-value">44 pays</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Population</span>
                    <span class="stat-value">747 millions</span>
                </div>
            `
            // Ajouter les autres régions...
        };

        return stats[region] || '';
    }

    // Fonction d'easing
    easeInOutQuad(t) {
        return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
    }
}

// Export pour utilisation globale
window.GeographicStages = GeographicStages;