# 🎮 MONDE MAGIQUE v2.0 - NOUVELLES FONCTIONNALITÉS

## 📋 TABLE DES MATIÈRES
1. [Vue d'ensemble](#vue-densemble)
2. [Fonctionnalités ajoutées](#fonctionnalités-ajoutées)
3. [Architecture technique](#architecture-technique)
4. [Installation & Configuration](#installation--configuration)
5. [Intégration dans les pages](#intégration-dans-les-pages)
6. [Guide d'utilisation](#guide-dutilisation)
7. [Dépannage](#dépannage)

---

## 🎯 Vue d'ensemble

**Version:** 2.0  
**Date:** 2024  
**Ajouts:** 4 nouvelles fonctionnalités majeures sans modifications du design actuel

### Les 4 Nouvelles Fonctionnalités:
1. ✅ **Certificats PDF + Badges** - Génération et partage après chaque stage
2. ✅ **Bouton "Découvrir Stage"** - Navigation automatique entre stages
3. ✅ **Personnage Guide Animé** - Assistant virtuel avec text-to-speech français
4. ✅ **Système de Diamants & Pièces** - Progression et récompenses avec base de données

---

## ✨ Fonctionnalités ajoutées

### 1. 🏆 Certificats PDF + Badges

**Fichier:** `/js/certificates.js`

**Fonctionnalités:**
- Génération de certificats PDF élégants après complément d'un stage
- Création de badges SVG circulaires avec étoile à 5 points
- Partage sur 4 plateformes: WhatsApp, Facebook, Twitter, Email
- Stockage des certificats dans la base de données

**Utilisation:**
```javascript
// Initialiser le générateur
const certGen = new CertificateGenerator();

// Générer un PDF
certGen.generatePDF('Ali Beyrambaye', 'Tunisia', 1);

// Générer un badge SVG
certGen.generateBadgeSVG('Tunisia', 1);

// Partager sur WhatsApp
certGen.shareOnSocial('whatsapp', 'Ali', 'Tunisia', 'https://...');
```

**API Endpoint:**
```
POST /api/stages/{id_user}/{num_stage}/complete-etape
```
Auto-génère le certificat lors de la completion du 5ème step.

---

### 2. 🧭 Bouton "Découvrir Stage"

**Fichier:** `/js/stage-navigator.js`

**Fonctionnalités:**
- Boutons de navigation entre les 10 stages
- Support des variantes de langue (English/French)
- Routage automatique vers le bon fichier (stage-{n}.html)
- Styling réactif (hover animations, responsive design)

**Utilisation:**
```javascript
// Initialiser les boutons sur le dashboard
StageNavigator.initButtons();

// Créer un bouton unique
StageNavigator.createStageButton(1, 'Tunisia', '🇹🇳');

// Ouvrir directement un stage
StageNavigator.openStage(2, 'french'); // or 'english'
```

**HTML à ajouter sur dashboard:**
```html
<div id="stage-navigation-buttons"></div>
```

Le script génère automatiquement les boutons avec: `stage-1.html`, `stage-2-maghreb.html`, etc.

---

### 3. 🤖 Personnage Guide Animé

**Fichier:** `/js/assistant.js`

**Fonctionnalités:**
- Bulle flottante avec personnage animé (SVG)
- Messages contextuels (7 types prédéfinis)
- Text-to-speech français natif (Web Speech API)
- Auto-masquage après 5 secondes
- Animation de rebond

**Utilisation:**
```javascript
// Initialiser l'assistant
const assistant = new AnimatedAssistant();
assistant.init();

// Afficher un message
assistant.show('welcome'); // 'stage_start', 'quiz_win', etc.

// Activer la parole
assistant.speak();

// Basculer la visibilité
assistant.toggle();
```

**Messages disponibles:**
- `welcome` - Bienvenue!
- `stage_start` - Commence ton stage
- `video_done` - Vidéo complétée!
- `quiz_start` - Quiz détecté
- `quiz_win` - Quiz réussi! 🎉
- `stage_complete` - Stage complété!
- `help` - Besoin d'aide?

---

### 4. 💎 Système de Diamants + Base de Données

**Fichier:** `/js/resources.js`

**Fonctionnalités:**
- Suivi des diamants et pièces d'or de chaque utilisateur
- Synchronisation localStorage ↔ Base de données
- Déduction automatique pour utiliser l'assistant
- Interface utilisateur en temps réel

**Utilisation:**
```javascript
// Initialiser le gestionnaire
const resources = new ResourceManager(userId);
resources.init();

// Ajouter des ressources
resources.add(5, 10); // +5 diamants, +10 pièces

// Utiliser des diamants
if (resources.useDiamants(3)) {
    // Utilisé avec succès
}

// Obtenir les valeurs actuelles
console.log(resources.diamants); // 25
console.log(resources.pieces);    // 100
```

**Affichage en page:**
```html
<div id="diamonds-count">10</div>
<div id="coins-count">0</div>
```

**Récompenses automatiques:**
- Stage complété: `30 + (num_stage × 5)` diamants
- Bonus pièces: x5 diamants = pièces
- Exemple Stage 1: 35 diamants + 175 pièces

---

## 🏗️ Architecture technique

### Structure des fichiers

```
monde-magique/
├── js/
│   ├── assistant.js              ✨ nouveau
│   ├── resources.js              ✨ nouveau
│   ├── certificates.js           ✨ nouveau
│   └── stage-navigator.js        ✨ nouveau
├── php/
│   ├── config.php                (existant)
│   └── api/
│       ├── users.php             ✨ nouveau
│       └── stages.php            ✨ nouveau
├── database/
│   └── schema.sql                (updated)
└── ...
```

### Base de données

**4 nouvelles tables:**

| Table | Rôle | Clé étrangère |
|-------|------|---------------|
| `utilisateurs_ressources` | Diamants & Pièces par utilisateur | users.id |
| `stages_completion_status` | Statut d'achèvement des stages | users.id |
| `etapes_completion_status` | Statut des steps individuels (1-5) | users.id |
| `certificats_badges` | Certificats générés & partages | users.id |

### Points d'intégration

**Dashboard:** 
- Initialise l'assistant
- Affiche les boutons de navigation
- Charge les ressources utilisateur

**Stage files:**
- Importent les ressources & certificats
- Appellent l'API lors de la completion des steps
- Génèrent les certificats à 5/5 steps

---

## 🔧 Installation & Configuration

### Étape 1: Configuration de la base de données

```bash
# Exécuter le script SQL d'initialisation
mysql -u root -p monde_magique < database/schema.sql
```

**Vérifier les tables créées:**
```sql
SHOW TABLES LIKE '%ressources%';
SHOW TABLES LIKE '%completion%';
SHOW TABLES LIKE '%certificat%';
```

### Étape 2: Vérifier config.php

Fichier: `/php/config.php`

```php
// Doit contenir la connexion PDO correcte:
$pdo = new PDO(
    'mysql:host=localhost;dbname=monde_magique;charset=utf8mb4',
    'root',
    'password'
);
```

### Étape 3: Vérifier les imports

**Dashboard:**
```html
<!-- À la fin du dashboard.html avant </body> -->
<script src="js/assistant.js"></script>
<script src="js/resources.js"></script>
<script src="js/certificates.js"></script>
<script src="js/stage-navigator.js"></script>
```

**Fichiers stage (DÉJÀ FAITS):**
```html
<!-- À la fin de chaque stage-*.html -->
<script src="js/resources.js"></script>
<script src="js/certificates.js"></script>
```

---

## 📖 Intégration dans les pages

### Dashboard.html

**Ce qui a été fait:**
✅ Scripts ajoutés avant `</body>`

**À faire manuellement:**
1. Ajouter conteneur pour les diamants:
```html
<!-- Dans .sidebar ou .header -->
<div class="resources-display">
    <span id="diamonds-count">10</span> 💎
    <span id="coins-count">0</span> 🪙
</div>
```

2. Ajouter conteneur pour boutons de navigation:
```html
<!-- Dans la section des stages -->
<div id="stage-navigation-buttons"></div>
```

3. Initialiser au chargement:
```javascript
document.addEventListener('DOMContentLoaded', () => {
    // Votre code existant...
    
    // Initialiser ressources
    const resourceManager = new ResourceManager(window.userId || 1);
    resourceManager.init();
    
    // Initialiser assistant
    const assistant = new AnimatedAssistant();
    assistant.init();
    
    // Initialiser navigation
    StageNavigator.initButtons();
});
```

### Stage files (stage-*.html)

**Ce qui a été fait:**
✅ Scripts ajoutés avant `</body>`

**À faire manuellement:**

1. Au début de la section de script existante:
```javascript
// Obtenir l'ID utilisateur (de session ou URL)
const userId = window.userId || 1; // À adapter selon votre système d'auth

// Initialiser les ressources
const resourceManager = new ResourceManager(userId);
resourceManager.init();
```

2. Au moment de la completion de step:
```javascript
// Dans la fonction goToStep ou équivalent
async function completeStep(stageNum, stepNum, score) {
    // Votre logique existante...
    
    // Appeler l'API pour enregistrer la completion
    try {
        const response = await fetch(`/php/api/stages/${userId}/${stageNum}/complete-etape`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                num_etape: stepNum,
                type_etape: 'qcm', // ou 'video', 'poster', 'pdf', 'essay'
                score: score
            })
        });
        
        const data = await response.json();
        
        // Si stage complété
        if (data.stage_complete) {
            // Générer certificat
            const certGen = new CertificateGenerator();
            certGen.generatePDF(
                window.userName || 'Student',
                data.stage_name,
                stageNum
            );
            
            // Afficher message
            resourceManager.assistant.show('stage_complete');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
```

3. Lors du test du QCM/essai:
```javascript
function evaluateQuiz(answers, correctAnswers) {
    const score = calculateScore(answers, correctAnswers);
    
    // Afficher le score
    const rewards = calculateRewards(score, currentStage);
    
    // Ajouter les ressources
    resourceManager.add(rewards.diamants, rewards.pieces);
    
    // Lancer l'assistant
    assistant.show('quiz_win');
    assistant.speak(); // Optionnel
    
    return score;
}
```

---

## 📚 Guide d'utilisation

### Pour les administrateurs

**Vérifier la progression d'un utilisateur:**
```sql
-- Dashboard d'admin
SELECT u.*, sr.diamants, sr.pieces, sr.progression
FROM users u
LEFT JOIN utilisateurs_ressources sr ON u.id = sr.id_user
WHERE u.username = 'test_user';

-- Stages complétés
SELECT * FROM stages_completion_status 
WHERE id_user = 1 AND statut = 'termine';

-- Certificats générés
SELECT * FROM certificats_badges
WHERE id_user = 1;
```

### Pour les utilisateurs

**Obtenir un certificat:**
1. Compléter un stage en finissant tous les 5 steps
2. Un certificat PDF est généré automatiquement
3. Bouton "Télécharger" ou "Partager" apparaît
4. Partager sur WhatsApp, Facebook, Twitter ou Email

**Utiliser l'assistant:**
1. Une bulle flottante apparaît en bas à droite
2. Cliquer pour écouter les messages (TTS français)
3. Auto-masquage après 5 secondes

**Accumuler des ressources:**
- Chaque étape complétée = points
- Stage complété = diamants + pièces
- Utiliser l'assistant = -3 diamants

---

## 🐛 Dépannage

### L'assistant ne parle pas

**Solutions:**
1. Vérifier que le navigateur supporte Web Speech API (Chrome, Edge, Safari)
2. Vérifier que le volume du navigateur n'est pas muet
3. Activer les notifications sonores dans les paramètres

### Les ressources ne se synchronisent pas

**Solutions:**
1. Vérifier `/php/api/users.php` et `/php/api/stages.php` sont accessibles
2. Vérifier la configuration de la base de données dans `config.php`
3. Vérifier les permissions utilisateur MySQL
4. Vérifier les logs PHP (`error_log`)

### Les certificats ne se génèrent pas

**Solutions:**
1. Vérifier que jsPDF est chargé depuis le CDN
2. Vérifier que `CertificateGenerator` est initialisée
3. Vérifier les permissions du dossier `/assets/images/`

### Les boutons de navigation ne s'affichent pas

**Solutions:**
1. Vérifier que `stage-navigator.js` est chargé
2. Vérifier qu'il existe un `<div id="stage-navigation-buttons"></div>`
3. Vérifier que `StageNavigator.initButtons()` est appelé

---

## 📞 Support

Pour toute question ou problème:
1. Vérifier les logs du navigateur (F12 → Console)
2. Vérifier les logs PHP (`xampppp/apache/logs/`)
3. Consulter les commentaires dans les fichiers JS/PHP

---

**Dernière mise à jour:** 2024  
**Auteur:** Monde Magique Development Team
