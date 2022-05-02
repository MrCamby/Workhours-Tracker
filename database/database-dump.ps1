docker exec workhours-tracker_database_1 sh -c 'exec mysqldump --no-data workhours -uroot -p"$MARIADB_ROOT_PASSWORD"' > ./initial.sql
docker exec workhours-tracker_database_1 sh -c 'exec mysqldump --no-create-info --skip-triggers --no-create-db --compact workhours -uroot -p"$MARIADB_ROOT_PASSWORD"' > ./data.sql