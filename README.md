# Bibliothèque - Projet Symfony + Nuxt

Une application de gestion de bibliothèque avec un backend Symfony (API Platform) et un frontend Nuxt.js.

## Architecture

- **Backend** : Symfony 7 + API Platform + Doctrine ORM + JWT Authentication
- **Frontend** : Nuxt 3 + TypeScript + Tailwind CSS + DaisyUI

## Prérequis

- PHP 8.2+
- Composer
- Node.js 18+
- npm ou yarn
- Docker et Docker Compose (pour la base de données)

## Installation et lancement

### Backend (Symfony)

1. **Naviguer vers le dossier backend**
```bash
cd bibliotheque-backend
```

2. **Démarrer la base de données avec Docker**
```bash
# Démarrer uniquement la base de données
docker-compose up -d
```

3. **Installer les dépendances PHP**
```bash
composer install
```

4. **Générer les clés JWT**
```bash
# Générer les clés JWT pour l'authentification
php bin/console lexik:jwt:generate-keypair
```

5. **Configurer la base de données**
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

6. **Insérer les données de test**
```bash
# Insérer des données de test (utilisateurs, catégories, auteurs, livres)
php bin/console app:seed-test-data

# Ou avec purge des données existantes
php bin/console app:seed-test-data --purge
```

7. **Lancer le serveur de développement**
```bash
symfony server:start
# ou
php -S localhost:8000 -t public
```

Le backend sera accessible sur `http://localhost:8000`

### Frontend (Nuxt)

1. **Naviguer vers le dossier frontend**
```bash
cd bibliotheque-frontend
```

2. **Installer les dépendances Node.js**
```bash
npm install
# ou
yarn install
```

3. **Lancer le serveur de développement**
```bash
npm run dev
# ou
yarn dev
```

Le frontend sera accessible sur `http://localhost:3000`

## 👥 Comptes de test

Après avoir exécuté la commande de seed, vous pouvez vous connecter avec :

- **Administrateur** : `admin@example.com` / `adminpass`
- **Utilisateur** : `user@example.com` / `userpass`
