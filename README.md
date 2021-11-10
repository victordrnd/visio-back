[![forthebadge](https://forthebadge.com/images/badges/built-by-developers.svg)](https://forthebadge.com)

# Visio Backend

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