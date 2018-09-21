
build:
	docker-compose up -d --build

database:
	docker-compose exec api-php php artisan db:create \
	&& docker-compose exec api-php php artisan migrate:refresh --seed

open:
	docker-compose up -d

close:
	docker-compose stop

bankrupt:
	docker-compose down
	
#docker-compose down --rmi 'all'