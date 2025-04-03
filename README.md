# Mediatekformation

## Présentation

Ce site, développé avec Symfony 6.4, permet d'accéder aux vidéos d'auto-formation proposées par une chaîne de médiathèques et qui sont aussi accessibles sur YouTube.  

---

## Fonctionnalités ajoutées

-  Ajout d’un système d’authentification pour sécuriser l’accès au back-office.
-  Nettoyage complet du code selon les consignes de SonarLint.
-  Gestion des entités Formation, Playlist et Catégorie (CRUD complet).
-  Ajout d’une colonne indiquant le nombre de formations par playlist.
-  Tests unitaires, d’intégration et fonctionnels implémentés.
-  Test de compatibilité sur Chrome, Firefox et Edge.
-  Déploiement continu via GitHub Actions sur PlanetHoster.
-  Script `sauvegarde_bdd.php` pour automatiser les sauvegardes SQL.

---


## Base de données

### Schéma conceptuel
```text
formation (id, published_at, title, video_id, description, playlist_id)
  ↳ playlist_id = clé étrangère vers playlist(id)
playlist (id, name, description)
categorie (id, name)
formation_categorie (id_formation, id_categorie)
  ↳ clés étrangères vers formation et categorie
```
- Clés primaires auto-incrémentées
- Images YouTube générées à partir de `video_id`

---

## Mode opératoire (installation locale)

```bash
git clone https://github.com/Simba-TRZ/mediatekformation.git
composer install
npm install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony serve
```

---

## Déploiement

 [https://aziz-mediatekformation.worldlite.fr](https://aziz-mediatekformation.worldlite.fr)

---

## Tests réalisés

| Type de test         | Statut |
|----------------------|--------|
|  Test unitaire      | OK     |
|  Test d’intégration | OK     |
|  Test fonctionnel   | OK     |
|  Compatibilité navigateur | OK |

---

## Sauvegarde BDD

Le script `sauvegarde_bdd.php` génère un fichier SQL contenant la structure et les données.

---

## Auteur

Projet réalisé par **Aziz-M'HAIMID** – BTS SIO SLAM – CNED
