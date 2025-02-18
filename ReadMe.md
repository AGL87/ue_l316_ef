# Configuration projet

Par Aymeric LEGER ACHARD - LP3 MI AW

---

J'ai utilisé Docker pour embarquer directement
la base de données SQLite par défaut de Symfony.

Vous pouvez modifier le .env pour changer l'url de la 
base de donneés si vous souhaitez passer par un autre moteur.

Exécuter les commandes suivante pour lancer le projet :

````

php bin/console d:m:diff
php bin/console d:m:m
symfony server:start

````

Ces commands permettent de générer la base de données et lancer le serveur
web. (ce sont les commandes raccourcies)

Si la base de données doit être créée, n'oubliez pas la commande :


````
php bin/console doctrine:database:create
````