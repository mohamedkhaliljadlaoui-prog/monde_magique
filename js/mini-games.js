// 🎮 Mini-Jeux Interactifs - Monde Magique

class MiniGames {
    constructor() {
        this.games = {
            puzzle: this.initPuzzle.bind(this),
            flagMatch: this.initFlagMatch.bind(this),
            memoryGame: this.initMemoryGame.bind(this),
            findCountry: this.initFindCountry.bind(this)
        };
    }

    // 🧩 Puzzle de Carte
    initPuzzle(countryData) {
        const modal = this.createGameModal('لعبة البازل', `
            <div class="puzzle-game">
                <div class="puzzle-preview">
                    <img src="${countryData.image}" alt="${countryData.name}">
                </div>
                <div class="puzzle-pieces" id="puzzle-container"></div>
                <div class="puzzle-timer">⏱️ <span id="puzzle-time">0</span>ث</div>
            </div>
        `);

        this.generatePuzzlePieces(countryData, 9); // 3x3
    }

    generatePuzzlePieces(countryData, pieces) {
        const container = document.getElementById('puzzle-container');
        const gridSize = Math.sqrt(pieces);
        
        let positions = Array.from({length: pieces}, (_, i) => i);
        positions = this.shuffle(positions);

        positions.forEach((pos, index) => {
            const piece = document.createElement('div');
            piece.className = 'puzzle-piece';
            piece.draggable = true;
            piece.dataset.correctPos = pos;
            piece.dataset.currentPos = index;
            piece.style.backgroundImage = `url(${countryData.image})`;
            piece.style.backgroundSize = `${gridSize * 100}% ${gridSize * 100}%`;
            piece.style.backgroundPosition = `${(pos % gridSize) * (100 / (gridSize - 1))}% ${Math.floor(pos / gridSize) * (100 / (gridSize - 1))}%`;
            
            piece.addEventListener('dragstart', this.handleDragStart);
            piece.addEventListener('dragover', this.handleDragOver);
            piece.addEventListener('drop', this.handleDrop);
            
            container.appendChild(piece);
        });

        this.startPuzzleTimer();
    }

    // 🚩 مطابقة الأعلام
    initFlagMatch(countries) {
        const modal = this.createGameModal('طابق العلم مع الدولة', `
            <div class="flag-match-game">
                <div class="game-score">النقاط: <span id="match-score">0</span></div>
                <div class="flags-grid" id="flags-container"></div>
                <div class="countries-grid" id="countries-container"></div>
            </div>
        `);

        this.generateFlagMatchGame(countries);
    }

    generateFlagMatchGame(countries) {
        const flagsContainer = document.getElementById('flags-container');
        const countriesContainer = document.getElementById('countries-container');
        
        const shuffledCountries = this.shuffle([...countries]);
        
        shuffledCountries.forEach((country, index) => {
            // إنشاء بطاقة العلم
            const flagCard = document.createElement('div');
            flagCard.className = 'flag-card';
            flagCard.innerHTML = `<div class="flag-emoji">${country.flag}</div>`;
            flagCard.dataset.countryId = country.id;
            flagCard.addEventListener('click', () => this.selectFlag(flagCard));
            flagsContainer.appendChild(flagCard);

            // إنشاء بطاقة الدولة
            const countryCard = document.createElement('div');
            countryCard.className = 'country-card';
            countryCard.innerHTML = `<div class="country-name">${country.nameAr}</div>`;
            countryCard.dataset.countryId = country.id;
            countryCard.addEventListener('click', () => this.selectCountry(countryCard));
            countriesContainer.appendChild(countryCard);
        });
    }

    selectFlag(card) {
        document.querySelectorAll('.flag-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        this.selectedFlag = card.dataset.countryId;
        this.checkFlagMatch();
    }

    selectCountry(card) {
        document.querySelectorAll('.country-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        this.selectedCountry = card.dataset.countryId;
        this.checkFlagMatch();
    }

    checkFlagMatch() {
        if (this.selectedFlag && this.selectedCountry) {
            if (this.selectedFlag === this.selectedCountry) {
                // مطابقة صحيحة
                document.querySelectorAll(`[data-country-id="${this.selectedFlag}"]`).forEach(card => {
                    card.classList.add('matched');
                    card.classList.remove('selected');
                });
                
                const scoreEl = document.getElementById('match-score');
                scoreEl.textContent = parseInt(scoreEl.textContent) + 10;
                
                playSound('correct');
                this.createParticles('✨');
            } else {
                // مطابقة خاطئة
                playSound('wrong');
                setTimeout(() => {
                    document.querySelectorAll('.selected').forEach(card => {
                        card.classList.remove('selected');
                    });
                }, 500);
            }
            
            this.selectedFlag = null;
            this.selectedCountry = null;
            
            // التحقق من الفوز
            if (document.querySelectorAll('.matched').length === document.querySelectorAll('.flag-card').length * 2) {
                setTimeout(() => this.winFlagMatch(), 500);
            }
        }
    }

    // 🧠 لعبة الذاكرة
    initMemoryGame(theme) {
        const modal = this.createGameModal('لعبة الذاكرة', `
            <div class="memory-game">
                <div class="game-info">
                    <div>المحاولات: <span id="memory-moves">0</span></div>
                    <div>الوقت: <span id="memory-time">0</span>ث</div>
                </div>
                <div class="memory-grid" id="memory-container"></div>
            </div>
        `);

        this.generateMemoryGame(theme);
    }

    generateMemoryGame(theme) {
        const container = document.getElementById('memory-container');
        const cards = this.getMemoryCards(theme);
        const duplicatedCards = [...cards, ...cards];
        const shuffledCards = this.shuffle(duplicatedCards);

        shuffledCards.forEach((card, index) => {
            const cardEl = document.createElement('div');
            cardEl.className = 'memory-card';
            cardEl.innerHTML = `
                <div class="card-inner">
                    <div class="card-front">❓</div>
                    <div class="card-back">${card.icon}</div>
                </div>
            `;
            cardEl.dataset.cardId = card.id;
            cardEl.addEventListener('click', () => this.flipMemoryCard(cardEl));
            container.appendChild(cardEl);
        });

        this.memoryFlipped = [];
        this.memoryMoves = 0;
        this.startMemoryTimer();
    }

    getMemoryCards(theme) {
        const themes = {
            animals: [
                {id: 1, icon: '🦁'}, {id: 2, icon: '🐘'}, {id: 3, icon: '🦒'},
                {id: 4, icon: '🦓'}, {id: 5, icon: '🐪'}, {id: 6, icon: '🦘'},
                {id: 7, icon: '🐧'}, {id: 8, icon: '🐼'}
            ],
            monuments: [
                {id: 1, icon: '🗼'}, {id: 2, icon: '🗽'}, {id: 3, icon: '🕌'},
                {id: 4, icon: '⛩️'}, {id: 5, icon: '🏛️'}, {id: 6, icon: '🏰'},
                {id: 7, icon: '🗿'}, {id: 8, icon: '🕋'}
            ],
            flags: [
                {id: 1, icon: '🇹🇳'}, {id: 2, icon: '🇩🇿'}, {id: 3, icon: '🇲🇦'},
                {id: 4, icon: '🇪🇬'}, {id: 5, icon: '🇸🇦'}, {id: 6, icon: '🇦🇪'},
                {id: 7, icon: '🇯🇴'}, {id: 8, icon: '🇱🇧'}
            ]
        };

        return themes[theme] || themes.animals;
    }

    flipMemoryCard(card) {
        if (card.classList.contains('flipped') || this.memoryFlipped.length >= 2) {
            return;
        }

        card.classList.add('flipped');
        this.memoryFlipped.push(card);

        if (this.memoryFlipped.length === 2) {
            this.memoryMoves++;
            document.getElementById('memory-moves').textContent = this.memoryMoves;
            
            setTimeout(() => this.checkMemoryMatch(), 500);
        }
    }

    checkMemoryMatch() {
        const [card1, card2] = this.memoryFlipped;
        
        if (card1.dataset.cardId === card2.dataset.cardId) {
            card1.classList.add('matched');
            card2.classList.add('matched');
            playSound('correct');
            this.createParticles('⭐');
            
            // التحقق من الفوز
            if (document.querySelectorAll('.memory-card.matched').length === document.querySelectorAll('.memory-card').length) {
                setTimeout(() => this.winMemoryGame(), 500);
            }
        } else {
            setTimeout(() => {
                card1.classList.remove('flipped');
                card2.classList.remove('flipped');
            }, 500);
            playSound('wrong');
        }

        this.memoryFlipped = [];
    }

    // 🎯 ابحث عن الدولة
    initFindCountry(countries) {
        const modal = this.createGameModal('ابحث عن الدولة', `
            <div class="find-country-game">
                <div class="game-header">
                    <div class="target-country">ابحث عن: <span id="target-country-name"></span></div>
                    <div class="game-timer">⏱️ <span id="find-timer">30</span>ث</div>
                    <div class="game-score">النقاط: <span id="find-score">0</span></div>
                </div>
                <div class="world-map-mini" id="find-map"></div>
            </div>
        `);

        this.initFindCountryGame(countries);
    }

    initFindCountryGame(countries) {
        this.findCountries = countries;
        this.findScore = 0;
        this.findTimeLeft = 30;
        
        this.nextFindCountry();
        this.startFindTimer();
    }

    nextFindCountry() {
        if (this.findCountries.length === 0) {
            this.winFindCountry();
            return;
        }

        const randomIndex = Math.floor(Math.random() * this.findCountries.length);
        this.currentTargetCountry = this.findCountries[randomIndex];
        
        document.getElementById('target-country-name').textContent = this.currentTargetCountry.nameAr;
        
        // رسم الخريطة مع الدول
        this.renderFindMap();
    }

    // Helper: إنشاء modal اللعبة
    createGameModal(title, content) {
        const modal = document.createElement('div');
        modal.className = 'game-modal';
        modal.innerHTML = `
            <div class="game-modal-content animate__animated animate__zoomIn">
                <div class="game-header">
                    <h2>${title}</h2>
                    <button class="close-game" onclick="closeGameModal()">✕</button>
                </div>
                <div class="game-body">
                    ${content}
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        return modal;
    }

    // Helper: خلط عشوائي
    shuffle(array) {
        const newArray = [...array];
        for (let i = newArray.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
        }
        return newArray;
    }

    // Helper: إنشاء جزيئات
    createParticles(emoji) {
        for (let i = 0; i < 10; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.textContent = emoji;
            particle.style.left = Math.random() * window.innerWidth + 'px';
            particle.style.top = Math.random() * window.innerHeight + 'px';
            document.body.appendChild(particle);
            
            setTimeout(() => particle.remove(), 2000);
        }
    }

    // Timers
    startPuzzleTimer() {
        this.puzzleTime = 0;
        this.puzzleTimer = setInterval(() => {
            this.puzzleTime++;
            document.getElementById('puzzle-time').textContent = this.puzzleTime;
        }, 1000);
    }

    startMemoryTimer() {
        this.memoryTime = 0;
        this.memoryTimer = setInterval(() => {
            this.memoryTime++;
            document.getElementById('memory-time').textContent = this.memoryTime;
        }, 1000);
    }

    startFindTimer() {
        this.findTimer = setInterval(() => {
            this.findTimeLeft--;
            document.getElementById('find-timer').textContent = this.findTimeLeft;
            
            if (this.findTimeLeft <= 0) {
                clearInterval(this.findTimer);
                this.gameOverFind();
            }
        }, 1000);
    }

    // Win conditions
    winFlagMatch() {
        clearInterval(this.matchTimer);
        const score = document.getElementById('match-score').textContent;
        this.showGameResult('🎉 أحسنت!', `لقد حصلت على ${score} نقطة!`, score);
    }

    winMemoryGame() {
        clearInterval(this.memoryTimer);
        const moves = this.memoryMoves;
        const time = this.memoryTime;
        this.showGameResult('🏆 رائع!', `أكملت اللعبة في ${moves} محاولة و ${time} ثانية!`, 100 - moves);
    }

    winFindCountry() {
        clearInterval(this.findTimer);
        this.showGameResult('🌟 ممتاز!', `لقد وجدت جميع الدول! النقاط: ${this.findScore}`, this.findScore);
    }

    gameOverFind() {
        this.showGameResult('⏰ انتهى الوقت!', `النقاط النهائية: ${this.findScore}`, this.findScore);
    }

    showGameResult(title, message, score) {
        const resultDiv = document.createElement('div');
        resultDiv.className = 'game-result animate__animated animate__bounceIn';
        resultDiv.innerHTML = `
            <h2>${title}</h2>
            <p>${message}</p>
            <div class="result-buttons">
                <button onclick="miniGames.restartGame()" class="btn-primary">العب مرة أخرى</button>
                <button onclick="closeGameModal()" class="btn-secondary">إغلاق</button>
            </div>
        `;

        document.querySelector('.game-body').appendChild(resultDiv);
        
        // إضافة المكافآت
        addCoins(score * 10);
        addXP(score * 5);
        
        playSound('victory');
    }
}

// Instance globale
const miniGames = new MiniGames();

// Fonctions globales
function closeGameModal() {
    const modal = document.querySelector('.game-modal');
    if (modal) modal.remove();
}

function launchMiniGame(gameType, data) {
    if (miniGames.games[gameType]) {
        miniGames.games[gameType](data);
    }
}
