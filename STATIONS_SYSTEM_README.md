# 🎮 Système de Stations avec Tour du Monde

## ✨ Nouvelles Fonctionnalités

### 1. **Système de Stations de Train** 🚂

Les stages sont maintenant organisés comme des **stations de train** :

#### Caractéristiques :
- ✅ **10 stations progressives** - De تونس à العالم
- ✅ **Verrouillage progressif** - Chaque station débloque la suivante
- ✅ **Animations de train** - Effet pulse et mouvement
- ✅ **Ligne de progression** - Chemin ferroviaire visible sur la carte
- ✅ **3 états visuels** :
  - 🔒 **Verrouillée** (gris) - Pas encore accessible
  - 🚂 **Active** (orange) - Station en cours avec animation
  - ✓ **Complétée** (vert) - Terminée avec succès

#### Dossiers Créés :
```
stages/
  ├── tunisia/        (Station 1 - Débloquée par défaut)
  ├── maghreb/        (Station 2 - Se débloque après tunisia)
  ├── africa/         (Station 3)
  ├── europe/         (Station 4)
  ├── asia/           (Station 5)
  ├── namerica/       (Station 6)
  ├── samerica/       (Station 7)
  ├── oceania/        (Station 8)
  ├── poles/          (Station 9)
  └── world/          (Station 10 - Boss final)
```

### 2. **Profile Utilisateur Complet** 👤

Sidebar avec toutes les informations :

#### Profil :
- 👦👧 **Avatar animé** - Basé sur le genre
- 📊 **Nom d'utilisateur** - Personnalisé
- 🎯 **Niveau et âge** - Affichage dynamique
- 📈 **Statistiques en grille** :
  - ⭐ XP (Points d'expérience)
  - 💰 Coins (Pièces d'or)
  - 💎 Diamonds (Diamants)

### 3. **Actions Rapides** ⚡

4 boutons d'accès rapide :

#### Fonctionnalités :
- 🛒 **Boutique** (Shop) - Acheter items avec coins
- 👤 **Profil** - Page profil complète
- 🏆 **Classement** (Leaderboard) - Top 5 joueurs
- 🏅 **Réalisations** - Achievements (à venir)

### 4. **Système de Boutique** 🛒

Modal avec items achetables :

#### Items Disponibles :
- 💡 **Hint** (50💰) - Indice pour les questions
- ⚡ **Boost XP** (100💰) - Double XP pendant 1h
- 🎭 **Avatar** (200💰) - Personnages spéciaux
- 🎨 **Thème** (300💰) - Couleurs personnalisées

#### Fonctionnement :
```javascript
// Vérification automatique du solde
if (coins < price) {
    showToast('Pas assez de pièces! 💰', 'error');
    return;
}
// Achat et mise à jour
userData.coins -= price;
showToast('Achat réussi! ✓', 'success');
```

### 5. **Leaderboard (Classement)** 🏆

Modal avec top joueurs :

#### Affichage :
- 🥇 **1er** - Badge or avec gradient
- 🥈 **2ème** - Badge argent
- 🥉 **3ème** - Badge bronze
- 4-5 - Badges standards

#### Format :
```
👦 أحمد المغامر - 5000 points
👧 فاطمة البطلة - 4500 points
```

### 6. **Système de Récompenses** 🎁

Chaque station offre des récompenses croissantes :

| Station | Coins💰 | XP⭐ | Diamonds💎 |
|---------|---------|-----|-----------|
| تونس    | 100     | 50  | 5         |
| المغرب  | 150     | 75  | 10        |
| أفريقيا | 200     | 100 | 15        |
| أوروبا  | 250     | 125 | 20        |
| آسيا    | 300     | 150 | 25        |
| أمريكا الشمالية | 350 | 175 | 30 |
| أمريكا الجنوبية | 400 | 200 | 35 |
| أوقيانوسيا | 450 | 225 | 40 |
| القطبان | 500 | 250 | 45 |
| العالم  | 1000    | 500 | 100       |

### 7. **Chemin Ferroviaire sur Carte** 🗺️

Visualisation du parcours :

#### Éléments :
- 📍 **Ligne pointillée orange** - Relie toutes les stations
- 🔒 **Cadenas** - Stations verrouillées
- 🚂 **Train** - Station active
- ✓ **Coche** - Stations complétées

### 8. **Chatbot Amélioré** 🤖

Questions spécifiques au système :

#### Nouvelles Réponses :
```
👤 "كيف أفتح المحطات؟"
🤖 "لفتح محطة جديدة، يجب عليك إكمال المحطة السابقة بنجاح!"

👤 "ما هي المكافآت؟"
🤖 "كل محطة تمنحك: 💰 عملات، ⭐ XP، 💎 ألماس"

👤 "كيف أربح نقاط؟"
🤖 "اربح النقاط عن طريق: إكمال المحطات، الإجابة الصحيحة..."
```

## 🎯 Logique de Progression

### Comment Débloquer les Stations :

1. **Démarrage** : Station 1 (تونس) débloquée
2. **Jouer** : Compléter تونس avec succès
3. **Déblocage** : Station 2 (المغرب) se débloque automatiquement
4. **Continuer** : Répéter jusqu'à la station finale

### Code de Déblocage :
```javascript
// Quand une station est complétée
station.completed = true;
station.progress = 100;

// Débloquer la suivante
const nextIndex = STATIONS.findIndex(s => s.id === stationId) + 1;
if (nextIndex < STATIONS.length) {
    STATIONS[nextIndex].unlocked = true;
}
```

### Sauvegarde de Progression :
```javascript
// Stockage dans localStorage
const progress = {
    tunisia: { completed: true, progress: 100, score: 95 },
    maghreb: { completed: false, progress: 60, score: 0 }
};
localStorage.setItem('stage_progress', JSON.stringify(progress));
```

## 📱 Interface Responsive

### Sidebar (400px) :
- 📋 Profile en haut
- ⚡ 4 boutons d'action
- 🚂 Liste des stations défilante
- 📏 Barre de progression par station

### Map (Flex 1) :
- 🗺️ Carte Leaflet plein écran
- 🛤️ Chemin ferroviaire visible
- 📍 10 marqueurs interactifs
- 💬 Popups avec infos et bouton

## 🎨 Design & Animations

### Stations :
- **Locked** : Opacité 0.6, curseur not-allowed
- **Active** : Animation pulse orange, train animé
- **Completed** : Checkmark vert, barre 100%

### Effets :
- 🔄 **Pulse** - Sur stations actives
- 🚂 **Train bounce** - Icône qui bouge
- 📊 **Progress bar** - Remplissage animé
- ⬆️ **Hover lift** - Élévation au survol

## 🚀 Utilisation

### Tester :
1. Ouvrir `index-new.html`
2. Voir le profil en haut de la sidebar
3. Cliquer sur Station 1 (تونس) - Seule débloquée
4. Cliquer "ابدأ المغامرة 🚀"
5. Le jeu s'ouvre en iframe
6. À la fin, appeler `completeStation('tunisia', score)`
7. Station 2 (المغرب) se débloque automatiquement!

### Compléter une Station depuis le Jeu :
```javascript
// À ajouter dans jeux.html
function finishGame(stageId, finalScore) {
    // Appeler la fonction parent
    if (window.parent && window.parent.completeStation) {
        window.parent.completeStation(stageId, finalScore);
    }
}
```

## 📂 Structure des Fichiers

### Fichiers Principaux :
- **`index-new.html`** - Page principale avec tout le système
- **`index.html`** - Redirige vers index-new.html
- **`login.html`** - Redirige vers index-new.html après login
- **`jeux.html`** - Jeu qui reçoit `?stage=tunisia`

### Dossiers Stages :
Chaque dossier peut contenir :
```
stages/tunisia/
  ├── questions.json      (Questions spécifiques)
  ├── images/             (Images du pays)
  ├── audio/              (Sons locaux)
  └── data.json           (Infos sur le pays)
```

## 🐛 Débogage

### Réinitialiser la Progression :
```javascript
// Dans la console du navigateur
localStorage.removeItem('stage_progress');
localStorage.setItem('user_data', JSON.stringify({
    coins: 0,
    xp: 0,
    diamonds: 0,
    level: 1
}));
location.reload();
```

### Débloquer Toutes les Stations :
```javascript
// Pour tester
STATIONS.forEach(s => {
    s.unlocked = true;
    s.completed = true;
    s.progress = 100;
});
renderStations();
```

## 🎓 Prochaines Étapes

1. **Créer `questions.json`** dans chaque dossier stage
2. **Adapter `jeux.html`** pour charger questions depuis dossiers
3. **Ajouter page Achievements** avec médailles
4. **Système de niveaux** (Level up tous les 100 XP)
5. **Animations de célébration** quand station complétée
6. **Sons** pour déblocage/complétion
7. **Mode parent** pour suivre progression

---

**Créé le :** 4 février 2026  
**Version :** 3.0 - Système de Stations  
**Fichier principal :** `index-new.html`  
**Statut :** ✅ Opérationnel
