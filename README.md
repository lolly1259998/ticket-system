# Système de Gestion de Tickets

Une application web complète de gestion de tickets d'assistance technique (Helpdesk), développée en PHP natif suivant le modèle architectural MVC (Modèle-Vue-Contrôleur).

## Fonctionnalités

### Gestion des Tickets

- **Création** : Formulaire complet avec gestion de priorités, trackers, et pièces jointes.
- **Suivi** : Workflow d'états (Nouveau → En cours → Résolu → Fermé).
- **Organisation** : Pagination, filtres par statut et par date.
- **Historique** : Traçabilité complète des actions sur chaque ticket.

### Collaboration

- **Commentaires** : Fil de discussion intégré au ticket.
- **Solutions** : Proposition de solutions techniques avec système d'acceptation ou de refus par le demandeur.
- **Satisfaction** : Enquête de satisfaction client une fois le ticket résolu.

### Espace Utilisateur

- **Authentification** : Connexion sécurisée.
- **Profil** : Gestion de l'avatar et des informations personnelles.
- **Dashboard** : Vue d'ensemble avec statistiques clés (KPIs) et cartes récapitulatives.

## 🛠 Technologies Utilisées

- **Langage** : PHP 8+
- **Base de Données** : MySQL (Interface PDO)
- **Frontend** : HTML5, CSS3 (Design responsive, Variables CSS), JavaScript
- **Architecture** : MVC (Model-View-Controller)
- **Environnement** : XAMPP (Apache/MySQL)

## 📂 Structure du Projet

```
ticket-system/
├── assets/          # CSS, JS, Images, Uploads
├── config/          # Configuration DB (database.php)
├── controllers/     # Logique métier (TicketController, AuthController...)
├── models/          # Accès données (Ticket, User, Comment...)
├── views/           # Interfaces utilisateurs (Templates PHP)
└── index.php        # Point d'entrée (Routeur)
```

## ⚙️ Installation

1.  **Prérequis** : Installer XAMPP ou WAMP.
2.  **Fichiers** : Placer le dossier `ticket-system` dans `htdocs` (c:\xampp\htdocs\).
3.  **Base de Données** :
    - Créer une base de données nommée `ticket_system` (ou selon votre config).
    - Importer les tables (`users`, `tickets`, `comments`, `solutions`, `ticket_history`).
4.  **Configuration** :
    - Vérifier les paramètres de connexion dans `config/database.php`.
5.  **Lancement** :
    - Ouvrir le navigateur sur `http://localhost/ticket-system`.

## 📅 Historique du Développement

Projet réalisé sur la période Novembre - Décembre 2025 (Sprints 0 à 6).

- **Sprint 0** : Architecture MVC & Base de données.
- **Sprint 1** : Authentification & Création tickets.
- **Sprint 2** : Listes & Filtres.
- **Sprint 3** : Module Satisfaction & Top Intervenants.
- **Sprint 4** : Commentaires & Solutions.
- **Sprint 5** : Profils, Avatars & Pagination.

---

_Projet académique/stage._
