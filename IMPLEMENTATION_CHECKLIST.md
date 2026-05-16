📋 MONDE MAGIQUE v2.0 - CHECKLIST D'IMPLÉMENTATION
================================================

🎯 OBJECTIF: Intégrer les 4 nouvelles fonctionnalités sans modifier le design actuel

═══════════════════════════════════════════════════════════════════════════════

✅ PHASE 1: INFRASTRUCTURE (COMPLÉTÉE)
─────────────────────────────────────────────────────────────────────────────

✅ Créé: /js/assistant.js
   └─ Personnage guide animé avec TTS français

✅ Créé: /js/resources.js
   └─ Gestionnaire diamants/pièces avec sync DB

✅ Créé: /js/certificates.js
   └─ Générateur PDF certificates + SVG badges

✅ Créé: /js/stage-navigator.js
   └─ Boutons "Découvrir Stage" avec navigation

✅ Créé: /php/api/users.php
   └─ API endpoint: GET /api/users/{id}
   └─ API endpoint: POST /api/users/create
   └─ API endpoint: POST /api/users/{id}/update-resources

✅ Créé: /php/api/stages.php
   └─ API endpoint: GET /api/stages/user/{id_user}
   └─ API endpoint: POST /api/stages/{id_user}/{num_stage}/complete-etape

✅ Mis à jour: database/schema.sql
   └─ Table: utilisateurs_ressources
   └─ Table: stages_completion_status
   └─ Table: etapes_completion_status
   └─ Table: certificats_badges

✅ Mis à jour: dashboard.html (scripts ajoutés)
   └─ <script src="js/assistant.js"></script>
   └─ <script src="js/resources.js"></script>
   └─ <script src="js/certificates.js"></script>
   └─ <script src="js/stage-navigator.js"></script>

✅ Mis à jour: Tous les 18 fichiers stage-*.html (scripts ajoutés)
   └─ <script src="js/resources.js"></script>
   └─ <script src="js/certificates.js"></script>

═══════════════════════════════════════════════════════════════════════════════

⏳ PHASE 2: BASE DE DONNÉES (EN ATTENTE)
─────────────────────────────────────────────────────────────────────────────

☐ ÉTAPE 1: Exécuter le SQL d'initialisation
   Commande:
   ```
   mysql -u root -p monde_magique < c:\xampppp\htdocs\monde-magique\database\schema.sql
   ```

☐ ÉTAPE 2: Vérifier que les 4 nouvelles tables sont créées
   Requête SQL:
   ```
   SHOW TABLES LIKE '%ressources%';
   SHOW TABLES LIKE '%completion%';
   SHOW TABLES LIKE '%certificat%';
   ```

☐ ÉTAPE 3: Vérifier que config.php contient la bonne connexion DB
   Fichier: php/config.php
   Doit avoir:
   - host: localhost
   - database: monde_magique
   - user: root
   - credentials correctes

═══════════════════════════════════════════════════════════════════════════════

⏳ PHASE 3: INTÉGRATION DASHBOARD (EN ATTENTE)
─────────────────────────────────────────────────────────────────────────────

☐ ÉTAPE 1: Ajouter le conteneur de ressources
   Fichier: dashboard.html
   Location: Dans .sidebar ou .header
   Ajouter:
   ```html
   <div class="resources-display">
       <span id="diamonds-count">10</span> 💎
       <span id="coins-count">0</span> 🪙
   </div>
   ```

☐ ÉTAPE 2: Ajouter le conteneur de navigation des stages
   Fichier: dashboard.html
   Location: Là où les stages sont affichés
   Ajouter:
   ```html
   <div id="stage-navigation-buttons"></div>
   ```

☐ ÉTAPE 3: Initialiser les composants dans DOMContentLoaded
   Fichier: dashboard.html
   Location: Dans le DOMContentLoaded existant
   Ajouter:
   ```javascript
   // Obtenir ID utilisateur (de session ou localStorage)
   const userId = window.userId || parseInt(localStorage.getItem('userId') || 1);
   
   // Initialiser ressources
   const resourceManager = new ResourceManager(userId);
   resourceManager.init();
   
   // Initialiser assistant
   const assistant = new AnimatedAssistant();
   assistant.init();
   
   // Initialiser navigation
   StageNavigator.initButtons();
   
   // Afficher bienvenue
   assistant.show('welcome');
   ```

☐ ÉTAPE 4: Ajouter CSS pour l'affichage des ressources
   Fichier: css/dashboard.css ou style existant
   Ajouter:
   ```css
   .resources-display {
       position: fixed;
       top: 20px;
       right: 20px;
       background: rgba(255, 255, 255, 0.95);
       padding: 10px 15px;
       border-radius: 10px;
       box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
       font-weight: bold;
       font-size: 1.1rem;
       z-index: 1000;
   }
   
   #diamonds-count, #coins-count {
       color: #E91E63;
       padding: 0 5px;
   }
   ```

═══════════════════════════════════════════════════════════════════════════════

⏳ PHASE 4: INTÉGRATION STAGE FILES (EN ATTENTE)
─────────────────────────────────────────────────────────────────────────────

☐ ÉTAPE 1: Initialiser ResourceManager dans chaque stage
   Fichier: Tous les stage-*.html
   Location: En haut du premier <script>
   Ajouter:
   ```javascript
   // Obtenir ID utilisateur
   const userId = window.userId || parseInt(localStorage.getItem('userId') || 1);
   
   // Initialiser ressources
   const resourceManager = new ResourceManager(userId);
   resourceManager.init();
   ```

☐ ÉTAPE 2: Hook sur la completion des étapes
   Fichier: Tous les stage-*.html
   Location: Fonction qui invalide une étape (après le test/vidéo)
   Ajouter:
   ```javascript
   // Enregistrer l'étape comme completée
   async function recordStepCompletion(stageNum, stepNum, typeOfStep, score) {
       try {
           const response = await fetch(`/php/api/stages/${userId}/${stageNum}/complete-etape`, {
               method: 'POST',
               headers: { 'Content-Type': 'application/json' },
               body: JSON.stringify({ 
                   num_etape: stepNum,
                   type_etape: typeOfStep, // 'video', 'poster', 'pdf', 'qcm', 'essay'
                   score: score || 0
               })
           });
           
           const data = await response.json();
           
           if (data.stage_complete) {
               // Stage complété! Générer certificat
               const certGen = new CertificateGenerator();
               certGen.generatePDF(
                   window.userName || 'Étudiant',
                   data.stage_name,
                   stageNum
               );
               
               // Afficher message de victoire
               if (window.assistant) {
                   window.assistant.show('stage_complete');
                   window.assistant.speak();
               }
               
               // Rediriger vers dashboard après 3 secondes
               setTimeout(() => window.location.href = 'dashboard.html', 3000);
           }
       } catch (error) {
           console.error('Erreur:', error);
       }
   }
   ```

☐ ÉTAPE 3: Appeler recordStepCompletion à chaque étape
   Exemples:
   
   Pour la vidéo:
   ```javascript
   // Après que l'utilisateur finisse la vidéo
   recordStepCompletion(1, 1, 'video', 100);
   ```
   
   Pour le QCM:
   ```javascript
   // Dans la fonction d'évaluation du QCM
   const score = (correctAnswers / totalQuestions) * 100;
   recordStepCompletion(1, 4, 'qcm', score);
   ```
   
   Pour l'essai:
   ```javascript
   // Après soumission de l'essai
   recordStepCompletion(1, 5, 'essay', evaluatedScore);
   ```

═══════════════════════════════════════════════════════════════════════════════

⏳ PHASE 5: SYSTÈME D'AUTHENTIFICATION (EN ATTENTE)
─────────────────────────────────────────────────────────────────────────────

☐ ÉTAPE 1: Obtenir l'ID utilisateur
   Méthode 1 - De session PHP:
   ```javascript
   const userId = <?php echo $_SESSION['user_id'] ?? 1; ?>;
   ```
   
   Méthode 2 - De localStorage:
   ```javascript
   const userId = parseInt(localStorage.getItem('userId') || 1);
   ```
   
   Méthode 3 - De l'URL:
   ```javascript
   const params = new URLSearchParams(window.location.search);
   const userId = params.get('user_id') || 1;
   ```

☐ ÉTAPE 2: Stocker l'ID utilisateur globalement
   Ajouter dans dashboard.html:
   ```javascript
   window.userId = userId;
   window.userName = 'Ali Beyrambaye'; // Adapter selon votre système
   ```

═══════════════════════════════════════════════════════════════════════════════

✅ PHASE 6: TESTS ET VALIDATION
─────────────────────────────────────────────────────────────────────────────

☐ TEST 1: Vérifier que l'assistant apparaît
   Résultat attendu: Une bulle flottante en bas à droite avec un personnage animé

☐ TEST 2: Vérifier que les ressources s'affichent
   Résultat attendu: "10 💎 0 🪙" en haut à droite du dashboard

☐ TEST 3: Vérifier que les boutons de navigation sont cliquables
   Résultat attendu: Clic → Redirection vers stage-X.html

☐ TEST 4: Compléter un stage entièrement
   Résultat attendu: 
   - Message "Félicitations!"
   - Assistant dit "Bravo!"
   - Certificat généré
   - Ressources augmentées

☐ TEST 5: Tester le partage de certificat
   Résultat attendu: Fenêtre de partage pour WhatsApp/Facebook/Twitter/Email

☐ TEST 6: Vérifier les données en base de données
   Requête:
   ```sql
   SELECT * FROM stages_completion_status WHERE id_user = 1;
   SELECT * FROM certificats_badges WHERE id_user = 1;
   ```

═══════════════════════════════════════════════════════════════════════════════

📊 RÉSUMÉ DES FICHIERS MODIFIÉS
─────────────────────────────────────────────────────────────────────────────

CRÉÉS (7 nouveaux):
✅ /js/assistant.js
✅ /js/resources.js
✅ /js/certificates.js
✅ /js/stage-navigator.js
✅ /php/api/users.php
✅ /php/api/stages.php
✅ FEATURES_v2.0_GUIDE.md

MODIFIÉS (19):
✅ dashboard.html (+4 scripts)
✅ stage-1-tunisia.html (+2 scripts)
✅ stage-1.html (+2 scripts)
✅ stage-2-maghreb.html (+2 scripts)
✅ [... 15 autres stage files ...]
✅ database/schema.sql (+4 tables)

═══════════════════════════════════════════════════════════════════════════════

🚀 NEXT STEPS
─────────────────────────────────────────────────────────────────────────────

1. Exécuter le SQL: schema.sql
2. Compléter Phase 3 (Dashboard integration)
3. Compléter Phase 4 (Stage files integration)
4. Tester les 6 cas d'usage
5. Déployer!

═══════════════════════════════════════════════════════════════════════════════

Pour toute question, consulter: FEATURES_v2.0_GUIDE.md
