Voir la version du composer et celui du php
PHP ≥ 8.1
MySQL

bash ::: 
    php -v
    composer -v
    si pas encore installer d abord composer 

Puis Creation du projet Laravel
    composer create-project laravel/laravel coworking-app

Pour un nouvelle Pc qui a deja le projet il suffit juste d'installer composer 
    cd coworking-app
    composer install

modification de toute la base dans :::::::  .env


Installe le package Laravel pour reCAPTCHA pour connaissances robot :
    composer require anhskohbo/no-captcha
    composer require biscolab/laravel-recaptcha
Installe le package Laravel pour faire une export pdf 
    composer require barryvdh/laravel-dompdf

php artisan migrate
php artisan db:seed





**********
Activation de mysql
Ouvrez une invite de commande et exécutez la commande suivante : mysqld --skip-grant-tables
Ouvrez une nouvelle invite de commande et connectez-vous  :   mysql -u root -p


**********
Pas encore de seeders alors il faut ceci 

INSERT INTO Profils (profil) VALUES
('Administrateur'),
('Manager'),
('Employé'),
('Client'),
('Visiteur');
************************************************
Vider la cache 
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
    php artisan event:clear
    php artisan optimize:clear


Mettre a jour le composer 
    composer dump-autoload


*************

UTILISATION DOCKER 

[ Serveur Debian 7 ]
├── Base MySQL existante (externe)
├── Apache/PHP 5.4 (vieux système)
└── 🐳 Docker
     └── [Conteneur Laravel]
          ├── PHP 8.2
          ├── MySQL (séparé)
          └── Apache ou Nginx

    sudo apt-get update
    sudo apt-get install apt-transport-https ca-certificates curl gnupg lsb-release


apt-transport-https : Permet à APT (le gestionnaire de paquets) d'accéder aux dépôts via HTTPS pour des téléchargements sécurisés. Depuis les versions récentes d'APT, cela est généralement intégré par défaut.​

ca-certificates : Installe les certificats d'autorité nécessaires pour vérifier l’authenticité des connexions HTTPS (utile pour sécuriser la communication réseau).​

curl : Utilitaire en ligne de commande pour transférer des données avec des URL (très utilisé pour télécharger des fichiers, scripts ou interagir avec des APIs).​

gnupg : Fournit les outils de gestion de clés GPG, souvent nécessaires pour vérifier et signer des paquets ou dépôts lors de l'installation de logiciels tiers.​

lsb-release : Affiche les informations sur la distribution Linux utilisée (utile pour les scripts d’installation qui adaptent leurs instructions selon la version du système).


    curl -fsSL https://download.docker.com/linux/debian/gpg








Installation cloudflared
    winget install --id Cloudflare.cloudflared

Faire marcher en prod  
    cloudflared tunnel --url http://127.0.0.1:8000





a mettre dans 
    start.bat::
        Vider tout les caches 
            php artisan config:clear
            php artisan cache:clear
            php artisan route:clear
            php artisan view:clear
            php artisan event:clear
            php artisan optimize:clear

        start mysql dans xampp
            mysqld --skip-grant-tables
        Demarrer Laravel
            php artisan serve 
        Demarrer le production cloudflared
            cloudflared tunnel --url http://127.0.0.1:8000