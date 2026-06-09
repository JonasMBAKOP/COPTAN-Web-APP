# Guide de Test Manuel - Étapes 4.5 et 4.6

## 🚀 Préparation

1. Assurez-vous que le serveur Laravel est en cours d'exécution:
   ```
   php artisan serve --host=127.0.0.1 --port=8000
   ```

2. Connectez-vous avec un compte admin (ou directeur)

3. Assurez-vous qu'une année académique **active** existe (ex: 2025-2026)

---

## ✅ Test 1: Créer un Staff (Personnel)

### Flux:
1. Allez à: Personnel → Nouveau Membre
2. Remplissez les informations:
   - Nom: `KAMGA`
   - Prénom: `Jean-Paul`
   - Genre: `Masculin`
   - Date de naissance: `15/01/1990`
   - Téléphone: `237 99 999 99 99`
   - Email: `jean.kamga@test.cm`
   - Diplôme: `Master en Mathématiques`
   - Date d'embauche: `01/09/2020`
   - Type de contrat: `Permanent`
   - État: `Actif` (coché)

3. **Sélectionnez au moins 1 poste** (ex: Enseignant et Censeur)

4. Marquez **Enseignant** comme **Principal**

5. **Compte de connexion**: Choisissez "Créer un compte"
   - Nom complet: `Jean-Paul KAMGA Enseignant`
   - Email: `jp.kamga@test.cm`
   - Mot de passe: `SecurePass123!`
   - Rôle: `Teacher`

6. Cliquez **Créer**

### Résultats attendus:
✅ Redirection vers la page de détail du personnel
✅ Message: "Dossier de Jean-Paul KAMGA créé avec succès."
✅ Le staff est listé dans Personnel → Index
✅ Les postes s'affichent dans la fiche (Enseignant + Censeur)
✅ Le poste principal est marqué "Enseignant"
✅ Un nouvel utilisateur existe (visible dans Comptes utilisateurs)

### Si erreur:
❌ Message d'erreur clair expliquant le problème
❌ Formulaire recharge avec les données saisies (old())

---

## ✅ Test 2: Éditer un Staff

### Flux:
1. Allez à Personnel → Index
2. Cliquez sur le staff créé (Jean-Paul KAMGA)
3. Cliquez le bouton **Éditer**
4. Changez:
   - Prénom: `Jean-Marc` (au lieu de Jean-Paul)
   - Ajouter le poste "Surveillant Général"

5. Cliquez **Mettre à jour**

### Résultats attendus:
✅ Redirection vers la fiche du personnel
✅ Le prénom s'est changé en "Jean-Marc"
✅ Le nouveau poste "Surveillant Général" s'affiche
✅ Le poste principal reste "Enseignant"
✅ Message: "Dossier de Jean-Marc KAMGA mis à jour."

---

## ✅ Test 3: Supprimer un Staff

### Flux:
1. Personnel → Fiche d'un personnel (sans assignations de cours)
2. Cliquez le bouton **Supprimer**
3. Confirmez si demandé

### Résultats attendus:
✅ Redirection vers la liste Personnel
✅ Message: "Dossier de [Nom] supprimé."
✅ Le personnel ne figure plus dans la liste
✅ Le compte utilisateur associé subsiste (pour historique audit)

### Si erreur:
❌ Si le personnel a des cours assignés cette année:
   Message: "Impossible de supprimer [Nom] : il/elle a des cours assignés."

---

## ✅ Test 4: Créer un Élève + Inscription

### Flux:
1. Allez à: Élèves → Nouvelle inscription
2. **Étape 1 - Identité**:
   - Prénom: `Marie`
   - Nom: `NKOMO`
   - Genre: `Féminin`
   - Date de naissance: `10/05/2010`
   - Lieu de naissance: `Yaoundé`
   - Nationalité: `Camerounaise`
   - Adresse: `123 Rue de la Paix, Yaoundé`
   - Cliquez **Suivant**

3. **Étape 2 - Scolarité**:
   - Année académique: `2025-2026` (doit être pré-sélectionné si active)
   - Classe: `6ème A` (par exemple)
   - Date d'inscription: `Aujourd'hui`
   - Répétition: Non coché
   - Cliquez **Suivant**

4. **Étape 3 - Parents/Tuteurs**:
   - Père: `Jean NKOMO` / `237 666 555 444`
   - Mère: `Antoinette NKOMO` / `237 777 888 999`
   - Cliquez **Suivant**

5. **Étape 4 - Confirmation**:
   - Vérifiez les données
   - Cliquez **Enregistrer**

### Résultats attendus:
✅ Redirection vers la fiche de l'élève
✅ Message: "Marie NKOMO ajouté(e) et inscrit(e) avec succès. Matricule : CP2025001"
✅ L'élève s'affiche dans Élèves → Index
✅ La fiche montre l'inscription actuelle (Classe, Année, Statut: Actif)
✅ L'inscription apparaît dans la BD (table `student_enrollments`)

### Erreurs possibles attendues:
- ❌ **Classe pleine**: "Classe pleine : la classe a atteint le nombre maximum d'élèves"
- ❌ **Inscription dupliquée**: "Inscription dupliquée : cet élève est déjà inscrit à cette année académique"
- ❌ **Validation failed**: Message de validation (ex: email déjà utilisé)

---

## ✅ Test 5: Essayer Doubler une Inscription

### Flux:
1. Créez un élève (Marie NKOMO) dans classe 6ème A
2. Essayez de créer le même élève ou une inscription dupliquée
3. À l'étape Scolarité, sélectionnez la même classe
4. Cliquez **Enregistrer**

### Résultats attendus:
❌ L'inscription NE se crée pas
❌ Message d'erreur: "Inscription dupliquée : cet élève est déjà inscrit à cette année académique"
❌ L'élève n'est pas dupliqué en BD
❌ Le formulaire recharge avec les données (old())

---

## ✅ Test 6: Créer un Élève dans une Classe Pleine

### Flux:
1. Créez une classe "Test Full" avec capacité = 2 élèves
2. Inscrivez 2 élèves dans cette classe
3. Essayez d'en inscrire un 3ème

### Résultats attendus:
❌ L'élève NE se crée pas
❌ Message d'erreur: "Classe pleine : la classe a atteint le nombre maximum d'élèves"
❌ Le formulaire recharge

---

## ✅ Test 7: Éditer un Élève

### Flux:
1. Élèves → Index
2. Cliquez sur un élève (ex: Marie NKOMO)
3. Cliquez **Éditer**
4. Changez le prénom: `Marie-Claire`
5. Cliquez **Mettre à jour**

### Résultats attendus:
✅ Redirection vers la fiche
✅ Le prénom a changé
✅ Message: "Fiche de Marie-Claire NKOMO mise à jour."

---

## ✅ Test 8: Supprimer un Élève

### Flux:
1. Élèves → Fiche d'un élève (sans inscription cette année)
2. Cliquez **Supprimer**
3. Confirmez

### Résultats attendus:
✅ Redirection vers Élèves → Index
✅ Message: "Élève [Nom] supprimé(e)."
✅ L'élève ne figure plus dans la liste

### Si erreur:
❌ Si l'élève a des inscriptions:
   Message: "Impossible de supprimer [Nom] : il/elle a des inscriptions."

---

## ✅ Test 9: Renouveler une Inscription (enroll)

### Flux:
1. Élèves → Fiche d'un élève de l'année précédente
2. Cliquez **Renouveler l'inscription**
3. Sélectionnez:
   - Même classe (répétition) OU classe supérieure (promotion)
4. Cliquez **Enregistrer**

### Résultats attendus:
✅ Une nouvelle `StudentEnrollment` est créée pour l'année active
✅ Le statut est "Actif"
✅ L'année précédente reste en historique
✅ Message: "Inscription renouvellée avec succès."

---

## 📊 Résumé des Validations

| Test | Fonctionnalité | Avant | Après |
|------|---|---|---|
| 1 | Créer staff + user + postes | ❌ Postes non créés | ✅ Tous créés |
| 2 | Éditer staff | ✅ Fonctionnait | ✅ Toujours OK |
| 3 | Supprimer staff | ❌ Pas disponible | ✅ Fonctionne |
| 4 | Créer élève + inscription | ❌ Silencieux | ✅ Messages clairs |
| 5 | Duplicat inscripti | ❌ Silencieux | ✅ Message d'erreur |
| 6 | Classe pleine | ❌ Silencieux | ✅ Message d'erreur |
| 7 | Éditer élève | ✅ Fonctionnait | ✅ Toujours OK |
| 8 | Supprimer élève | ❌ Pas disponible | ✅ Fonctionne |
| 9 | Renouveler inscr | ✅ Existait | ✅ Toujours OK |

---

## 🔍 Debugging Avancé

Si quelque chose ne fonctionne pas:

1. **Vérifiez les logs**: `storage/logs/laravel.log`
2. **Vérifiez la BD**:
   - `staff` → staff_id créé?
   - `users` → user_id créé?
   - `staff_positions` → positions créées?
   - `students` → student créé?
   - `student_enrollments` → enrollment créé?
3. **Inspectez le formulaire**: Assurez-vous que tous les champs requis sont remplis
4. **Vérifiez les permissions**: L'utilisateur a-t-il `manage-staff` et `manage-students`?

---

## 🚀 Prochaines Étapes

Une fois que tous ces tests passent ✅:
- Passer à l'étape **4.10 (Finances)**
- Revoir les étapes 4.1, 4.3, 4.4 pour les architectures
