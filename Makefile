.DEFAULT_GOAL := run

.PHONY: prod push down run

registry := ghcr.io/bjornsnoen/lolz

prod:
	docker build . -f dockerfiles/Dockerfile --target=fpm -t $(registry)/fpm:latest
	docker build . -f dockerfiles/Dockerfile --target=cron -t $(registry)/cron:latest
	docker build . -f dockerfiles/Dockerfile --target=proxy -t $(registry)/proxy:latest

push: prod
	docker push $(registry)/fpm:latest
	docker push $(registry)/cron:latest
	docker push $(registry)/proxy:latest

run: prod
	docker-compose up -d

down:
	docker-compose down

clean: down
	rm -rf data/db.sqlite node_modules vendor