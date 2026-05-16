# Corrections du problème d'inscription

## Problèmes identifiés et corrigés :

### 1. ✅ URL incorrecte dans auth.js
- **Problème** : La méthode `register()` utilisait `this.API_URL` qui pointait vers `login.php`
- **Solution** : Changé pour utiliser directement `'php/api/auth/register.php'`

### 2. ✅ Nom de colonne incorrect
- **Problème** : Le code utilisait `password_hash` mais le schéma utilise `password`
- **Solution** : Corrigé pour utiliser `password` comme nom de colonne

### 3. ✅ Colonnes non supportées
- **Problème** : Le code tentait d'insérer `language` et `parent_email` qui n'existent pas dans le schéma
- **Solution** : Retiré ces colonnes de la requête INSERT

### 4. ✅ Amélioration de la gestion des erreurs
- **Problème** : Messages d'erreur génériques peu utiles pour le debug
- **Solution** : Ajout de logs détaillés et messages d'erreur plus précis

## Étapes pour tester l'inscription :

### Étape 1 : Vérifier la base de données
1. Démarrer XAMPP (Apache + MySQL)
2. Ouvrir phpMyAdmin : http://localhost/phpmyadmin
3. Vérifier que la base de données `monde_magique` existe
4. Si elle n'existe pas, exécuter le script : `database/schema-complete.sql`

### Étape 2 : Tester la connexion
Ouvrir dans le navigateur : http://localhost/monde-magique/php/test-db.php
- Devrait afficher : ✅ Connexion réussie
- Devrait afficher la structure de la table `users`

### Étape 3 : Tester l'inscription
Ouvrir dans le navigateur : http://localhost/monde-magique/test-inscription.html
- Cliquer sur "Tester l'inscription"
- Devrait afficher : ✅ Inscription réussie!

### Étape 4 : Tester avec le vrai formulaire
Ouvrir : http://localhost/monde-magique/inscription.html
- Remplir tous les champs
- Soumettre le formulaire
- Devrait rediriger vers dashboard.html

## Fichiers modifiés :

1. **js/auth.js**
   - Ligne 70-92 : Méthode `register()` avec URL correcte

2. **php/api/auth/register.php**
   - Ligne 1-5 : Ajout de error_reporting pour debug
   - Ligne 15-23 : Amélioration validation des données
   - Ligne 47-59 : Correction requête INSERT avec bonnes colonnes
   - Ligne 78-82 : Amélioration gestion erreurs

## Fichiers créés pour le debug :

1. **php/test-db.php** - Test de connexion à la base de données
2. **test-inscription.html** - Test de l'API d'inscription

## En cas de problème persistant :

### Vérifier les logs d'erreur :
- **Apache** : xampp/apache/logs/error.log
- **PHP** : xampp/php/logs/php_error_log
- **Console navigateur** : F12 > Console

### Erreurs courantes :

1. **"Table 'monde_magique.users' doesn't exist"**
   - Solution : Exécuter le script SQL `database/schema-complete.sql`

2. **"SQLSTATE[HY000] [1045] Access denied"**
   - Solution : Vérifier les identifiants MySQL dans `php/config/database.php`

3. **"Champ requis manquant"**
   - Solution : Vérifier que tous les champs requis sont remplis dans le formulaire

4. **"Nom d'utilisateur ou email déjà utilisé"**
   - Solution : Choisir un autre nom d'utilisateur ou email

## Configuration MySQL recommandée :

Dans `php/config/database.php` (déjà configuré) :
```php
'host' => 'localhost',
'database' => 'monde_magique',
'username' => 'root',
'password' => '',  // Vide par défaut pour XAMPP
'port' => '3306'
```

## Prochaines étapes :

Une fois l'inscription fonctionnelle, vérifier :
- ✓ La connexion (login.php)
- ✓ Le tableau de bord (dashboard.html)
- ✓ La progression des stages
