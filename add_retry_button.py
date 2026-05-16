#!/usr/bin/env python3
# -*- coding: utf-8 -*-

stage_files = [
    'stage-1-tunisia.html',
    'stage-2-maghreb.html',
    'stage-3-africa.html',
    'stage-4-europe.html',
    'stage-5-asia.html',
    'stage-6-namerica.html',
    'stage-7-samerica.html',
    'stage-8-oceania.html',
    'stage-9-poles.html',
    'stage-10-world.html',
]

for filename in stage_files:
    filepath = f'c:\\xampppp\\htdocs\\monde-magique\\{filename}'
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Ajouter le style pour le bouton Réessayer si pas présent
    if '.retry-btn' not in content:
        # Ajouter après les styles des autres boutons
        old_verify_style = '.verify-btn:disabled{opacity:0.5;cursor:not-allowed}'
        new_verify_style = '''.verify-btn:disabled{opacity:0.5;cursor:not-allowed}
        .retry-btn{background:linear-gradient(135deg,var(--candy-orange) 0%,var(--candy-red) 100%);color:var(--text-light);border:none;padding:10px 25px;border-radius:25px;font-size:0.95rem;font-weight:700;cursor:pointer;margin-top:10px;margin-right:10px;transition:all 0.3s;display:inline-block;animation:slideRight 0.5s ease-out}
        .retry-btn:hover{transform:translateY(-3px);box-shadow:0 4px 15px rgba(255,152,0,0.4)}'''
        content = content.replace(old_verify_style, new_verify_style)
    
    # Ajouter la fonction retryQCM si pas présent
    if 'function retryQCM()' not in content:
        # Trouver la fin de la fonction verifyAnswer
        old_verify = '''function verifyAnswer(q){if(!selectedAnswers[q]){alert('اختر إجابة!');return}const c=document.querySelector(`[data-question="${q}"]`);const ans=correctAnswers[q];const u=selectedAnswers[q];const ok=u===ans;answeredQuestions[q]=ok?1:0;c.querySelectorAll('.option-btn').forEach(b=>{b.classList.add('disabled');if(b.textContent.trim().charAt(0)===ans)b.classList.add('correct-answer');if(b.classList.contains('selected')&&!ok)b.classList.add('wrong-answer')});if(ok){c.classList.add('correct');gameState.diamonds+=10;gameState.coins+=25}else{c.classList.add('incorrect')}const ex=c.querySelector('.answer-explanation');ex.textContent=ok?'✓أحسنت!':'✗للأسف.';ex.classList.add('show');c.querySelector('.verify-btn').disabled=true;c.querySelector('.verify-btn').textContent=ok?'✅صحيح':'❌خطأ';updateResources()}'''
        
        new_verify = '''function verifyAnswer(q){if(!selectedAnswers[q]){alert('اختر إجابة!');return}const c=document.querySelector(`[data-question="${q}"]`);const ans=correctAnswers[q];const u=selectedAnswers[q];const ok=u===ans;answeredQuestions[q]=ok?1:0;c.querySelectorAll('.option-btn').forEach(b=>{b.classList.add('disabled');if(b.textContent.trim().charAt(0)===ans)b.classList.add('correct-answer');if(b.classList.contains('selected')&&!ok)b.classList.add('wrong-answer')});if(ok){c.classList.add('correct');gameState.diamonds+=10;gameState.coins+=25}else{c.classList.add('incorrect')}const ex=c.querySelector('.answer-explanation');ex.textContent=ok?'✓أحسنت!':'✗للأسف.';ex.classList.add('show');c.querySelector('.verify-btn').disabled=true;c.querySelector('.verify-btn').textContent=ok?'✅صحيح':'❌خطأ';if(!ok){let retryBtn=document.createElement('button');retryBtn.className='retry-btn';retryBtn.textContent='🔄 أعد المحاولة';retryBtn.onclick=()=>retryQuestion(q);c.appendChild(retryBtn)}updateResources()}
        
        function retryQuestion(q){const c=document.querySelector(`[data-question="${q}"]`);c.classList.remove('correct','incorrect');c.querySelectorAll('.option-btn').forEach(b=>{b.classList.remove('selected','disabled','correct-answer','wrong-answer')});c.querySelector('.verify-btn').disabled=false;c.querySelector('.verify-btn').textContent='✓ تحقق';c.querySelector('.answer-explanation').classList.remove('show');const retryBtn=c.querySelector('.retry-btn');if(retryBtn)retryBtn.remove();selectedAnswers[q]=null;answeredQuestions[q]=0}'''
        
        content = content.replace(old_verify, new_verify)
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✓ Stage {filename[:11]:20s}: Bouton réessayer ajouté ✓")

print(f"\n✅ Tous les 10 stages ont le bouton 'Réessayer'!")
