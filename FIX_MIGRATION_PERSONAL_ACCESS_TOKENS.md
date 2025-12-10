# 🔧 FIX: Erreur migration personal_access_tokens sur dev.etapsup.org

**Date**: 10 décembre 2025
**Erreur**: `SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'personal_access_tokens' already exists`
**Cause**: Table existe en base mais pas enregistrée dans table `migrations`

---

## 🎯 Solution rapide (sur serveur)

### Étape 1: Vérifier l'état des migrations

```bash
cd /home/etapsup-dev/htdocs/dev.etapsup.org
php artisan migrate:status
```

**Vous devriez voir** :
```
Migration name                                           Batch / Status
2025_11_30_184937_create_personal_access_tokens_table   [Pending] ⚠️
2025_12_10_120331_fix_programs_duration_column          [Pending]
2025_12_10_120718_change_cities_region_to_country       [Pending] ⚠️ CRITIQUE
2025_12_10_133827_create_failed_jobs_table              [Pending]
```

---

### Étape 2: Marquer personal_access_tokens comme exécutée

Puisque la table existe déjà, on l'enregistre manuellement :

```bash
php artisan tinker
```

Puis dans Tinker :
```php
DB::table('migrations')->insert([
    'migration' => '2025_11_30_184937_create_personal_access_tokens_table',
    'batch' => 1
]);
exit
```

**OU** via une seule commande :
```bash
php artisan tinker --execute="DB::table('migrations')->insert(['migration' => '2025_11_30_184937_create_personal_access_tokens_table', 'batch' => 1]);"
```

---

### Étape 3: Exécuter les migrations restantes

```bash
php artisan migrate --force
```

**Output attendu** :
```
Running migrations.
2025_12_10_120331_fix_programs_duration_column ............ DONE
2025_12_10_120718_change_cities_region_to_country ......... DONE ✅ CRITIQUE
2025_12_10_133827_create_failed_jobs_table ................ DONE
```

---

### Étape 4: Nettoyer les caches

```bash
php artisan optimize:clear
```

---

## ✅ Vérifier que le déploiement est complet

### 1️⃣ Vérifier les migrations
```bash
php artisan migrate:status | grep Pending
```
**Attendu**: Aucune ligne (toutes les migrations sont `[DONE]`)

### 2️⃣ Vérifier la version du code
```bash
git log -1 --oneline
```
**Attendu**: `08e03f9 fix: downgrade openspout vers 4.24 pour compatibilité PHP 8.2`

### 3️⃣ Vérifier la structure de la base

```bash
php artisan tinker --execute="DB::select('SHOW COLUMNS FROM cities LIKE \'country_id\'');"
```
**Attendu**: Retourne un résultat (colonne `country_id` existe)

### 4️⃣ Tester les pages critiques

```bash
# Test HTTP 200 sur pages principales
curl -s -o /dev/null -w "%{http_code}\n" https://dev.etapsup.org/
curl -s -o /dev/null -w "%{http_code}\n" https://dev.etapsup.org/establishments
curl -s -o /dev/null -w "%{http_code}\n" https://dev.etapsup.org/accueil
curl -s -o /dev/null -w "%{http_code}\n" https://dev.etapsup.org/admin
```
**Attendu**: Toutes les commandes retournent `200`

---

## 📊 Checklist de vérification complète

- [ ] Migration `personal_access_tokens` marquée comme exécutée
- [ ] `php artisan migrate:status` → Aucune migration `[Pending]`
- [ ] `git log -1` → Commit `08e03f9` (downgrade openspout)
- [ ] `composer show openspout/openspout` → Version `4.24.0`
- [ ] Colonne `cities.country_id` existe en base
- [ ] Page `/` retourne HTTP 200
- [ ] Page `/establishments` retourne HTTP 200
- [ ] Page `/accueil` retourne HTTP 200
- [ ] Page `/admin` retourne HTTP 200
- [ ] Aucune erreur dans `storage/logs/laravel.log`

---

## 🚨 Si l'erreur persiste

### Vérifier si la table existe vraiment
```bash
php artisan tinker --execute="Schema::hasTable('personal_access_tokens');"
```
**Si retourne `true`** → Table existe, utilisez la solution ci-dessus
**Si retourne `false`** → Table n'existe pas, supprimez l'entrée de migrations et relancez

### Forcer la recréation de la table
```bash
# Supprimer l'entrée des migrations
php artisan tinker --execute="DB::table('migrations')->where('migration', '2025_11_30_184937_create_personal_access_tokens_table')->delete();"

# Supprimer la table si elle existe
php artisan tinker --execute="Schema::dropIfExists('personal_access_tokens');"

# Relancer les migrations
php artisan migrate --force
```

---

## 📝 Résumé de la situation

### Avant (état incohérent)
- ✅ Table `personal_access_tokens` existe en base
- ❌ Migration pas enregistrée dans table `migrations`
- ❌ Migrations bloquées par erreur "table already exists"

### Après (état cohérent)
- ✅ Table `personal_access_tokens` existe en base
- ✅ Migration enregistrée dans table `migrations`
- ✅ Toutes les migrations suivantes exécutées avec succès
- ✅ Site fonctionnel avec dernières modifications

---

**État final attendu**: ✅ DÉPLOIEMENT COMPLET - Toutes migrations exécutées
