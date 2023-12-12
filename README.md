# MVC FROM SCRATCH
## Configuration
### Base de donnée

Créez un fichier `config/db.ini` avec les informations suivantes (un modèle est disponible dans [`config/db.ini-template`](config/db.ini-template)):

```ini
DB_HOST="127.0.0.1"
DB_PORT=3306
DB_NAME="databasename"
DB_CHARSET="utf8mb4"
DB_USERNAME="username"
DB_PASSWORD="password"
```