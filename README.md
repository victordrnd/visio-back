[![forthebadge](https://forthebadge.com/images/badges/built-by-developers.svg)](https://forthebadge.com)

# Visio Backend

L'objectif de ce projet est de réaliser un site internet communiquant avec un backend développé sans framework. Visio permet d'envoyer des messages texte, ainsi que de lancer des appels vidéo et audio. La fonction partage d'écran permet de faire visualiser à son correspondant l'écran souhaité.

## Choix architecturaux

Le backend de visio est inspiré des interfaces de programmation de Laravel. Voici quelques détails sur les différents concepts du projet.

- **Router** : Point d'entrée de l'application. Fait le lien entre un URL et une méthode d'un controlleur.
- **Request** : Objet contenant les paramètres envoyés lors de la requête HTTP. Les classes filles de Request peuvent servir à valider la présence de certains paramètres. Une absence dans les conditions définies dans la requête entrainera une erreur qui sera retournée au format JSON décrivant précisément le manquemant.
- **Middleware** : Module générique permettant de valider une condition ou d'exécuter n'importe quel code avant l'exécution de la méthode du Controlleur.
- **Ressource** : Objet permettant de définir un format de sérialisation spécifique pour un Model.
- **Model** : Entité permettant la liaison entre la BDD et le code, ici en Active Record.
- **QueryBuilder** : Ensemble de fonction permettant la manipulation des données à partir d'un Model et de construire des reqêtes SQL complexes sans avoir à les écrire manuellement.
- **RelationShip** : Entité permettant de relier deux modèles entre eux en indiquant la façon de les lier (HasOne, HasMany, BelongsTo)
- **Helpers** : Raccourci fonctionnel permettant de récupérer une instance d'un objet créé par le Kernel du Backend (request(), auth(), response(),...)
- **Collection** : Regroupement de Model sous forme d'Iterable
- **Resolver** : Classe permettant l'instantiation des dépendances d'une méthode ou d'une classe, nécessaire à l'injection de dépendances.


## Compatibilité 

```bash

PHP : ">7.4.3"
MariaDB : ">10"

```

**Démo :** [https://visio.victordurand.fr](https://visio.victordurand.fr)


Ce backend n'est dépendant d'aucun package composer. 

## Installation

```bash

# Clonez le dépot Github 
$ git clone https://github.com/victordrnd/visio-back.git

# Aller vers le répertoire
$ cd visio-back

# Aller vers le répertoire
$ cp config.example.ini config.ini

# Modifier la configuration SQL
$ nano config.ini

# Mettre les permissions sur le fichier config.ini
$ chmod 755 config.ini

$ mysql -u USER -pPASSWORD visio < database.sql


```


## VirtualHost

### NGINX

```

   # This server accepts all traffic to port 80 and passes it to the upstream. 
   # Notice that the upstream name and the proxy_pass need to match.

   server {
        listen 9000;

        #listen 44302 ssl http2;
        #listen [::]:44302 ssl http2;
        server_name api-visio.victordurand.fr www.api-visio.victordurand.fr;
        #ssl on;
        #ssl_certificate /etc/letsencrypt/live/test.victordurand.fr/fullchain.pem; # managed by Certbot
        #ssl_certificate_key /etc/letsencrypt/live/test.victordurand.fr/privkey.pem; # managed by Certbot
        root /var/www/html/visio-back;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-XSS-Protection "1; mode=block";
        add_header X-Content-Type-Options "nosniff";

        index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /public/index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /public/index.php;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9999;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

}
```

## Reverse Proxy

### Traefik 
```
# http routing section
http:
  routers:
    https-visio-router: 
      rule: "Host(`api-visio.victordurand.fr`)"
      service: api-visio
      middlewares:
      - custom-header
      - api-prefix
      - cors-header
      tls:
        certResolver : lets-encrypt

  middlewares:
    api-prefix:
     addPrefix:
         prefix: "/public"
    custom-header:
      headers:
       customResponseHeaders:
         Server: Visio
         x-powered-by: ''
    cors-header:
      headers:
        customResponseHeaders:
         Access-Control-Allow-Origin : "https://visio.victordurand.fr"
  services:
    api-visio:
      loadBalancer:
        servers:
        - url: http://IP:PORT
```
