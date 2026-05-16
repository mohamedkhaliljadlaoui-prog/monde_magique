#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import glob
import re

stage_files = [
    ('stage-1-tunisia.html', 1),
    ('stage-2-maghreb.html', 2),
    ('stage-3-africa.html', 3),
    ('stage-4-europe.html', 4),
    ('stage-5-asia.html', 5),
    ('stage-6-namerica.html', 6),
    ('stage-7-samerica.html', 7),
    ('stage-8-oceania.html', 8),
    ('stage-9-poles.html', 9),
    ('stage-10-world.html', 10),
]

next_stage_map = {
    1: 'stage-2-maghreb.html',
    2: 'stage-3-africa.html',
    3: 'stage-4-europe.html',
    4: 'stage-5-asia.html',
    5: 'stage-6-namerica.html',
    6: 'stage-7-samerica.html',
    7: 'stage-8-oceania.html',
    8: 'stage-9-poles.html',
    9: 'stage-10-world.html',
    10: 'dashboard-stages.html'
}

for filename, stage_num in stage_files:
    filepath = f'c:\\xampppp\\htdocs\\monde-magique\\{filename}'
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Remplacer la fonction updateResources
    old_update = 'function updateResources(){document.getElementById(\'diamonds-count\').textContent=gameState.diamonds;document.getElementById(\'coins-count\').textContent=gameState.coins}'
    
    new_update = '''function updateResources(){document.getElementById('diamonds-count').textContent=gameState.diamonds;document.getElementById('coins-count').textContent=gameState.coins;saveProgress()}
        
        function saveProgress(){let progress=JSON.parse(localStorage.getItem('mondeMagiqueProgress')||'{"progress":{},"totalDiamonds":0,"totalCoins":0}');progress.progress[{{STAGE_NUM}}]={completed:false,diamonds:gameState.diamonds,coins:gameState.coins,qcmScore:gameState.qcmScore};progress.totalDiamonds+=gameState.diamonds-150;progress.totalCoins+=gameState.coins-750;localStorage.setItem('mondeMagiqueProgress',JSON.stringify(progress))}'''
    
    new_update = new_update.replace('{{STAGE_NUM}}', str(stage_num))
    
    content = content.replace(old_update, new_update)
    
    # Remplacer la fonction evaluateEssay pour sauvegarder et naviguer
    old_eval = 'let nextBtn=\'🏠العودة\';if(gameState.stageNum<10){nextBtn=\'➡️ المرحلة التالية\'}'
    new_eval = f'let nextBtn="🏠العودة";let nextFile="dashboard-stages.html";if(gameState.stageNum<10){{nextBtn="➡️ المرحلة التالية";nextFile="{next_stage_map[stage_num]}"}}'
    
    content = content.replace(old_eval, new_eval)
    
    # Remplacer la section du bouton final pour sauvegarder la progression comme complète
    old_btn = 'document.getElementById(\'nextBtn\').onclick=()=>{if(gameState.stageNum<10){window.location.href=`stage-${gameState.stageNum+1}-*.html`.replace(\'*\',[\'tunisia\',\'maghreb\',\'africa\',\'europe\',\'asia\',\'namerica\',\'samerica\',\'oceania\',\'poles\',\'world\'][gameState.stageNum])}else{window.location.href=\'dashboard.html\'}}'
    
    new_btn = f'''document.getElementById('nextBtn').onclick=()=>{{let progress=JSON.parse(localStorage.getItem('mondeMagiqueProgress')||'{{\"progress\":{{}},\"totalDiamonds\":0,\"totalCoins\":0}}');progress.progress[gameState.stageNum]={{completed:true,diamonds:gameState.diamonds,coins:gameState.coins,qcmScore:gameState.qcmScore}};progress.totalDiamonds+=gameState.diamonds-150;progress.totalCoins+=gameState.coins-750;localStorage.setItem('mondeMagiqueProgress',JSON.stringify(progress));window.location.href=nextFile}}'''
    
    content = content.replace(old_btn, new_btn)
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✓ Stage {stage_num:2d}: Progression sauvegardée")

print(f"\n✅ Toutes les stages sont maintenant connectées avec progression!")
