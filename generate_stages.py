#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import os

stages = {
    3: ('Afrique', '🦁', 200, 1000),
    4: ('Europe', '🏰', 250, 1250),
    5: ('Asie', '🏯', 300, 1500),
    6: ('Amérique du Nord', '🗽', 200, 1000),
    7: ('Amérique du Sud', '🦜', 220, 1100),
    8: ('Océanie', '🏝️', 180, 900),
    9: ('Pôles', '❄️', 150, 750),
    10: ('Vue Mondiale', '🌎', 300, 1500),
}

questions = {
    3: [
        ('Quel est le plus grand désert d\'Afrique?', ['Sahara', 'Kalahari', 'Namib', 'Atacama'], 'Sahara'),
        ('Quel est le plus haut sommet d\'Afrique?', ['Kilimandjaro', 'Crête Stanley', 'Mont Kenya', 'Mont Cameroun'], 'Kilimandjaro'),
        ('Combien de pays y a-t-il en Afrique?', ['54', '48', '52', '50'], '54'),
        ('Quel est le plus grand fleuve d\'Afrique?', ['Nil', 'Congo', 'Niger', 'Zambèze'], 'Nil'),
    ],
    4: [
        ('Quelle est la capitale de la Suisse?', ['Berne', 'Zurich', 'Genève', 'Interlaken'], 'Berne'),
        ('Quel est le plus haut sommet d\'Europe?', ['Mont-Blanc', 'Mont Rose', 'Mont Fuji', 'Elbrus'], 'Elbrus'),
        ('Combien de pays font partie de l\'UE?', ['27', '25', '28', '24'], '27'),
        ('Quelle est la plus grande ville d\'Europe?', ['Istanbul', 'Rome', 'Moscou', 'Paris'], 'Moscou'),
    ],
    5: [
        ('Quel est le plus haut sommet du monde?', ['Everest', 'K2', 'Kangchenjunga', 'Lhotse'], 'Everest'),
        ('Combien de pays en Asie du Sud-Est?', ['10', '8', '12', '11'], '11'),
        ('Quelle est la plus grande ville d\'Asie?', ['Tokyo', 'Shanghai', 'Delhi', 'Bangkok'], 'Tokyo'),
        ('Quel est le plus grand pays d\'Asie?', ['Chine', 'Inde', 'Russie', 'Indonésie'], 'Russie'),
    ],
    6: [
        ('Quel est le plus long fleuve d\'Amérique du Nord?', ['Missouri', 'Mackenzie', 'Rio Grande', 'Mississippi'], 'Mackenzie'),
        ('Quel est le plus haut sommet d\'Amérique du Nord?', ['Denali', 'Mont Blanc', 'Pico de Orizaba', 'Aconcagua'], 'Denali'),
        ('Combien de pays en Amérique du Nord?', ['3', '4', '2', '5'], '3'),
        ('Quelle est la capitale des USA?', ['New York', 'Los Angeles', 'Washington D.C.', 'Boston'], 'Washington D.C.'),
    ],
    7: [
        ('Quel est le plus grand fleuve du monde?', ['Amazone', 'Nil', 'Yangtsé', 'Mississippi'], 'Amazone'),
        ('Quel est le plus haut sommet d\'Amérique du Sud?', ['Denali', 'Aconcagua', 'Mont Blanc', 'Elbrus'], 'Aconcagua'),
        ('Combien de pays en Amérique du Sud?', ['12', '10', '14', '13'], '12'),
        ('Quelle est la plus grande ville d\'Amérique du Sud?', ['São Paulo', 'Rio de Janeiro', 'Buenos Aires', 'Lima'], 'São Paulo'),
    ],
    8: [
        ('Quel est le plus grand pays d\'Océanie?', ['Australie', 'Nouvelle-Zélande', 'Papouasie-NG', 'Samoa'], 'Australie'),
        ('Quelle est la capitale de l\'Australie?', ['Sydney', 'Melbourne', 'Canberra', 'Brisbane'], 'Canberra'),
        ('Combien de pays/territoires en Océanie?', ['14', '10', '18', '12'], '14'),
        ('Quel est le plus haut sommet d\'Océanie?', ['Kosciuszko', 'Aspiring', 'Wilhelm', 'Tasman'], 'Wilhelm'),
    ],
    9: [
        ('Quel pôle est le plus froid?', ['Pôle Nord', 'Pôle Sud', 'Égal', 'Dépend'], 'Pôle Sud'),
        ('Quel animal au Pôle Nord?', ['Manchots', 'Ours', 'Bélugas', 'Otaries'], 'Ours'),
        ('Quel est le plus grand océan?', ['Atlantique', 'Pacifique', 'Indien', 'Arctique'], 'Pacifique'),
        ('Combien de continents?', ['5', '6', '7', '8'], '7'),
    ],
    10: [
        ('Combien de continents y a-t-il?', ['5', '6', '7', '8'], '7'),
        ('Combien de pays dans le monde?', ['185', '193', '205', '175'], '193'),
        ('Quel est le plus grand océan?', ['Atlantique', 'Pacifique', 'Indien', 'Arctique'], 'Pacifique'),
        ('Quel est le plus grand pays du monde?', ['Canada', 'Chine', 'États-Unis', 'Russie'], 'Russie'),
    ],
}

def generate_html(num, name, icon, diamonds, coins):
    question_data = questions.get(num, [])
    
    questions_html = ""
    correct_answers = {}
    
    for i, (q, opts, correct) in enumerate(question_data, 1):
        options_html = '\n'.join(f'                            <button class="option-btn" onclick="selectAnswer({i}, \'{opt}\')"{opt}</button>' for opt in opts)
        questions_html += f'''                    <div class="question-card">
                        <h3>Question {i}: {q}</h3>
                        <div class="options">
{options_html}
                        </div>
                        <div class="chatbot-help">
                            <div class="chatbot-help-text"><i class="fas fa-robot"></i> Besoin d'aide?</div>
                            <button class="chatbot-help-btn" onclick="askChatbot('{q}', {i})">🤖 Aide (5 💎)</button>
                        </div>
                    </div>
'''
        correct_answers[i] = correct
    
    # Generate correct answers mapping
    correct_answers_json = '{' + ', '.join(f'{k}: \'{v}\'' for k, v in correct_answers.items()) + '}'
    
    # Generate chatbot responses
    chatbot_responses = '\n'.join(f'                \'{q}\': \'🤖 La réponse est **{correct}**!\',' for q, _, correct in question_data[:-1])
    chatbot_responses += f'\n                \'{question_data[-1][0]}\': \'🤖 La réponse est **{question_data[-1][2]}**!\''
    
    return f'''<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌍 Stage {num}: {name} - Monde Magique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/arabic.css">
    <link rel="stylesheet" href="css/cartoon-theme.css">
    <style>
        * {{ margin: 0; padding: 0; box-sizing: border-box; }}
        body {{ background: linear-gradient(180deg, #f3f8ff 0%, #e8f4ff 40%, #ecf0f8 100%); min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }}
        .stage-container {{ max-width: 1000px; margin: 0 auto; }}
        .stage-header {{ background: linear-gradient(135deg, #9370DB 0%, #6A5ACD 100%); color: white; padding: 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(147, 112, 219, 0.3); text-align: center; }}
        .stage-header h1 {{ font-size: 2.5rem; margin-bottom: 10px; font-weight: 800; }}
        .stage-header p {{ font-size: 1.1rem; opacity: 0.95; }}
        .steps-progress {{ display: flex; justify-content: space-around; margin: 30px 0; flex-wrap: wrap; gap: 20px; }}
        .step {{ flex: 1; min-width: 150px; text-align: center; cursor: pointer; transition: all 0.3s; }}
        .step-icon {{ width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border: 3px solid #9370DB; transition: all 0.3s; }}
        .step.active .step-icon {{ background: linear-gradient(135deg, #9370DB 0%, #6A5ACD 100%); color: white; transform: scale(1.15); box-shadow: 0 8px 25px rgba(147, 112, 219, 0.4); }}
        .step.completed .step-icon {{ background: #4CAF50; color: white; border-color: #4CAF50; }}
        .step-label {{ font-weight: 600; color: #333; font-size: 1rem; margin-top: 10px; }}
        .step.active .step-label, .step.completed .step-label {{ color: #9370DB; }}
        .stage-content {{ background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 35px rgba(147, 112, 219, 0.15); border: 2px solid rgba(147, 112, 219, 0.1); min-height: 500px; animation: contentAppear 0.5s ease-out; }}
        @keyframes contentAppear {{ from {{ opacity: 0; transform: translateY(20px); }} to {{ opacity: 1; transform: translateY(0); }} }}
        .step-content {{ display: none; animation: fadeIn 0.5s ease-out; }}
        .step-content.active {{ display: block; }}
        @keyframes fadeIn {{ from {{ opacity: 0; }} to {{ opacity: 1; }} }}
        .step-content h2 {{ font-size: 2rem; color: #9370DB; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; }}
        .video-container {{ width: 100%; height: 500px; background: #000; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); margin-bottom: 20px; }}
        .video-container video {{ width: 100%; height: 100%; object-fit: cover; }}
        .course-poster {{ background: linear-gradient(135deg, #f0e8ff 0%, #e8d8ff 100%); border: 3px solid #9370DB; border-radius: 15px; padding: 30px; margin-bottom: 20px; text-align: center; }}
        .course-poster img {{ max-width: 100%; max-height: 400px; border-radius: 10px; box-shadow: 0 8px 20px rgba(147, 112, 219, 0.2); margin-bottom: 20px; }}
        .course-poster h3 {{ color: #9370DB; font-size: 1.5rem; margin-bottom: 15px; }}
        .course-poster p {{ color: #666; line-height: 1.6; font-size: 1.1rem; }}
        .pdf-container {{ background: #f5f5f5; border: 2px solid #ddd; border-radius: 15px; overflow: hidden; margin-bottom: 20px; }}
        .pdf-viewer {{ width: 100%; height: 500px; border: none; }}
        .qcm-container {{ display: flex; flex-direction: column; gap: 25px; }}
        .question-card {{ background: linear-gradient(135deg, #f0e8ff 0%, #e8d8ff 100%); border: 2px solid rgba(147, 112, 219, 0.2); border-radius: 15px; padding: 25px; margin-bottom: 20px; }}
        .question-card h3 {{ color: #9370DB; font-size: 1.3rem; margin-bottom: 20px; }}
        .options {{ display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }}
        .option-btn {{ padding: 15px 20px; background: white; border: 2px solid #ddd; border-radius: 10px; cursor: pointer; font-weight: 600; transition: all 0.3s; text-align: center; font-size: 1rem; }}
        .option-btn:hover {{ border-color: #9370DB; background: rgba(147, 112, 219, 0.05); transform: translateY(-3px); }}
        .option-btn.selected {{ background: linear-gradient(135deg, #9370DB 0%, #6A5ACD 100%); color: white; border-color: #9370DB; }}
        .chatbot-help {{ background: linear-gradient(135deg, #f0e8ff 0%, #e8d8ff 100%); border: 2px solid #9370DB; border-radius: 10px; padding: 15px 20px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center; gap: 15px; }}
        .chatbot-help-text {{ color: #6A5ACD; font-weight: 600; display: flex; align-items: center; gap: 10px; }}
        .chatbot-help-btn {{ padding: 10px 20px; background: #9370DB; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }}
        .chatbot-help-btn:hover {{ transform: scale(1.05); box-shadow: 0 6px 20px rgba(147, 112, 219, 0.3); }}
        .step-controls {{ display: flex; justify-content: space-between; gap: 20px; margin-top: 40px; flex-wrap: wrap; }}
        .btn {{ padding: 15px 30px; background: linear-gradient(135deg, #9370DB 0%, #6A5ACD 100%); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: all 0.3s; display: flex; align-items: center; gap: 10px; }}
        .btn:hover {{ transform: translateY(-3px); box-shadow: 0 8px 25px rgba(147, 112, 219, 0.3); }}
        .btn-complete {{ background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); flex: 1; min-width: 200px; }}
        .resources {{ display: flex; justify-content: flex-end; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }}
        .resource-display {{ background: white; padding: 10px 15px; border-radius: 10px; border: 2px solid #ddd; font-weight: 600; display: flex; align-items: center; gap: 8px; }}
        .resource-display.diamonds {{ border-color: #9370DB; color: #9370DB; }}
    </style>
</head>
<body class="cartoon-theme">
    <div class="stage-container">
        <div class="stage-header">
            <h1>{icon} Stage {num}: {name}</h1>
            <p>Apprends les merveilles de {name} à travers 4 étapes magiques!</p>
        </div>
        <div class="resources">
            <div class="resource-display diamonds"><span>💎</span><span id="diamonds-count">{diamonds}</span></div>
            <div class="resource-display coins"><span>🪙</span><span id="coins-count">{coins}</span></div>
        </div>
        <div class="steps-progress">
            <div class="step active" onclick="goToStep(1)"><div class="step-icon">🎬</div><div class="step-label">Étape 1: Vidéo</div></div>
            <div class="step" onclick="goToStep(2)"><div class="step-icon">🖼️</div><div class="step-label">Étape 2: Affiche</div></div>
            <div class="step" onclick="goToStep(3)"><div class="step-icon">📚</div><div class="step-label">Étape 3: PDF</div></div>
            <div class="step" onclick="goToStep(4)"><div class="step-icon">❓</div><div class="step-label">Étape 4: QCM</div></div>
        </div>
        <div class="stage-content">
            <div id="step-1" class="step-content active">
                <h2><span>🎬</span> Étape 1: Regarder la Vidéo</h2>
                <div class="video-container"><video controls style="width: 100%; height: 100%;"><source src="assets/vedios/v{num}.mp4" type="video/mp4"></video></div>
                <p style="color: #666; font-size: 1.1rem; line-height: 1.6;">Regardez cette vidéo pour découvrir les merveilles de {name}.</p>
                <div class="step-controls"><button class="btn" onclick="markStepCompleted(1);goToStep(2)"><i class="fas fa-arrow-right"></i> Étape Suivante</button></div>
            </div>
            <div id="step-2" class="step-content">
                <h2><span>🖼️</span> Étape 2: Affiche du Cours</h2>
                <div class="course-poster">
                    <img src="assets/images/c{num}.jpg" alt="Affiche {name}" onerror="this.style.display='none'">
                    <h3>{name} - Merveilles du Monde</h3>
                    <p>Découvrez les caractéristiques uniques de {name}...</p>
                </div>
                <div class="step-controls">
                    <button class="btn" onclick="goToStep(1)" style="background: #ff6b6b;">Retour</button>
                    <button class="btn" onclick="markStepCompleted(2);goToStep(3)"><i class="fas fa-arrow-right"></i> Étape Suivante</button>
                </div>
            </div>
            <div id="step-3" class="step-content">
                <h2><span>📚</span> Étape 3: Cours en PDF</h2>
                <div class="pdf-container"><iframe class="pdf-viewer" src="assets/pdf/p{num}.pdf"></iframe></div>
                <div class="step-controls">
                    <button class="btn" onclick="goToStep(2)" style="background: #ff6b6b;">Retour</button>
                    <button class="btn" onclick="markStepCompleted(3);goToStep(4)"><i class="fas fa-arrow-right"></i> Étape Suivante (QCM)</button>
                </div>
            </div>
            <div id="step-4" class="step-content">
                <h2><span>❓</span> Étape 4: Quiz Final</h2>
                <p style="color: #666; font-size: 1.1rem; margin-bottom: 30px;">Répondez aux questions de {name}!</p>
                <div class="qcm-container">
{questions_html}
                </div>
                <div class="step-controls">
                    <button class="btn" onclick="goToStep(3)" style="background: #ff6b6b;">Retour</button>
                    <button class="btn btn-complete" onclick="submitQCM()"><i class="fas fa-check-circle"></i> Soumettre le QCM</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let gameState = {{ currentStep: 1, diamonds: {diamonds}, coins: {coins}, completedSteps: [], answers: {{}}, chatbotUsed: {{}} }};
        function goToStep(n) {{ if (n < 1 || n > 4) return; document.querySelectorAll('.step-content').forEach(e => e.classList.remove('active')); document.querySelectorAll('.step').forEach(e => e.classList.remove('active', 'completed')); document.getElementById(`step-${{n}}`).classList.add('active'); document.querySelectorAll('.step')[n - 1].classList.add('active'); for (let i = 1; i < n; i++) document.querySelectorAll('.step')[i - 1].classList.add('completed'); gameState.currentStep = n; window.scrollTo({{ top: 0, behavior: 'smooth' }}); }}
        function markStepCompleted(n) {{ if (!gameState.completedSteps.includes(n)) gameState.completedSteps.push(n); }}
        function selectAnswer(q, a) {{ document.querySelectorAll(`.question-card:nth-child(${{q}}) .option-btn`).forEach(b => {{ if (b.textContent.includes(a)) b.classList.add('selected'); else b.classList.remove('selected'); }}); gameState.answers[q] = a; }}
        function askChatbot(q, n) {{ if (gameState.diamonds < 5) {{ alert('❌ Vous n\\'avez pas assez de diamants!'); return; }} gameState.diamonds -= 5; updateResources(); const r = {{ {chatbot_responses} }}; alert((r[q] || '🤖 Bonne question!') + '\\n\\n-5 💎'); }}
        function submitQCM() {{ let score = 0; const ca = {correct_answers_json}; for (let i = 1; i <= 4; i++) if (gameState.answers[i] === ca[i]) score++; const p = (score / 4) * 100; const r = Math.floor((p / 100) * {diamonds}); alert(`🎉 Résultats!\\n\\nScore: ${{score}}/4 (${{p.toFixed(0)}}%)\\n\\nDiamants gagnés: ${{r}}\\nPièces d'or gagnées: ${{r * 5}}`); gameState.diamonds += r; gameState.coins += r * 5; updateResources(); setTimeout(() => {{ alert('✅ Félicitations! Stage {num} complété!\\n\\nRetour au dashboard...'); window.location.href = 'stages-index.html'; }}, 500); }}
        function updateResources() {{ document.getElementById('diamonds-count').textContent = gameState.diamonds; document.getElementById('coins-count').textContent = gameState.coins; }}
        document.addEventListener('DOMContentLoaded', updateResources);
    </script>
</body>
</html>'''

# Create files  
for num, (name, icon, diamonds, coins) in stages.items():
    stage_name = name.lower().replace(' ', '-')
    filename = f'stage-{num}-{stage_name}.html'
    html = generate_html(num, name, icon, diamonds, coins)
    with open(filename, 'w', encoding='utf-8') as f:
        f.write(html)
    print(f'✅ Created: {filename}')

print('\n✅ All stages 3-10 generated successfully!\n')
