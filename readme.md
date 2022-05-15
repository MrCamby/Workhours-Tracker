[GitHub - Workhours-Tracker](https://github.com/MrCamby/Workhours-Tracker)<br />
[DockerHub - workhours-tracker](https://hub.docker.com/r/mrcamby/workhours-tracker)
<br /><br />
Default User
> Username: demo<br />
> Password: changeme
<br />

docker-compose.yml
```
version: "3.3"
services:
  database:
    environment:
      - MARIADB_ROOT_PASSWORD=password
#      - MARIADB_DATABASE=workhours
#      - MARIADB_USER=user
#      - MARIADB_PASSWORD=password
    image: mrcamby/workhours-tracker:database
    restart: always
    volumes:
      - ./database-data:/var/lib/mysql

  web:
    build: web/
    depends_on:
      - database
#    environment:
#      - DATABASE_SERVER="database" #default
#      - DATABASE_DATABASE="workhours" #default
#      - DATABASE_USER="user" #default
#      - DATABASE_PASSWORD="password" #default
    image: mrcamby/workhours-tracker:web
    ports:
      - 80:80
    restart: always
```
