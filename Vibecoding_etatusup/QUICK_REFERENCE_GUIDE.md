# ⚡ QUICK REFERENCE - TRAITEMENT DEMANDES ETATSUP

## 🚀 **WORKFLOW 3-ÉTAPES POUR PREMIER SHOT RÉUSSI**

### **⏱️ ÉTAPE 1 : PREP (5min)**
```bash
☐ Lire TOUS les fichiers demande_* dans l'ordre
☐ Créer todo exhaustive (1 tâche = 1 action précise)
☐ Identifier mots-clés : "au lieu de", "remplace", "seulement", "uniquement"
☐ Vérifier demandes répétées des versions précédentes
```

### **⚡ ÉTAPE 2 : EXEC (15-30min)**
```bash
☐ 1 modification → 1 vérification immédiate
☐ Strings longues et uniques pour MultiEdit
☐ Marquer todo "completed" IMMÉDIATEMENT après chaque action
☐ JAMAIS passer à la suite sans finir la tâche courante
```

### **✅ ÉTAPE 3 : VALID (5min)**
```bash
☐ Lancer agent de vérification qualité automatiquement
☐ Corriger problèmes détectés si score < 95%
☐ Confirmer 100% coverage des demandes
☐ Mentionner que agent de vérification a validé
```

---

## 🎨 **BRAND CONSTANTS ETATSUP**

### **Textes Standards**
```markdown
✅ "étudier à l'étranger" (JAMAIS "en France")
✅ "EtatSup" (JAMAIS "Mareza" ou "YOD INVEST")
✅ "votre rêve" (JAMAIS "Votre Rêve")
✅ "@2025" dans footers
✅ "Ils nous ont fait confiance" (testimonials)
```

### **Couleurs Brand**
```css
/* Gradients bleus (hero, footer CTA) */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Gradients rouges (boutons, icônes, accents) */
background: linear-gradient(45deg, #ed2939, #cc1f2d);
```

---

## 🛠 **PATTERNS TECHNIQUES RÉCURRENTS**

### **Layout System**
```vue
// TOUJOURS pour pages événementielles
defineOptions({
  layout: EventLayout  // Évite footer YOD INVEST
})
```

### **Form Success Pattern**
```php
// Redirection vers page dédiée (pas popup)
return redirect()->route('events.thanks')->with([...]);
```

### **Modification Sélective**
```markdown
⚠️ "Couleurs hero + footer CTA" = SEULEMENT ces 2 sections
⚠️ "Garde les autres éléments en rouge" = Ne pas tout changer
⚠️ "Masquer lien connexion" = display:none, pas suppression
```

---

## 🚨 **RED FLAGS CRITIQUES**

### **Signaux d'Échec**
```markdown
❌ Client dit "Tu n'as pas tout appliqué"
❌ Client dit "Je répète 3 fois la même demande"
❌ Agent qualité < 95%
❌ Footer YOD INVEST encore visible
❌ Textes "Mareza" ou "en France" restants
```

### **Auto-Checks Obligatoires**
```markdown
☐ Chaque modification = contexte vérifié (5 lignes avant/après)
☐ MultiEdit conflicts évités avec strings spécifiques
☐ Pas de régression sur sections non-demandées
☐ EventLayout utilisé (pas de layout:false)
```

---

## 📋 **TEMPLATE TODO OPTIMAL**

```markdown
DEMANDE VX - [Date]
☐ TÂCHE X.1: [Action précise] - [Fichier:ligne]
☐ TÂCHE X.2: [Action précise] - [Fichier:ligne]
☐ TÂCHE X.3: [Action précise] - [Fichier:ligne]
☐ Vérification qualité agent
```

**Exemple concret :**
```markdown
DEMANDE V6 - 29/09/2025
☐ TÂCHE 1: Changer hero gradient rouge→bleu - EventLanding.vue:564
☐ TÂCHE 2: Changer footer CTA gradient rouge→bleu - EventLanding.vue:1021
☐ TÂCHE 3: Créer page RemerciementEvent.vue avec design spécifié
☐ TÂCHE 4: Ajouter route /remerciement_event - web.php
☐ TÂCHE 5: Modifier redirection EventController.php
☐ Vérification qualité agent
```

---

## 💡 **SHORTCUTS MENTAUX**

### **Parsing Rapide**
```markdown
"au lieu de" → Remplacement exact
"ajoute" → Nouveau contenu
"retire/supprime" → Masquer ou commenter
"garde" → Ne pas modifier
"seulement/uniquement" → Modification très ciblée
```

### **Priorisation**
```markdown
1. Corrections répétées des demandes précédentes
2. Changements brand (textes Mareza→EtatSup)
3. Modifications couleurs/design
4. Nouvelles fonctionnalités
5. Améliorations UX/performance
```

---

## 🎯 **SUCCESS CHECKLIST FINAL**

```markdown
☐ Tous les todos "completed"
☐ Agent qualité score ≥ 95%
☐ Aucun texte Mareza/YOD INVEST visible
☐ Couleurs cohérentes avec brand EtatSup
☐ Layout EventLayout utilisé partout
☐ Parcours utilisateur fonctionnel bout en bout
☐ Responsive design préservé
☐ Aucune demande précédente ignorée
```

---

**⚡ RAPPEL CRITIQUE : L'objectif est 0 itération, 100% réussite au premier shot !**