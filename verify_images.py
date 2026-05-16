#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os
import glob

base_path = r'c:\xampppp\htdocs\monde-magique\assets\stage'

print('📸 VÉRIFICATION DES IMAGES POUR CHAQUE STAGE')
print('=' * 50)
print()

stages_ok = 0
stages_missing = 0

for i in range(1, 11):
    folder = os.path.join(base_path, f'f{i}')
    
    if not os.path.exists(folder):
        print(f'❌ Stage {i:2d}: Dossier f{i} n\'existe pas!')
        stages_missing += 1
        continue
    
    # Vérifier les fichiers
    debut_files = glob.glob(os.path.join(folder, 'debut.*'))
    v_files = glob.glob(os.path.join(folder, 'v.*'))
    p_files = glob.glob(os.path.join(folder, 'p.*'))
    pdf_files = glob.glob(os.path.join(folder, 'cours*.pdf'))
    
    has_debut = len(debut_files) > 0
    has_v = len(v_files) > 0
    has_p = len(p_files) > 0
    has_pdf = len(pdf_files) > 0
    
    status = '✓' if (has_debut and has_v and has_p and has_pdf) else '✗'
    
    if status == '✓':
        stages_ok += 1
        print(f'{status} Stage {i:2d}: Complet')
        print(f'    - debut: {os.path.basename(debut_files[0]) if debut_files else "MANQUANT"}')
        print(f'    - v:     {os.path.basename(v_files[0]) if v_files else "MANQUANT"}')
        print(f'    - p:     {os.path.basename(p_files[0]) if p_files else "MANQUANT"}')
        print(f'    - pdf:   {os.path.basename(pdf_files[0]) if pdf_files else "MANQUANT"}')
    else:
        stages_missing += 1
        print(f'{status} Stage {i:2d}: INCOMPLET')
        if not has_debut: print(f'    ⚠️  debut.[png/jpg] MANQUANT')
        if not has_v: print(f'    ⚠️  v.[jpg/png] MANQUANT')
        if not has_p: print(f'    ⚠️  p.png MANQUANT')
        if not has_pdf: print(f'    ⚠️  cours{i}.pdf MANQUANT')
    print()

print('=' * 50)
print(f'📊 RÉSUMÉ: {stages_ok}/10 stages complets ✓')
if stages_missing > 0:
    print(f'⚠️  {stages_missing} stages incomplets!')
else:
    print('✅ Tous les stages ont les images requises!')
