# Plan d'implémentation : Tableau de Bord Financier "Gestion Globale"

Ce plan détaille la création de la page de tableau de bord financier **Gestion Globale**, fusionnant les deux maquettes fournies dans une interface premium et fonctionnelle.

## Modifications Proposées

### 1. Routes (`routes/web.php`)
Ajouter une nouvelle route sous le préfixe `finances` :
* **Route :** `GET /finances/global`
* **Nom :** `finances.global`
* **Contrôleur :** `FinanceController@global`
* **Protection :** Accès limité aux rôles `super-admin`, `directeur`, `fondateur`.

### 2. Contrôleur (`app/Http/Controllers/FinanceController.php`)
Créer la méthode `global(Request $request)` pour extraire et calculer dynamiquement toutes les données requises pour le tableau de bord :
* Sélection de l'année scolaire active ou choisie.
* Calcul des KPIs globaux :
  * **Attendu (prévisions) :** Somme des montants requis par classe multiplié par les effectifs.
  * **Collecté :** Somme totale des paiements de l'année.
  * **Reste à collecter :** Différence entre l'attendu et le collecté.
  * **Taux de recouvrement :** Pourcentage global de recouvrement.
  * **Élèves à jour :** Pourcentage d'élèves n'ayant aucun reste à payer.
  * **Paiements du jour :** Quantité et somme collectée aujourd'hui.
* Statistiques par Section (Général, Technique, Anglophone) : total attendu, collecté, restant et taux de recouvrement.
* Statistiques par Tranche de paiement (Tranche 1, Tranche 2, Tranche 3) : pourcentage de couverture par rapport à l'effectif total.
* Liste des paiements mensuels pour alimenter un graphique d'évolution sur 10 mois (Septembre à Juin).
* Top 5 des paiements récents.
* Liste détaillée des élèves débiteurs (impayés) avec simulation de la dernière action de relance.

### 3. Vues
#### A. Sidebar (`resources/views/layouts/partials/sidebar.blade.php`)
Insérer l'élément "Gestion Globale" sous le bloc "FINANCES" :
* **Restreint à :** `super-admin`, `directeur` ou `fondateur`.
* **Icône :** `chart-bar`.
* **Lien :** `route('finances.global')`.

#### B. Nouvelle page (`resources/views/finances/global.blade.php`)
Créer une interface premium utilisant Tailwind CSS :
* **En-tête :** Titre, sous-titre, sélecteur d'année, et boutons d'action rapide.
* **KPIs :** 4 grandes cartes avec icônes de statut et bordures colorées HSL.
* **Grille principale (2 colonnes) :**
  * **Gauche :** Graphique d'évolution mensuelle (barres CSS stylisées) + Statistiques de collecte et d'impayés par Section.
  * **Droite :** Avancement par Tranche (barres de progression) + Historique des derniers paiements + Liens d'actions rapides.
* **Tableau inférieur :** Liste des élèves débiteurs avec actions de relance interactives (SMS, Email, Alarme).

## Plan de Vérification
* Accéder à la page avec un compte Super Admin / Directeur → La page doit s'afficher.
* Accéder à la page avec un compte Enseignant / Surveillant → Doit retourner un code d'accès refusé (`403 Forbidden`).
* Vérifier la cohérence des totaux calculés (Frais attendus = Collectés + Restants).
* Vérifier le rendu visuel responsive (surtout le graphique mensuel et le tableau des débiteurs).
