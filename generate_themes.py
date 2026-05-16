#!/usr/bin/env python3
# -*- coding: utf-8 -*-
# Generate all stages with unique dynamic themes

stages_config = {
    1: {
        'name': 'Tunisie',
        'icon': '🇹🇳',
        'diamonds': 100,
        'coins': 500,
        'primary': '#0066cc',
        'secondary': '#FF6B35',
        'light_bg': '#E6F2FF',
        'dark': '#004499',
        'accent': '#FF6B35'
    },
    2: {
        'name': 'Maghreb',
        'icon': '🌍',
        'diamonds': 150,
        'coins': 750,
        'primary': '#D4A574',
        'secondary': '#AA6B45',
        'light_bg': '#F5EDE0',
        'dark': '#8B5A2B',
        'accent': '#D4A574'
    },
    3: {
        'name': 'Afrique',
        'icon': '🦁',
        'diamonds': 200,
        'coins': 1000,
        'primary': '#8B4513',
        'secondary': '#CD853F',
        'light_bg': '#F5E6D3',
        'dark': '#654321',
        'accent': '#CD853F'
    },
    4: {
        'name': 'Europe',
        'icon': '🏰',
        'diamonds': 250,
        'coins': 1250,
        'primary': '#1E90FF',
        'secondary': '#4169E1',
        'light_bg': '#E6F0FF',
        'dark': '#0047AB',
        'accent': '#4169E1'
    },
    5: {
        'name': 'Asie',
        'icon': '🏯',
        'diamonds': 300,
        'coins': 1500,
        'primary': '#FF1493',
        'secondary': '#FFD700',
        'light_bg': '#FFE6F0',
        'dark': '#C71585',
        'accent': '#FFD700'
    },
    6: {
        'name': 'Amérique du Nord',
        'icon': '🗽',
        'diamonds': 200,
        'coins': 1000,
        'primary': '#228B22',
        'secondary': '#32CD32',
        'light_bg': '#E6F5E6',
        'dark': '#1a6b1a',
        'accent': '#32CD32'
    },
    7: {
        'name': 'Amérique du Sud',
        'icon': '🦜',
        'diamonds': 220,
        'coins': 1100,
        'primary': '#FF8C00',
        'secondary': '#FFD700',
        'light_bg': '#FFF0E6',
        'dark': '#B35A00',
        'accent': '#FFD700'
    },
    8: {
        'name': 'Océanie',
        'icon': '🏝️',
        'diamonds': 180,
        'coins': 900,
        'primary': '#20B2AA',
        'secondary': '#00CED1',
        'light_bg': '#E0F5F5',
        'dark': '#008B8B',
        'accent': '#00CED1'
    },
    9: {
        'name': 'Pôles',
        'icon': '❄️',
        'diamonds': 150,
        'coins': 750,
        'primary': '#4169E1',
        'secondary': '#87CEEB',
        'light_bg': '#E6F2FF',
        'dark': '#00008B',
        'accent': '#87CEEB'
    },
    10: {
        'name': 'Vue Mondiale',
        'icon': '🌎',
        'diamonds': 300,
        'coins': 1500,
        'primary': '#9370DB',
        'secondary': '#20B2AA',
        'light_bg': '#F0E6FF',
        'dark': '#4B0082',
        'accent': '#20B2AA'
    }
}

questions_data = {
    1: [
        ('ما المقصود بالمجال الجغرافي؟', ['أ) مكان فارغ لا يحتوي على سكان', 'ب) فضاء يعيش فيه الإنسان وينظم فيه أنشطته', 'ج) مكان خاص بالحيوانات فقط'], 'ب) فضاء يعيش فيه الإنسان وينظم فيه أنشطته'),
        ('أي من العناصر التالية يُعتبر من مكونات المجال الجغرافي؟', ['أ) الإنسان فقط', 'ب) الطبيعة فقط', 'ج) الإنسان والطبيعة'], 'ج) الإنسان والطبيعة'),
        ('ما الفرق بين المجال الريفي والمجال الحضري؟', ['أ) لا يوجد فرق', 'ب) الريفي فيه مصانع فقط', 'ج) الحضري يتميز بكثرة السكان والخدمات'], 'ج) الحضري يتميز بكثرة السكان والخدمات'),
    ],
    2: [
        ('Combien de pays composent le Maghreb?', ['5', '3', '7', '4'], '5'),
        ('Quelle est la plus grande ville du Maroc?', ['Casablanca', 'Rabat', 'Fes', 'Marrakech'], 'Casablanca'),
        ('Quel est le plus haut sommet du Maghreb?', ['Atlas', 'Kilimanjaro', 'Toubkal', 'Rif'], 'Toubkal'),
        ('Quelle langue est parlée au Maghreb?', ['Arabe', 'Anglais', 'Espagnol', 'Chinois'], 'Arabe'),
    ],
    3: [
        ('Quel est le plus grand désert d\'Afrique?', ['Sahara', 'Kalahari', 'Namib', 'Atacama'], 'Sahara'),
        ('Quel est le plus haut sommet d\'Afrique?', ['Kilimandjaro', 'Stanley', 'Kenya', 'Cameroun'], 'Kilimandjaro'),
        ('Combien de pays y a-t-il en Afrique?', ['54', '48', '52', '50'], '54'),
        ('Quel est le plus grand fleuve d\'Afrique?', ['Nil', 'Congo', 'Niger', 'Zambèze'], 'Nil'),
    ],
    4: [
        ('Quelle est la capitale de la France?', ['Paris', 'Lyon', 'Marseille', 'Nice'], 'Paris'),
        ('Quel est le plus haut sommet d\'Europe?', ['Mont-Blanc', 'Elbrus', 'Denali', 'Aconcagua'], 'Elbrus'),
        ('Combien de pays dans l\'UE?', ['27', '25', '28', '24'], '27'),
        ('Quelle est la plus grande ville d\'Europe?', ['Istanbul', 'Rome', 'Moscou', 'Paris'], 'Moscou'),
    ],
    5: [
        ('Quel est le plus haut sommet du monde?', ['Everest', 'K2', 'Kangchenjunga', 'Lhotse'], 'Everest'),
        ('Combien de pays en Asie du Sud-Est?', ['10', '8', '12', '11'], '11'),
        ('Quelle est la plus grande ville d\'Asie?', ['Tokyo', 'Shanghai', 'Delhi', 'Bangkok'], 'Tokyo'),
        ('Quel est le plus grand pays d\'Asie?', ['Chine', 'Inde', 'Russie', 'Indonésie'], 'Russie'),
    ],
    6: [
        ('Quelle est la capitale du Canada?', ['Toronto', 'Ottawa', 'Vancouver', 'Montreal'], 'Ottawa'),
        ('Quel est le plus long fleuve d\'Amérique du Nord?', ['Missouri', 'Mackenzie', 'Rio Grande', 'Mississippi'], 'Mackenzie'),
        ('Combien de pays en Amérique du Nord?', ['3', '4', '2', '5'], '3'),
        ('Quelle est la capitale des USA?', ['New York', 'Los Angeles', 'Washington D.C.', 'Boston'], 'Washington D.C.'),
    ],
    7: [
        ('Quel est le plus grand fleuve du monde?', ['Amazone', 'Nil', 'Yangtsé', 'Mississippi'], 'Amazone'),
        ('Quel est le plus haut sommet d\'Amérique du Sud?', ['Denali', 'Aconcagua', 'Mont Blanc', 'Elbrus'], 'Aconcagua'),
        ('Combien de pays en Amérique du Sud?', ['12', '10', '14', '13'], '12'),
        ('Quelle est la plus grande ville d\'Amérique du Sud?', ['São Paulo', 'Rio', 'Buenos Aires', 'Lima'], 'São Paulo'),
    ],
    8: [
        ('Quel est le plus grand pays d\'Océanie?', ['Australie', 'Nouvelle-Zélande', 'Papouasie', 'Samoa'], 'Australie'),
        ('Quelle est la capitale de l\'Australie?', ['Sydney', 'Melbourne', 'Canberra', 'Brisbane'], 'Canberra'),
        ('Combien de pays/territoires en Océanie?', ['14', '10', '18', '12'], '14'),
        ('Quel est le plus haut sommet d\'Océanie?', ['Kosciuszko', 'Aspiring', 'Wilhelm', 'Tasman'], 'Wilhelm'),
    ],
    9: [
        ('Quel pôle est le plus froid?', ['Nord', 'Sud', 'Égal', 'Dépend'], 'Sud'),
        ('Quel animal au Pôle Nord?', ['Manchots', 'Ours', 'Bélugas', 'Otaries'], 'Ours'),
        ('Quelle couche de glace au Pôle Sud?', ['Inlandsis', 'Permafrost', 'Banquise', 'Glacier'], 'Inlandsis'),
        ('Quel explorateur au Pôle Sud?', ['Amundsen', 'Peary', 'Cook', 'Ross'], 'Amundsen'),
    ],
    10: [
        ('Combien de continents y a-t-il?', ['5', '6', '7', '8'], '7'),
        ('Combien de pays dans le monde?', ['185', '193', '205', '175'], '193'),
        ('Quel est le plus grand océan?', ['Atlantique', 'Pacifique', 'Indien', 'Arctique'], 'Pacifique'),
        ('Quel est le plus grand pays du monde?', ['Canada', 'Chine', 'États-Unis', 'Russie'], 'Russie'),
    ],
}

def rgb_to_rgba(hex_color, alpha):
    """Convert hex to rgba"""
    hex_color = hex_color.lstrip('#')
    r = int(hex_color[0:2], 16)
    g = int(hex_color[2:4], 16)
    b = int(hex_color[4:6], 16)
    return f'rgba({r}, {g}, {b}, {alpha})'

def generate_stage_html(num, config):
    """Generate HTML for a stage with dynamic theme"""
    primary = config['primary']
    secondary = config['secondary']
    light_bg = config['light_bg']
    dark = config['dark']
    accent = config['accent']
    name = config['name']
    icon = config['icon']
    diamonds = config['diamonds']
    coins = config['coins']
    
    questions = questions_data.get(num, [])
    
    questions_html = ""
    correct_answers = {}
    
    for i, (q, opts, correct) in enumerate(questions, 1):
        options_html = '\n'.join(
            f'                            <button class="option-btn" onclick="selectAnswer({i}, \'{opt}\')">{opt}</button>'
            for opt in opts
        )
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
    
    correct_answers_json = '{' + ', '.join(f'{k}: \'{v}\'' for k, v in correct_answers.items()) + '}'
    chatbot_responses = '\n'.join(
        f'                \'{q}\': \'🤖 La réponse est **{correct}**!\','
        for q, _, correct in questions[:-1]
    )
    chatbot_responses += f'\n                \'{questions[-1][0]}\': \'🤖 La réponse est **{questions[-1][2]}**!\''
    
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
        
        @keyframes pulse {{ 0%, 100% {{ transform: scale(1); }} 50% {{ transform: scale(1.02); }} }}
        @keyframes bounce {{ 0%, 100% {{ transform: translateY(0); }} 50% {{ transform: translateY(-10px); }} }}
        @keyframes glow {{ 0%, 100% {{ box-shadow: 0 0 5px rgba(0,0,0,0.1); }} 50% {{ box-shadow: 0 0 20px {rgb_to_rgba(primary, 0.4)}; }} }}
        
        .stage-container {{ max-width: 1000px; margin: 0 auto; }}
        .stage-header {{ background: linear-gradient(135deg, {primary} 0%, {secondary} 100%); color: white; padding: 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 30px {rgb_to_rgba(primary, 0.3)}; text-align: center; animation: pulse 3s ease-in-out infinite; }}
        .stage-header h1 {{ font-size: 2.5rem; margin-bottom: 10px; font-weight: 800; }}
        .stage-header p {{ font-size: 1.1rem; opacity: 0.95; }}
        
        .steps-progress {{ display: flex; justify-content: space-around; margin: 30px 0; flex-wrap: wrap; gap: 20px; }}
        .step {{ flex: 1; min-width: 150px; text-align: center; cursor: pointer; transition: all 0.3s; }}
        .step-icon {{ width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border: 3px solid {primary}; transition: all 0.3s; }}
        .step.active .step-icon {{ background: linear-gradient(135deg, {primary} 0%, {secondary} 100%); color: white; transform: scale(1.15); animation: bounce 1s ease-in-out infinite; box-shadow: 0 8px 25px {rgb_to_rgba(primary, 0.4)}; }}
        .step.completed .step-icon {{ background: #4CAF50; color: white; border-color: #4CAF50; }}
        .step-label {{ font-weight: 600; color: #333; font-size: 1rem; margin-top: 10px; }}
        .step.active .step-label, .step.completed .step-label {{ color: {primary}; }}
        
        .stage-content {{ background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 35px {rgb_to_rgba(primary, 0.15)}; border: 2px solid {rgb_to_rgba(primary, 0.1)}; min-height: 500px; animation: glow 2s ease-in-out infinite; }}
        @keyframes contentAppear {{ from {{ opacity: 0; transform: translateY(20px); }} to {{ opacity: 1; transform: translateY(0); }} }}
        .step-content {{ display: none; animation: fadeIn 0.5s ease-out; }}
        .step-content.active {{ display: block; }}
        @keyframes fadeIn {{ from {{ opacity: 0; }} to {{ opacity: 1; }} }}
        .step-content h2 {{ font-size: 2rem; color: {primary}; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; }}
        
        .video-container {{ width: 100%; height: 500px; background: #000; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); margin-bottom: 20px; }}
        .video-container video {{ width: 100%; height: 100%; object-fit: cover; }}
        
        .course-poster {{ background: linear-gradient(135deg, {light_bg} 0%, rgba(255,255,255,0.8) 100%); border: 3px solid {primary}; border-radius: 15px; padding: 30px; margin-bottom: 20px; text-align: center; }}
        .course-poster img {{ max-width: 100%; max-height: 400px; border-radius: 10px; box-shadow: 0 8px 20px {rgb_to_rgba(primary, 0.2)}; margin-bottom: 20px; }}
        .course-poster h3 {{ color: {primary}; font-size: 1.5rem; margin-bottom: 15px; }}
        .course-poster p {{ color: #666; line-height: 1.6; font-size: 1.1rem; }}
        
        .pdf-container {{ background: #f5f5f5; border: 2px solid #ddd; border-radius: 15px; overflow: hidden; margin-bottom: 20px; }}
        .pdf-viewer {{ width: 100%; height: 500px; border: none; }}
        
        .qcm-container {{ display: flex; flex-direction: column; gap: 25px; }}
        .question-card {{ background: linear-gradient(135deg, {light_bg} 0%, rgba(255,255,255,0.9) 100%); border: 2px solid {rgb_to_rgba(primary, 0.2)}; border-radius: 15px; padding: 25px; margin-bottom: 20px; transition: all 0.3s; }}
        .question-card:hover {{ border-color: {primary}; box-shadow: 0 6px 20px {rgb_to_rgba(primary, 0.15)}; }}
        .question-card h3 {{ color: {primary}; font-size: 1.3rem; margin-bottom: 20px; }}
        
        .options {{ display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }}
        .option-btn {{ padding: 15px 20px; background: white; border: 2px solid #ddd; border-radius: 10px; cursor: pointer; font-weight: 600; transition: all 0.3s; text-align: center; font-size: 1rem; }}
        .option-btn:hover {{ border-color: {primary}; background: {light_bg}; transform: translateY(-3px); }}
        .option-btn.selected {{ background: linear-gradient(135deg, {primary} 0%, {secondary} 100%); color: white; border-color: {primary}; }}
        
        .chatbot-help {{ background: linear-gradient(135deg, {light_bg} 0%, rgba(255,255,255,0.9) 100%); border: 2px solid {primary}; border-radius: 10px; padding: 15px 20px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center; gap: 15px; }}
        .chatbot-help-text {{ color: {dark}; font-weight: 600; display: flex; align-items: center; gap: 10px; }}
        .chatbot-help-btn {{ padding: 10px 20px; background: {primary}; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }}
        .chatbot-help-btn:hover {{ transform: scale(1.05); box-shadow: 0 6px 20px {rgb_to_rgba(primary, 0.3)}; }}
        
        .step-controls {{ display: flex; justify-content: space-between; gap: 20px; margin-top: 40px; flex-wrap: wrap; }}
        .btn {{ padding: 15px 30px; background: linear-gradient(135deg, {primary} 0%, {secondary} 100%); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: all 0.3s; display: flex; align-items: center; gap: 10px; }}
        .btn:hover {{ transform: translateY(-3px); box-shadow: 0 8px 25px {rgb_to_rgba(primary, 0.3)}; }}
        .btn-complete {{ background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); flex: 1; min-width: 200px; }}
        
        .resources {{ display: flex; justify-content: flex-end; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }}
        .resource-display {{ background: white; padding: 10px 15px; border-radius: 10px; border: 2px solid #ddd; font-weight: 600; display: flex; align-items: center; gap: 8px; }}
        .resource-display.diamonds {{ border-color: {primary}; color: {primary}; }}
        .resource-display.coins {{ border-color: {accent}; color: {accent}; }}
        
        @media (max-width: 768px) {{
            .stage-header h1 {{ font-size: 1.8rem; }}
            .steps-progress {{ gap: 10px; }}
            .step-icon {{ width: 60px; height: 60px; font-size: 1.8rem; }}
            .stage-content {{ padding: 20px; }}
            .options {{ grid-template-columns: 1fr; }}
            .step-controls {{ flex-direction: column; }}
            .btn {{ width: 100%; justify-content: center; }}
        }}
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

# Generate all stage files with the new themes
import os
os.chdir('c:\\xampppp\\htdocs\\monde-magique')

for num in range(1, 11):
    config = stages_config[num]
    html = generate_stage_html(num, config)
    
    # Get stage filenames
    if num == 1:
        filenames = ['stage-1.html', 'stage-1-tunisia.html']
    elif num == 2:
        filenames = ['stage-2-maghreb.html']
    else:
        # Map stage numbers to names
        stage_names = {
            3: 'africa', 4: 'europe', 5: 'asia',
            6: 'namerica', 7: 'samerica', 8: 'oceania', 9: 'poles', 10: 'world'
        }
        stage_names_fr = {
            3: 'afrique', 4: 'europe', 5: 'asie',
            6: 'amérique-du-nord', 7: 'amérique-du-sud', 8: 'océanie', 9: 'pôles', 10: 'vue-mondiale'
        }
        filenames = [f'stage-{num}-{stage_names[num]}.html', f'stage-{num}-{stage_names_fr[num]}.html']
    
    for filename in filenames:
        try:
            with open(filename, 'w', encoding='utf-8') as f:
                f.write(html)
            print(f'✅ Updated: {filename}')
        except Exception as e:
            print(f'❌ Error writing {filename}: {e}')

print('\n🎨 All stages with dynamic themes created!')
