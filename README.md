Voir la version du composer et celui du php
PHP ≥ 8.1
MySQL

bash ::: 
    php -v
    composer -V

Puis Creation du projet Laravel
    composer create-project laravel/laravel coworking-app


Installe le package Laravel pour reCAPTCHA pour connaissances robot :
    composer require anhskohbo/no-captcha
    composer require biscolab/laravel-recaptcha





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