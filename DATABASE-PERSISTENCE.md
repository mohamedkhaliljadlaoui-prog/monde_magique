# 💾 SYSTÈME DE PERSISTANCE - Base de Données

## Vue d'ensemble

Le système de progression a été migré vers une **base de données MySQL** pour persister les données même après fermeture du navigateur.

### Architecture

```
Frontend (dashboard.html, profile.html)
    ↓
JavaScript localStorage + Session
    ↓
PHP API Endpoints
    ↓
MySQL Database (user_stage_progression, stage_scores)
```

---

## 🔧 Mise en Place

### 1. Créer les tables de base de données

**Automatique:** Appelez une fois au chargement:
```javascript
fetch('php/api/init-db.php')
    .then(r => r.json())
    .then(data => console.log(data.message));
```

**Ou Manual:** Exécutez directement le fichier SQL:
```bash
mysql -u root monde_magique < database/migration-progression-tables.sql
```

### 2. Tables créées

#### `user_stage_progression`
- **Stocke:** La progression complète en JSON par utilisateur
- **Colonnes:**
  - `user_id` (INT): Référence à users.id
  - `progression_data` (JSON): Objet avec structure `{tunisia: {passed, score, attempts, lastAttempt}, ...}`
  - `created_at`, `updated_at`: Timestamps

#### `stage_scores`
- **Stocke:** Chaque tentative de stage avec son score
- **Colonnes:**
  - `user_id` (INT)
  - `stage_key` (VARCHAR): CLÉ du stage (tunisia, maghreb, etc.)
  - `score` (INT): Score 0-100
  - `attempts` (INT): Numéro de tentative
  - `attempted_at` (TIMESTAMP)

---

## 📡 API Endpoints

### 1. `save-progress.php` - Sauvegarder la progression

**Méthode:** POST

**Payload JSON:**
```json
{
  "userId": "123",
  "stageKey": "tunisia",
  "score": 85,
  "attempts": 1,
  "progression": {
    "tunisia": {"passed": true, "score": 85, "attempts": 1, "lastAttempt": "2024-01-01T..."},
    "maghreb": {"passed": false, "score": 70, "attempts": 1, "lastAttempt": "2024-01-01T..."}
  }
}
```

**Réponse:**
```json
{
  "success": true,
  "message": "Progression sauvegardée ✅",
  "level": 2,
  "totalXp": 1200,
  "totalCoins": 150,
  "totalDiamonds": 18,
  "completedStages": 2
}
```

**Appelé depuis:** dashboard.html `saveProgressToDatabase()`

---

### 2. `get-progress.php` - Récupérer la progression

**Méthode:** GET

**Params:**
- `userId`: ID de l'utilisateur (URL encoded)

**Exemple:**
```
GET php/api/get-progress.php?userId=123
```

**Réponse:**
```json
{
  "success": true,
  "user": {
    "id": 123,
    "username": "ahmed123",
    "guide_name": "Ahmed",
    "level": 2,
    "xp": 1200,
    "coins": 150,
    "diamonds": 18
  },
  "progression": {
    "tunisia": {"passed": true, "score": 85, ...},
    "maghreb": {"passed": false, "score": 70, ...}
  },
  "stats": {
    "total_attempts": 12,
    "average_score": 76.5,
    "completed_stages": 2
  }
}
```

**Appelé depuis:** profile.html `loadProfileFromDatabase()`

---

### 3. `init-db.php` - Initialiser les tables

**Méthode:** GET

**Réponse (succès):**
```json
{
  "success": true,
  "message": "✅ Tables de progression créées avec succès!",
  "tables_created": ["user_stage_progression", "stage_scores"]
}
```

**Réponse (déjà fait):**
```json
{
  "success": true,
  "message": "✅ Base de données déjà initialisée",
  "status": "already_initialized"
}
```

---

## 🎮 Flux de Données

### Lors de la complétion d'une étape:

1. **Frontend (ifame → stage-**.html)**
   - QCM complété
   - Envoie: `postMessage({type: 'stage_complete', stageKey, score})`

2. **Dashboard.html**
   - Reçoit message from iframe
   - Appelle: `saveStageCompletion(stageKey, score)`
   - Met à jour: localStorage `stage_progression`
   - **APPELLE:** `saveProgressToDatabase(userId, stageKey, score, progression)`

3. **save-progress.php (Backend)**
   - Reçoit: userId, stageKey, score, progression JSON
   - **Insère:** stage_scores (une ligne par tentative)
   - **UPSERT:** user_stage_progression (met à jour si existe)
   - **Calcul:** Récompenses totales + Level
   - **UPDATE:** users table avec level, xp, coins, diamonds

4. **Base de données MySQL**
   - Données persistées! 💾

---

### Lors du chargement du profil:

1. **profile.html `loadUserProfile()`**
   - Récupère: userId from localStorage
   - **APPELLE:** `loadProfileFromDatabase(userId)`

2. **get-progress.php (Backend)**
   - SELECT user data from users table
   - SELECT progression JSON from user_stage_progression
   - SELECT tous les scores from stage_scores
   - Retourne: structure complète

3. **profile.html**
   - Parse réponse
   - Affiche: level, achievements, stages progress, stats
   - **OU fallbacks** à localStorage si BD non disponible

---

## 🔄 Synchronisation

### Données Frontend (localStorage):
- **stage_progression:** Utilisé pour affichage rapide
- **user_data:** Profil utilisateur
- **chatbot_messages_count:** Nombre de questions

### Données Backend (MySQL):
- **user_stage_progression:** Source de vérité pour progression
- **stage_scores:** Historique complet des tentatives
- **users table:** Profil + récompenses

### Synchronisation:
- ✅ Dashboard → BD: Automatique après chaque stage
- ✅ Profile ← BD: Au chargement de la page

---

## 🛡️ Sécurité

- ✅ **Parameterized Queries:** Toutes les requêtes utilisent `?` placeholders
- ✅ **Transaction Atomicity:** RollBack en cas d'erreur
- ✅ **JSON Validation:** progression JSON est parsé/validé
- ✅ **Score Check:** 0-100 range validated
- ✅ **FK constraints:** user_id vérifié dans users table

---

## 📊 Calcul des Récompenses

```
Récompenses par stage (hardcodé dans save-progress.php):
tunisia:    coins: 50,  diamonds: 10, xp: 500
maghreb:    coins: 60,  diamonds: 12, xp: 600
africa:     coins: 70,  diamonds: 14, xp: 700
europe:     coins: 80,  diamonds: 16, xp: 800
asia:       coins: 90,  diamonds: 18, xp: 900
namerica:   coins: 100, diamonds: 20, xp: 1000
samerica:   coins: 110, diamonds: 22, xp: 1100
oceania:    coins: 120, diamonds: 24, xp: 1200
poles:      coins: 130, diamonds: 26, xp: 1300
world:      coins: 150, diamonds: 30, xp: 1500

TOTAL au 100%: 950 coins, 177 diamonds, 9500 xp
```

---

## 🐛 Troubleshooting

### "get-progress.php returns error"
- ✅ Vérifier userId est correct
- ✅ Vérifier tables existent: `SHOW TABLES;`
- ✅ Vérifier Database connexion: `php/config/database.php`

### "save-progress.php fails"
- ✅ Vérifier `user_stage_progression` table existe
- ✅ Vérifier `stage_scores` table existe
- ✅ Vérifier user_id existe dans `users` table
- ✅ Check logs: inspect browser console + server logs

### "Profile data not loading"
- ✅ Vérifier localStorage userId
- ✅ Vérifier init-db.php a été exécuté
- ✅ Fallback à localStorage fonctionne automatiquement

### "Progression not saved"
- ✅ Vérifier Network tab: POST à save-progress.php répond
- ✅ Check PHP error logs
- ✅ Vérifier connexion BD active

---

## 📝 Notes

- **userId:** Actuellement générée comme `guide_name` or fallback à `'guest_' + timestamp`
  - À améliorer: Utiliser ID authentifié depuis session
  
- **Récompenses:** Hardcodées dans save-progress.php
  - À améliorer: Lire depuis `stages` table avec colonne `reward_coins`

- **Transactions:** `beginTransaction()` et `commit()` utilisés
  - Rollback automatique en cas d'erreur

---

## 🚀 Prochaines Étapes

1. ✅ Tables créées
2. ✅ API endpoints complètes
3. ✅ Dashboard sauvegarde automatique
4. ✅ Profile charge depuis BD
5. ⏳ TODO: Authentification robuste avec userId
6. ⏳ TODO: Parent dashboard voir progression enfants
7. ⏳ TODO: Analytics et leaderboards

---

**Créé:** Janvier 2026  
**Status:** ✅ En production  
**Version:** 1.0

