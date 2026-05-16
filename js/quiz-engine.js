// quiz-engine.js - Moteur de quiz avancé
class QuizEngine {
    constructor(config) {
        this.config = config || {};
        this.currentQuestion = 0;
        this.score = 0;
        this.timeRemaining = 0;
        this.timer = null;
        this.questions = [];
        this.userAnswers = [];
        this.isActive = false;
        this.results = null;
        
        this.init();
    }
    
    init() {
        // Initialiser les événements
        this.setupEventListeners();
        
        // Charger les questions si fournies
        if (this.config.questions) {
            this.loadQuestions(this.config.questions);
        }
    }
    
    // Charger des questions
    loadQuestions(questions) {
        this.questions = questions.map((q, index) => ({
            id: q.id || `q${index + 1}`,
            text: q.question_fr || q.text,
            text_ar: q.question_ar,
            type: q.type || 'text',
            image: q.image_url,
            options: this.processOptions(q.options),
            correctAnswer: this.findCorrectAnswer(q.options),
            explanation: q.explanation_fr || q.explanation,
            explanation_ar: q.explanation_ar,
            funFact: q.fun_fact,
            difficulty: q.difficulty || 1,
            timeLimit: q.time_limit || 30,
            points: (q.difficulty || 1) * 10
        }));
        
        // Mélanger les options si configuré
        if (this.config.shuffleOptions) {
            this.shuffleOptions();
        }
    }
    
    processOptions(options) {
        return options.map((opt, idx) => ({
            id: opt.id || String.fromCharCode(65 + idx), // A, B, C, D
            text: opt.text,
            correct: opt.correct || false,
            image: opt.image_url
        }));
    }
    
    findCorrectAnswer(options) {
        const correctOption = options.find(opt => opt.correct);
        return correctOption ? (correctOption.id || options.indexOf(correctOption)) : null;
    }
    
    shuffleOptions() {
        this.questions.forEach(question => {
            const shuffled = [...question.options];
            for (let i = shuffled.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
            }
            
            // Mettre à jour l'ID correct après mélange
            const originalCorrectIndex = question.options.findIndex(opt => opt.correct);
            if (originalCorrectIndex !== -1) {
                question.correctAnswer = shuffled[originalCorrectIndex].id;
            }
            
            question.options = shuffled;
        });
    }
    
    // Démarrer un quiz
    startQuiz(quizConfig = {}) {
        this.currentQuestion = 0;
        this.score = 0;
        this.userAnswers = [];
        this.isActive = true;
        this.results = null;
        
        // Configurer le timer
        const totalTime = quizConfig.timeLimit || (this.questions.length * 30);
        this.timeRemaining = totalTime;
        this.startTimer();
        
        // Afficher la première question
        this.showQuestion(this.currentQuestion);
        
        // Émettre un événement de début
        this.emitEvent('quizStarted', {
            totalQuestions: this.questions.length,
            timeLimit: totalTime
        });
    }
    
    // Afficher une question
    showQuestion(index) {
        if (index >= this.questions.length) {
            this.endQuiz();
            return;
        }
        
        const question = this.questions[index];
        
        // Mettre à jour l'interface
        this.updateQuestionDisplay(question);
        
        // Réinitialiser la sélection
        this.clearSelection();
        
        // Démarrer le timer pour cette question
        this.startQuestionTimer(question.timeLimit);
        
        // Émettre un événement
        this.emitEvent('questionChanged', {
            questionNumber: index + 1,
            totalQuestions: this.questions.length,
            question: question
        });
    }
    
    updateQuestionDisplay(question) {
        const quizContainer = document.getElementById('quiz-container');
        if (!quizContainer) return;
        
        let html = `
            <div class="quiz-question" data-question-id="${question.id}">
                <div class="question-header">
                    <span class="question-number">Question ${this.currentQuestion + 1}/${this.questions.length}</span>
                    <span class="question-timer" id="question-timer">${question.timeLimit}s</span>
                </div>
                
                <div class="question-content">
                    <h3 class="question-text">${question.text}</h3>
        `;
        
        if (question.image) {
            html += `
                <div class="question-image-container">
                    <img src="${question.image}" alt="Question" class="question-image">
                </div>
            `;
        }
        
        html += `
                </div>
                
                <div class="question-options" id="question-options">
        `;
        
        // Générer les options
        question.options.forEach(option => {
            const optionClass = option.image ? 'option-with-image' : 'option-text-only';
            
            html += `
                <div class="quiz-option ${optionClass}" 
                     data-option-id="${option.id}"
                     onclick="quizEngine.selectOption('${option.id}')">
            `;
            
            if (option.image) {
                html += `
                    <div class="option-image">
                        <img src="${option.image}" alt="${option.text}">
                    </div>
                `;
            }
            
            html += `
                    <div class="option-text">${option.text}</div>
                    <div class="option-selector"></div>
                </div>
            `;
        });
        
        html += `
                </div>
                
                <div class="question-controls">
                    <button class="btn-previous" onclick="quizEngine.previousQuestion()" 
                            ${this.currentQuestion === 0 ? 'disabled' : ''}>
                        ⬅️ Précédent
                    </button>
                    <button class="btn-next" onclick="quizEngine.nextQuestion()">
                        ${this.currentQuestion === this.questions.length - 1 ? 'Terminer ✅' : 'Suivant ➡️'}
                    </button>
                </div>
            </div>
        `;
        
        quizContainer.innerHTML = html;
    }
    
    // Sélectionner une option
    selectOption(optionId) {
        if (!this.isActive) return;
        
        const question = this.questions[this.currentQuestion];
        const options = document.querySelectorAll('.quiz-option');
        
        // Désélectionner toutes les options
        options.forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Sélectionner l'option choisie
        const selectedOption = document.querySelector(`[data-option-id="${optionId}"]`);
        if (selectedOption) {
            selectedOption.classList.add('selected');
        }
        
        // Enregistrer la réponse
        this.userAnswers[this.currentQuestion] = {
            questionId: question.id,
            selectedOption: optionId,
            isCorrect: optionId === question.correctAnswer,
            timeTaken: question.timeLimit - this.currentQuestionTimeRemaining
        };
        
        // Émettre un événement
        this.emitEvent('optionSelected', {
            questionId: question.id,
            optionId: optionId,
            isCorrect: optionId === question.correctAnswer
        });
    }
    
    // Passer à la question suivante
    nextQuestion() {
        // Vérifier si une réponse a été sélectionnée
        if (this.userAnswers[this.currentQuestion] === undefined) {
            this.showWarning('Veuillez sélectionner une réponse avant de continuer.');
            return;
        }
        
        // Arrêter le timer de la question
        this.stopQuestionTimer();
        
        // Passer à la question suivante
        this.currentQuestion++;
        
        if (this.currentQuestion < this.questions.length) {
            this.showQuestion(this.currentQuestion);
        } else {
            this.endQuiz();
        }
    }
    
    // Revenir à la question précédente
    previousQuestion() {
        if (this.currentQuestion > 0) {
            this.stopQuestionTimer();
            this.currentQuestion--;
            this.showQuestion(this.currentQuestion);
        }
    }
    
    // Gestion du timer
    startTimer() {
        this.timer = setInterval(() => {
            this.timeRemaining--;
            
            // Mettre à jour l'affichage du timer principal
            this.updateMainTimer();
            
            if (this.timeRemaining <= 0) {
                this.timeUp();
            }
        }, 1000);
    }
    
    startQuestionTimer(limit) {
        this.currentQuestionTimeRemaining = limit;
        this.questionTimer = setInterval(() => {
            this.currentQuestionTimeRemaining--;
            
            const timerElement = document.getElementById('question-timer');
            if (timerElement) {
                timerElement.textContent = `${this.currentQuestionTimeRemaining}s`;
                
                // Changement de couleur selon le temps restant
                if (this.currentQuestionTimeRemaining <= 10) {
                    timerElement.classList.add('warning');
                }
                if (this.currentQuestionTimeRemaining <= 5) {
                    timerElement.classList.add('critical');
                }
            }
            
            if (this.currentQuestionTimeRemaining <= 0) {
                this.questionTimeUp();
            }
        }, 1000);
    }
    
    stopQuestionTimer() {
        if (this.questionTimer) {
            clearInterval(this.questionTimer);
        }
    }
    
    timeUp() {
        this.stopTimer();
        this.endQuiz();
        this.showWarning('Temps écoulé ! Le quiz est terminé.');
    }
    
    questionTimeUp() {
        this.stopQuestionTimer();
        this.userAnswers[this.currentQuestion] = {
            questionId: this.questions[this.currentQuestion].id,
            selectedOption: null,
            isCorrect: false,
            timeTaken: this.questions[this.currentQuestion].timeLimit,
            timeUp: true
        };
        
        this.showWarning('Temps écoulé pour cette question !');
        
        // Passer automatiquement à la question suivante après 2 secondes
        setTimeout(() => {
            this.nextQuestion();
        }, 2000);
    }
    
    // Terminer le quiz
    endQuiz() {
        this.isActive = false;
        this.stopTimer();
        this.stopQuestionTimer();
        
        // Calculer le score
        this.calculateResults();
        
        // Afficher les résultats
        this.showResults();
        
        // Émettre un événement
        this.emitEvent('quizCompleted', this.results);
        
        // Sauvegarder les résultats
        this.saveResults();
    }
    
    calculateResults() {
        const totalQuestions = this.questions.length;
        let correctAnswers = 0;
        let totalPoints = 0;
        let totalTime = 0;
        
        this.userAnswers.forEach((answer, index) => {
            const question = this.questions[index];
            
            if (answer && answer.isCorrect) {
                correctAnswers++;
                totalPoints += question.points;
            }
            
            if (answer && answer.timeTaken) {
                totalTime += answer.timeTaken;
            }
        });
        
        const percentage = (correctAnswers / totalQuestions) * 100;
        const averageTime = totalTime / totalQuestions;
        
        this.results = {
            totalQuestions,
            correctAnswers,
            incorrectAnswers: totalQuestions - correctAnswers,
            unanswered: this.userAnswers.filter(a => !a).length,
            score: totalPoints,
            maxScore: this.questions.reduce((sum, q) => sum + q.points, 0),
            percentage,
            totalTime: this.config.timeLimit ? (this.config.timeLimit - this.timeRemaining) : totalTime,
            averageTime,
            passed: percentage >= (this.config.passingScore || 60),
            answers: this.userAnswers,
            questions: this.questions
        };
    }
    
    showResults() {
        const quizContainer = document.getElementById('quiz-container');
        if (!quizContainer) return;
        
        const results = this.results;
        
        let html = `
            <div class="quiz-results">
                <div class="results-header">
                    <h2>📊 Résultats du Quiz</h2>
                    <div class="results-summary">
                        <div class="summary-item ${results.passed ? 'passed' : 'failed'}">
                            <span class="summary-label">Score</span>
                            <span class="summary-value">${results.percentage.toFixed(1)}%</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Réponses correctes</span>
                            <span class="summary-value">${results.correctAnswers}/${results.totalQuestions}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Temps total</span>
                            <span class="summary-value">${Math.floor(results.totalTime / 60)}:${(results.totalTime % 60).toString().padStart(2, '0')}</span>
                        </div>
                    </div>
                    
                    <div class="results-status ${results.passed ? 'status-passed' : 'status-failed'}">
                        ${results.passed ? '🎉 Félicitations ! Tu as réussi !' : '😔 Tu peux mieux faire !'}
                    </div>
                </div>
                
                <div class="detailed-results">
                    <h3>📝 Détail des réponses</h3>
        `;
        
        // Afficher chaque question avec la réponse de l'utilisateur
        this.questions.forEach((question, index) => {
            const userAnswer = results.answers[index];
            const isCorrect = userAnswer ? userAnswer.isCorrect : false;
            const userSelected = userAnswer ? question.options.find(opt => opt.id === userAnswer.selectedOption) : null;
            const correctOption = question.options.find(opt => opt.correct);
            
            html += `
                <div class="question-review ${isCorrect ? 'correct' : 'incorrect'}">
                    <div class="review-question">
                        <span class="review-number">Q${index + 1}</span>
                        <span class="review-text">${question.text}</span>
                    </div>
                    
                    <div class="review-answers">
                        <div class="user-answer">
                            <span class="answer-label">Ta réponse:</span>
                            <span class="answer-value ${isCorrect ? 'correct' : 'incorrect'}">
                                ${userSelected ? userSelected.text : 'Aucune réponse'}
                            </span>
                        </div>
                        
                        ${!isCorrect ? `
                            <div class="correct-answer">
                                <span class="answer-label">Bonne réponse:</span>
                                <span class="answer-value correct">${correctOption.text}</span>
                            </div>
                        ` : ''}
                        
                        ${question.explanation ? `
                            <div class="answer-explanation">
                                <span class="explanation-label">💡 Explication:</span>
                                <span class="explanation-text">${question.explanation}</span>
                            </div>
                        ` : ''}
                        
                        ${question.funFact ? `
                            <div class="fun-fact">
                                <span class="fact-label">🌟 Savais-tu ?</span>
                                <span class="fact-text">${question.funFact}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        
        html += `
                </div>
                
                <div class="results-actions">
                    <button class="btn-retry" onclick="quizEngine.retryQuiz()">
                        🔄 Réessayer le quiz
                    </button>
                    <button class="btn-continue" onclick="quizEngine.continueToNext()">
                        ${results.passed ? '🚀 Continuer l\'aventure' : '📚 Revoir la leçon'}
                    </button>
                    <button class="btn-share" onclick="quizEngine.shareResults()">
                        📤 Partager mes résultats
                    </button>
                </div>
            </div>
        `;
        
        quizContainer.innerHTML = html;
        
        // Animation de résultats
        this.animateResults();
    }
    
    animateResults() {
        const resultItems = document.querySelectorAll('.question-review');
        resultItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                item.style.transition = 'all 0.5s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 100 * index);
        });
    }
    
    retryQuiz() {
        this.startQuiz();
    }
    
    continueToNext() {
        if (this.results.passed) {
            // Émettre un événement pour continuer
            this.emitEvent('quizPassed', this.results);
            
            // Rediriger ou continuer
            if (this.config.onSuccess) {
                this.config.onSuccess(this.results);
            }
        } else {
            // Revoir la leçon
            this.emitEvent('quizFailed', this.results);
            
            if (this.config.onFailure) {
                this.config.onFailure(this.results);
            }
        }
    }
    
    shareResults() {
        const text = `J'ai obtenu ${this.results.percentage.toFixed(1)}% au quiz sur Monde Magique ! 🎮`;
        
        if (navigator.share) {
            navigator.share({
                title: 'Mes résultats Monde Magique',
                text: text,
                url: window.location.href
            });
        } else {
            // Fallback pour copier dans le presse-papier
            navigator.clipboard.writeText(text)
                .then(() => this.showSuccess('Résultats copiés !'))
                .catch(() => this.showInfo('Partage non supporté sur cet appareil'));
        }
    }
    
    saveResults() {
        if (!this.results) return;
        
        // Sauvegarder localement
        const quizHistory = JSON.parse(localStorage.getItem('quizHistory') || '[]');
        quizHistory.push({
            date: new Date().toISOString(),
            quizId: this.config.quizId,
            results: this.results
        });
        
        localStorage.setItem('quizHistory', JSON.stringify(quizHistory));
        
        // Envoyer au serveur si connecté
        if (window.app && window.app.user) {
            this.sendResultsToServer();
        }
    }
    
    async sendResultsToServer() {
        try {
            const response = await fetch('/api/quiz/results', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${window.app.user.token}`
                },
                body: JSON.stringify({
                    quizId: this.config.quizId,
                    stageId: this.config.stageId,
                    stationId: this.config.stationId,
                    results: this.results,
                    timestamp: new Date().toISOString()
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                console.log('Résultats sauvegardés sur le serveur');
            }
        } catch (error) {
            console.error('Erreur sauvegarde résultats:', error);
        }
    }
    
    // Événements
    setupEventListeners() {
        // Écouter les événements clavier
        document.addEventListener('keydown', (e) => {
            if (!this.isActive) return;
            
            switch (e.key) {
                case '1':
                case '2':
                case '3':
                case '4':
                    const optionIndex = parseInt(e.key) - 1;
                    const options = document.querySelectorAll('.quiz-option');
                    if (options[optionIndex]) {
                        options[optionIndex].click();
                    }
                    break;
                    
                case 'ArrowRight':
                    this.nextQuestion();
                    break;
                    
                case 'ArrowLeft':
                    this.previousQuestion();
                    break;
                    
                case 'Enter':
                    this.nextQuestion();
                    break;
            }
        });
    }
    
    emitEvent(eventName, data) {
        const event = new CustomEvent(`quiz:${eventName}`, { detail: data });
        window.dispatchEvent(event);
    }
    
    // Utilitaires
    showWarning(message) {
        const warning = document.createElement('div');
        warning.className = 'quiz-warning';
        warning.innerHTML = `
            <span>⚠️ ${message}</span>
            <button onclick="this.parentElement.remove()">×</button>
        `;
        
        document.querySelector('.quiz-container').appendChild(warning);
        
        setTimeout(() => {
            warning.classList.add('fade-out');
            setTimeout(() => warning.remove(), 300);
        }, 3000);
    }
    
    showSuccess(message) {
        const success = document.createElement('div');
        success.className = 'quiz-success';
        success.innerHTML = `
            <span>✅ ${message}</span>
            <button onclick="this.parentElement.remove()">×</button>
        `;
        
        document.querySelector('.quiz-container').appendChild(success);
        
        setTimeout(() => {
            success.classList.add('fade-out');
            setTimeout(() => success.remove(), 300);
        }, 3000);
    }
    
    clearSelection() {
        const options = document.querySelectorAll('.quiz-option');
        options.forEach(opt => opt.classList.remove('selected'));
    }
    
    stopTimer() {
        if (this.timer) {
            clearInterval(this.timer);
        }
    }
    
    updateMainTimer() {
        const timerElement = document.getElementById('quiz-timer');
        if (timerElement) {
            const minutes = Math.floor(this.timeRemaining / 60);
            const seconds = this.timeRemaining % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (this.timeRemaining <= 60) {
                timerElement.classList.add('warning');
            }
        }
    }
}

// Initialisation globale
window.QuizEngine = QuizEngine;

// Instance par défaut
let quizEngine = null;

document.addEventListener('DOMContentLoaded', () => {
    // Initialiser le moteur de quiz si un conteneur existe
    if (document.getElementById('quiz-container')) {
        quizEngine = new QuizEngine();
        window.quizEngine = quizEngine;
    }
});