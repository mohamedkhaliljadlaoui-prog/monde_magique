// Script to generate stage HTML files with naming convention v#, c#, p#
const fs = require('fs');
const path = require('path');

const stages = [
    {
        num: 3,
        name: 'Afrique',
        icon: '🦁',
        description: 'Découvre la richesse et la diversité de l\'Afrique!',
        startDiamonds: 200,
        startCoins: 1000,
        questions: [
            { q: 'Quel est le plus grand désert d\'Afrique?', opts: ['Sahara', 'Kalahari', 'Namib', 'Atacama'], correct: 'Sahara' },
            { q: 'Quelle est la plus grande ville d\'Afrique?', opts: ['Lagos', 'Le Caire', 'Johannesburg', 'Abuja'], correct: 'Lagos' },
            { q: 'Quel est le plus haut sommet d\'Afrique?', opts: ['Kilimandjaro', 'Crête Stanley', 'Montagne du Kenya', 'Mont Cameroun'], correct: 'Kilimandjaro' },
            { q: 'Combien de pays y a-t-il en Afrique?', opts: ['54', '48', '52', '50'], correct: '54' }
        ]
    },
    {
        num: 4,
        name: 'Europe',
        icon: '🏰',
        description: 'Explore les châteaux et montagnes d\'Europe!',
        startDiamonds: 250,
        startCoins: 1250,
        questions: [
            { q: 'Quelle est la capitale de la Suisse?', opts: ['Berne', 'Zurich', 'Genève', 'Interlaken'], correct: 'Berne' },
            { q: 'Quel est le plus haut sommet d\'Europe?', opts: ['Mont-Blanc', 'Mont Rose', 'Mont Fuji', 'Elbrus'], correct: 'Elbrus' },
            { q: 'Combien de pays font partie de l\'Union européenne?', opts: ['27', '25', '28', '24'], correct: '27' },
            { q: 'Quelle est la plus grande ville d\'Europe?', opts: ['Istanbul', 'Rome', 'Moscou', 'Paris'], correct: 'Moscou' }
        ]
    },
    {
        num: 5,
        name: 'Asie',
        icon: '🏯',
        description: 'Plonge dans la mystique et la beauté de l\'Asie!',
        startDiamonds: 300,
        startCoins: 1500,
        questions: [
            { q: 'Quel est le plus haut sommet du monde?', opts: ['Everest', 'K2', 'Kangchenjunga', 'Lhotse'], correct: 'Everest' },
            { q: 'Combien de pays composent l\'Asie du Sud-Est?', opts: ['10', '8', '12', '11'], correct: '11' },
            { q: 'Quelle est la plus grande ville d\'Asie?', opts: ['Tokyo', 'Shanghai', 'Delhi', 'Bangkok'], correct: 'Tokyo' },
            { q: 'Quel est le plus grand pays d\'Asie?', opts: ['Chine', 'Inde', 'Russie', 'Indonésie'], correct: 'Russie' }
        ]
    },
    {
        num: 6,
        name: 'Amérique du Nord',
        icon: '🗽',
        description: 'Visite les gratte-ciels et chutes de l\'Amérique du Nord!',
        startDiamonds: 200,
        startCoins: 1000,
        questions: [
            { q: 'Quel est le plus long fleuve d\'Amérique du Nord?', opts: ['Missouri', 'Mackenzie', 'Rio Grande', 'Mississippi'], correct: 'Mackenzie' },
            { q: 'Quel est le plus haut sommet d\'Amérique du Nord?', opts: ['Denali', 'Mont Blanc', 'Pico de Orizaba', 'Pic d\'Aconcagua'], correct: 'Denali' },
            { q: 'Combien de pays composent l\'Amérique du Nord?', opts: ['3', '4', '2', '5'], correct: '3' },
            { q: 'Quelle est la capitale des États-Unis?', opts: ['New York', 'Los Angeles', 'Washington D.C.', 'Boston'], correct: 'Washington D.C.' }
        ]
    },
    {
        num: 7,
        name: 'Amérique du Sud',
        icon: '🦜',
        description: 'Découvre l\'Amazonie et les merveilles du sud!',
        startDiamonds: 220,
        startCoins: 1100,
        questions: [
            { q: 'Quel est le plus grand fleuve du monde?', opts: ['Amazone', 'Nil', 'Yangtsé', 'Mississippi'], correct: 'Amazone' },
            { q: 'Quel est le plus haut sommet d\'Amérique du Sud?', opts: ['Denali', 'Pic d\'Aconcagua', 'Mont Blanc', 'Elbrus'], correct: 'Pic d\'Aconcagua' },
            { q: 'Combien de pays composent l\'Amérique du Sud?', opts: ['12', '10', '14', '13'], correct: '12' },
            { q: 'Quelle est la plus grande ville d\'Amérique du Sud?', opts: ['São Paulo', 'Rio de Janeiro', 'Buenos Aires', 'Lima'], correct: 'São Paulo' }
        ]
    },
    {
        num: 8,
        name: 'Océanie',
        icon: '🏝️',
        description: 'Navigue entre les îles et récifs de l\'Océanie!',
        startDiamonds: 180,
        startCoins: 900,
        questions: [
            { q: 'Quel est le plus grand pays d\'Océanie?', opts: ['Australie', 'Nouvelle-Zélande', 'Papouasie-Nouvelle-Guinée', 'Samoa'], correct: 'Australie' },
            { q: 'Quelle est la capitale de l\'Australie?', opts: ['Sydney', 'Melbourne', 'Canberra', 'Brisbane'], correct: 'Canberra' },
            { q: 'Combien de pays/territoires composent l\'Océanie?', opts: ['14', '10', '18', '12'], correct: '14' },
            { q: 'Quel est le plus haut sommet d\'Océanie?', opts: ['Mont Kosciuszko', 'Mont Aspiring', 'Mont Wilhelm', 'Mont Tasman'], correct: 'Mont Jaya' }
        ]
    },
    {
        num: 9,
        name: 'Pôles',
        icon: '❄️',
        description: 'Explore les terres glacées et les aurores boréales!',
        startDiamonds: 150,
        startCoins: 750,
        questions: [
            { q: 'Quel pôle est le plus froid?', opts: ['Pôle Nord', 'Pôle Sud', 'Ils sont égaux', 'Dépend de la saison'], correct: 'Pôle Sud' },
            { q: 'Quelle est la couche de glace du Pôle Sud?', opts: ['Inlandsis', 'Permafrostov', 'Glaciair', 'Banquise'], correct: 'Inlandsis' },
            { q: 'Quels animaux vivent au Pôle Nord?', opts: ['Manchots', 'Ours polaires', 'Bélugas', 'Otaries'], correct: 'Ours polaires' },
            { q: 'Combien d\'années fait-il la nuit pendant six mois au Pôle Nord?', opts: ['1', '2', '6', 'C\'est rare'], correct: '1' }
        ]
    },
    {
        num: 10,
        name: 'Vue Mondiale',
        icon: '🌎',
        description: 'Maîtrise ta connaissance du monde entier!',
        startDiamonds: 300,
        startCoins: 1500,
        questions: [
            { q: 'Combien de continents y a-t-il?', opts: ['5', '6', '7', '8'], correct: '7' },
            { q: 'Combien de pays y a-t-il dans le monde?', opts: ['185', '193', '205', '175'], correct: '193' },
            { q: 'Quel est le plus grand océan?', opts: ['Atlantique', 'Pacifique', 'Indien', 'Arctique'], correct: 'Pacifique' },
            { q: 'Quel est le plus grand pays du monde?', opts: ['Canada', 'China', 'États-Unis', 'Russie'], correct: 'Russie' }
        ]
    }
];

function generateStageHTML(stage) {
    const questionsHTML = stage.questions.map((q, idx) => {
        const optionsHTML = q.opts.map(opt => 
            `<button class="option-btn" onclick="selectAnswer(${idx+1}, '${opt}')">${opt}</button>`
        ).join('\n                            ');
        
        return `                    <div class="question-card">
                        <h3>Question ${idx+1}: ${q.q}</h3>
                        <div class="options">
                            ${optionsHTML}
                        </div>
                        <div class="chatbot-help">
                            <div class="chatbot-help-text"><i class="fas fa-robot"></i> Besoin d'aide?</div>
                            <button class="chatbot-help-btn" onclick="askChatbot('${q.q}', ${idx+1})">🤖 Aide (5 💎)</button>
                        </div>
                    </div>`;
    }).join('\n');

    const correctAnswersObj = stage.questions.reduce((obj, q, idx) => {
        obj[idx+1] = q.correct;
        return obj;
    }, {});

    const correctAnswers = JSON.stringify(correctAnswersObj).replace(/"/g, "'");

    return `<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌍 Stage ${stage.num}: ${stage.name} - Monde Magique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/arabic.css">
    <link rel="stylesheet" href="css/cartoon-theme.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(180deg, #f3f8ff 0%, #e8f4ff 40%, #ecf0f8 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .stage-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .stage-header {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(243, 156, 18, 0.3);
            text-align: center;
        }
        .stage-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 800;
        }
        .stage-header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        .steps-progress {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            flex-wrap: wrap;
            gap: 20px;
        }
        .step {
            flex: 1;
            min-width: 150px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .step-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 3px solid #f39c12;
            transition: all 0.3s;
        }
        .step.active .step-icon {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            transform: scale(1.15);
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4);
        }
        .step.completed .step-icon {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        .step-label {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
            margin-top: 10px;
        }
        .step.active .step-label,
        .step.completed .step-label {
            color: #f39c12;
        }
        .stage-content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 35px rgba(243, 156, 18, 0.15);
            border: 2px solid rgba(243, 156, 18, 0.1);
            min-height: 500px;
            animation: contentAppear 0.5s ease-out;
        }
        @keyframes contentAppear {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .step-content {
            display: none;
            animation: fadeIn 0.5s ease-out;
        }
        .step-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .step-content h2 {
            font-size: 2rem;
            color: #f39c12;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .video-container {
            position: relative;
            width: 100%;
            height: 500px;
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }
        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .course-poster {
            background: linear-gradient(135deg, #fff8e8 0%, #ffe8b6 100%);
            border: 3px solid #f39c12;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            text-align: center;
        }
        .course-poster img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(243, 156, 18, 0.2);
            margin-bottom: 20px;
        }
        .course-poster h3 {
            color: #f39c12;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .course-poster p {
            color: #666;
            line-height: 1.6;
            font-size: 1.1rem;
        }
        .pdf-container {
            background: #f5f5f5;
            border: 2px solid #ddd;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .pdf-viewer {
            width: 100%;
            height: 500px;
            border: none;
        }
        .qcm-container {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        .question-card {
            background: linear-gradient(135deg, #fff8e8 0%, #ffe8b6 100%);
            border: 2px solid rgba(243, 156, 18, 0.2);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        .question-card h3 {
            color: #f39c12;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }
        .options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .option-btn {
            padding: 15px 20px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
            font-size: 1rem;
        }
        .option-btn:hover {
            border-color: #f39c12;
            background: rgba(243, 156, 18, 0.05);
            transform: translateY(-3px);
        }
        .option-btn.selected {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            border-color: #f39c12;
        }
        .chatbot-help {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 15px 20px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        .chatbot-help-text {
            color: #856404;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chatbot-help-btn {
            padding: 10px 20px;
            background: #ffc107;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .chatbot-help-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.3);
        }
        .chatbot-help-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .step-controls {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 15px 30px;
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.3);
        }
        .btn:active {
            transform: translateY(-1px);
        }
        .btn-prev {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8787 100%);
        }
        .btn-complete {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            flex: 1;
            min-width: 200px;
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .resources {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .resource-display {
            background: white;
            padding: 10px 15px;
            border-radius: 10px;
            border: 2px solid #ddd;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .resource-display.diamonds {
            border-color: #f39c12;
            color: #f39c12;
        }
        .resource-display.coins {
            border-color: #ffc107;
            color: #ffc107;
        }
    </style>
</head>
<body class="cartoon-theme">
    <div class="stage-container">
        <div class="stage-header">
            <h1>${stage.icon} Stage ${stage.num}: ${stage.name}</h1>
            <p>${stage.description}</p>
        </div>
        <div class="resources">
            <div class="resource-display diamonds">
                <span>💎</span>
                <span id="diamonds-count">${stage.startDiamonds}</span>
            </div>
            <div class="resource-display coins">
                <span>🪙</span>
                <span id="coins-count">${stage.startCoins}</span>
            </div>
        </div>
        <div class="steps-progress">
            <div class="step active" onclick="goToStep(1)">
                <div class="step-icon">🎬</div>
                <div class="step-label">Étape 1: Vidéo</div>
            </div>
            <div class="step" onclick="goToStep(2)">
                <div class="step-icon">🖼️</div>
                <div class="step-label">Étape 2: Affiche</div>
            </div>
            <div class="step" onclick="goToStep(3)">
                <div class="step-icon">📚</div>
                <div class="step-label">Étape 3: PDF</div>
            </div>
            <div class="step" onclick="goToStep(4)">
                <div class="step-icon">❓</div>
                <div class="step-label">Étape 4: QCM</div>
            </div>
        </div>
        <div class="stage-content">
            <div id="step-1" class="step-content active">
                <h2><span>🎬</span> Étape 1: Regarder la Vidéo</h2>
                <div class="video-container">
                    <video controls style="width: 100%; height: 100%;">
                        <source src="assets/vedios/v${stage.num}.mp4" type="video/mp4">
                    </video>
                </div>
                <p style="color: #666; font-size: 1.1rem; line-height: 1.6;">Regardez cette vidéo pour découvrir les merveilles de ${stage.name}.</p>
                <div class="step-controls">
                    <button class="btn btn-prev" onclick="markStepCompleted(1);goToStep(2)">
                        <i class="fas fa-arrow-right"></i> Étape Suivante
                    </button>
                </div>
            </div>
            <div id="step-2" class="step-content">
                <h2><span>🖼️</span> Étape 2: Affiche du Cours</h2>
                <div class="course-poster">
                    <img src="assets/images/c${stage.num}.jpg" alt="Affiche ${stage.name}" onerror="this.style.display='none'">
                    <h3>${stage.name} - Merveilles du Monde</h3>
                    <p>Découvrez les caractéristiques uniques de ${stage.name}...</p>
                </div>
                <div class="step-controls">
                    <button class="btn btn-prev" onclick="goToStep(1)">Retour</button>
                    <button class="btn" onclick="markStepCompleted(2);goToStep(3)">
                        <i class="fas fa-arrow-right"></i> Étape Suivante
                    </button>
                </div>
            </div>
            <div id="step-3" class="step-content">
                <h2><span>📚</span> Étape 3: Cours en PDF</h2>
                <div class="pdf-container">
                    <iframe class="pdf-viewer" src="assets/pdf/p${stage.num}.pdf"></iframe>
                </div>
                <div class="step-controls">
                    <button class="btn btn-prev" onclick="goToStep(2)">Retour</button>
                    <button class="btn" onclick="markStepCompleted(3);goToStep(4)">
                        <i class="fas fa-arrow-right"></i> Étape Suivante (QCM)
                    </button>
                </div>
            </div>
            <div id="step-4" class="step-content">
                <h2><span>❓</span> Étape 4: Quiz Final</h2>
                <p style="color: #666; font-size: 1.1rem; margin-bottom: 30px;">Répondez aux questions de ${stage.name}!</p>
                <div class="qcm-container">
${questionsHTML}
                </div>
                <div class="step-controls">
                    <button class="btn btn-prev" onclick="goToStep(3)">Retour</button>
                    <button class="btn btn-complete" onclick="submitQCM()">
                        <i class="fas fa-check-circle"></i> Soumettre le QCM
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let gameState = {
            currentStep: 1,
            diamonds: ${stage.startDiamonds},
            coins: ${stage.startCoins},
            completedSteps: [],
            answers: {},
            chatbotUsed: {}
        };
        function goToStep(stepNumber) {
            if (stepNumber < 1 || stepNumber > 4) return;
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step').forEach(el => el.classList.remove('active', 'completed'));
            document.getElementById(\`step-\${stepNumber}\`).classList.add('active');
            document.querySelectorAll('.step')[stepNumber - 1].classList.add('active');
            for (let i = 1; i < stepNumber; i++) {
                document.querySelectorAll('.step')[i - 1].classList.add('completed');
            }
            gameState.currentStep = stepNumber;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function markStepCompleted(stepNumber) {
            if (!gameState.completedSteps.includes(stepNumber)) {
                gameState.completedSteps.push(stepNumber);
            }
        }
        function selectAnswer(questionNumber, answer) {
            document.querySelectorAll(\`.question-card:nth-child(\${questionNumber}) .option-btn\`).forEach(btn => {
                if (btn.textContent.includes(answer)) {
                    btn.classList.add('selected');
                } else {
                    btn.classList.remove('selected');
                }
            });
            gameState.answers[questionNumber] = answer;
        }
        function askChatbot(question, questionNumber) {
            const diamondCost = 5;
            if (gameState.diamonds < diamondCost) {
                alert('❌ Vous n\\'avez pas assez de diamants!');
                return;
            }
            gameState.diamonds -= diamondCost;
            updateResources();
            gameState.chatbotUsed[questionNumber] = true;
            const responses = {
${stage.questions.map((q, idx) => `                '${q.q}': '🤖 La réponse est **${q.correct}**!'`).join(',\n')}
            };
            const answer = responses[question] || '🤖 Bonne question!';
            alert(answer + '\\n\\n-5 💎 dépensés');
        }
        function submitQCM() {
            let score = 0;
            const correctAnswers = ${correctAnswers};
            for (let i = 1; i <= 4; i++) {
                if (gameState.answers[i] === correctAnswers[i]) score++;
            }
            const percentage = (score / 4) * 100;
            const reward = Math.floor((percentage / 100) * ${stage.startDiamonds});
            alert(\`🎉 Résultats!\\n\\nScore: \${score}/4 (\${percentage.toFixed(0)}%)\\n\\nDiamants gagnés: \${reward}\\nPièces d'or gagnées: \${reward * 5}\`);
            gameState.diamonds += reward;
            gameState.coins += reward * 5;
            updateResources();
            markStepCompleted(4);
            setTimeout(() => {
                alert('✅ Félicitations! Stage ${stage.num} complété!\\n\\nRetour au dashboard...');
                window.location.href = 'stages-index.html';
            }, 500);
        }
        function updateResources() {
            document.getElementById('diamonds-count').textContent = gameState.diamonds;
            document.getElementById('coins-count').textContent = gameState.coins;
        }
        document.addEventListener('DOMContentLoaded', updateResources);
    </script>
</body>
</html>`;
}

// Generate HTML for stages 3-10
stages.forEach(stage => {
    const filename = \`stage-\${stage.num}-\${stage.name.toLowerCase().replace(/\\s+/g, '-')}.html\`;
    const html = generateStageHTML(stage);
    console.log(`Generated: \${filename}`);
});
