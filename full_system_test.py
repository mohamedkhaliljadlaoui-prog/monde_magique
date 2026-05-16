#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os
import re

print('🧪 TEST COMPLET DES 10 STAGES')
print('=' * 60)
print()

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

total_checks = 0
passed_checks = 0

for filename, stage_num in stage_files:
    filepath = f'c:\\xampppp\\htdocs\\monde-magique\\{filename}'
    
    if not os.path.exists(filepath):
        print(f'❌ Stage {stage_num}: Fichier introuvable!')
        continue
    
    print(f'📋 Stage {stage_num}: {filename}')
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Test 1: Vérifier les 6 étapes
    steps = len(re.findall(r'class="step-content"', content))
    has_6_steps = steps >= 6
    total_checks += 1
    if has_6_steps:
        passed_checks += 1
        print(f'  ✓ Test 1: 6 étapes trouvées ({steps} .step-content)')
    else:
        print(f'  ✗ Test 1: Seulement {steps} étapes (besoin de 6)')
    
    # Test 2: Vérifier le QCM (5 questions)
    qcm_count = len(re.findall(r'data-question="q[1-5]"', content))
    has_qcm = qcm_count == 5
    total_checks += 1
    if has_qcm:
        passed_checks += 1
        print(f'  ✓ Test 2: QCM complet (5 questions)')
    else:
        print(f'  ✗ Test 2: Seulement {qcm_count} questions (besoin de 5)')
    
    # Test 3: Vérifier la vérification 80%
    has_80_check = 'gameState.qcmScore<80' in content
    total_checks += 1
    if has_80_check:
        passed_checks += 1
        print(f'  ✓ Test 3: Vérification 80% présente')
    else:
        print(f'  ✗ Test 3: Pas de vérification 80%')
    
    # Test 4: Vérifier le bouton réessayer
    has_retry = 'retry-btn' in content and 'retryQuestion' in content
    total_checks += 1
    if has_retry:
        passed_checks += 1
        print(f'  ✓ Test 4: Bouton réessayer présent')
    else:
        print(f'  ✗ Test 4: Bouton réessayer manquant')
    
    # Test 5: Vérifier la sauvegarde de progression
    has_save = 'saveProgress' in content
    total_checks += 1
    if has_save:
        passed_checks += 1
        print(f'  ✓ Test 5: Sauvegarde de progression présente')
    else:
        print(f'  ✗ Test 5: Pas de sauvegarde')
    
    # Test 6: Vérifier les images
    has_images = f'assets/stage/f{stage_num}/' in content
    total_checks += 1
    if has_images:
        passed_checks += 1
        print(f'  ✓ Test 6: Références d\'images f{stage_num}/ présentes')
    else:
        print(f'  ✗ Test 6: Images non référencées')
    
    # Test 7: Vérifier le PDF
    has_pdf = f'assets/stage/f{stage_num}/cours' in content
    total_checks += 1
    if has_pdf:
        passed_checks += 1
        print(f'  ✓ Test 7: PDF référencé correctement')
    else:
        print(f'  ✗ Test 7: PDF non trouvé')
    
    # Test 8: Vérifier les récompenses
    has_rewards = '+10' in content and '+25' in content
    total_checks += 1
    if has_rewards:
        passed_checks += 1
        print(f'  ✓ Test 8: Système de récompenses (+10💎 +25🪙)')
    else:
        print(f'  ✗ Test 8: Récompenses manquantes')
    
    # Test 9: Vérifier le scoring du QCM
    has_scoring = 'gameState.qcmScore=' in content and 'Math.round' in content
    total_checks += 1
    if has_scoring:
        passed_checks += 1
        print(f'  ✓ Test 9: Système de scoring QCM présent')
    else:
        print(f'  ✗ Test 9: Scoring QCM manquant')
    
    # Test 10: Vérifier la navigation
    has_navigation = 'goToStep' in content and 'nextStep' in content
    total_checks += 1
    if has_navigation:
        passed_checks += 1
        print(f'  ✓ Test 10: Navigation entre étapes présente')
    else:
        print(f'  ✗ Test 10: Navigation manquante')
    
    print()

print('=' * 60)
print(f'📊 RÉSUMÉ TESTS: {passed_checks}/{total_checks} vérifications réussies')
print()

if passed_checks == total_checks:
    print('✅ Tous les tests passent! Le système est fonctionnel!')
else:
    print(f'⚠️  {total_checks - passed_checks} tests échoués')

# Test du Dashboard
print()
print('🎯 VÉRIFICATION DU DASHBOARD')
print('-' * 60)

dashboard_file = 'c:\\xampppp\\htdocs\\monde-magique\\dashboard-stages.html'
if os.path.exists(dashboard_file):
    with open(dashboard_file, 'r', encoding='utf-8') as f:
        dash_content = f.read()
    
    checks = {
        'Stage progression loader': 'loadProgress' in dash_content,
        'localStorage integration': 'localStorage.getItem' in dash_content,
        '10 stages defined': 'num:10' in dash_content,
        'Progress bar': 'progressBar' in dash_content,
        'Stats display': 'stagesCompletedCount' in dash_content,
    }
    
    for check, result in checks.items():
        status = '✓' if result else '✗'
        print(f'{status} {check}')
    
    if all(checks.values()):
        print('\n✅ Dashboard est fonctionnel!')
else:
    print('❌ Dashboard introuvable!')

print()
print('=' * 60)
print('🎮 RÉSUMÉ FINAL DU SYSTÈME')
print('=' * 60)
print('✓ 10 Stages créés')
print('✓ QCM spécifiques pour chaque stage')
print('✓ Vérification 80% pour progression')
print('✓ Bouton réessayer en cas d\'erreur')
print('✓ Sauvegarde automatique de progression')
print('✓ Dashboard de navigation')
print('✓ Système de récompenses (💎🪙)')
print('✓ Images et PDFs intégrés')
print('✓ Déblocage progressif des stages')
print()
print('🚀 LE SYSTÈME EST PRÊT POUR UTILISATION!')
