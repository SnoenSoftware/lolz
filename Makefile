.DEFAULT_GOAL := run

.PHONY: prod push down run

registry := ghcr.io/bjornsnoen/brbcoffee

prod:
	docker build . -f dockerfiles/Dockerfile --target=fpm -t $(registry)/lolz-fpm:latest
	docker build . -f dockerfiles/Dockerfile --target=cron -t $(registry)/lolz-cron:latest
	docker build . -f dockerfiles/Dockerfile --target=proxy -t $(registry)/lolz-proxy:latest

push: prod
	docker push $(registry)/lolz-fpm:latest
	docker push $(registry)/lolz-cron:latest
	docker push $(registry)/lolz-proxy:latest

run: prod
	docker-compose up -d

down:
	docker-compose down

clean: down
	rm -rf data/db.sqlite node_modules vendor