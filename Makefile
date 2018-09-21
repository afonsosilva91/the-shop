
open:
	docker-compose up -d --build

close:
	docker-compose stop

delete:
	docker-compose kill