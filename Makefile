.DEFAULT_GOAL := run

.PHONY: prod push down run

registry = registry.digitalocean.com

prod:
	docker build . -f dockerfiles/Dockerfile --target=fpm -t $(registry)/brbcoffee/site:lolz-fpm
	docker build . -f dockerfiles/Dockerfile --target=cron -t $(registry)/brbcoffee/site:lolz-cron
	docker build . -f dockerfiles/Dockerfile --target=proxy -t $(registry)/brbcoffee/site:lolz-proxy

push: prod
	docker push $(registry)/brbcoffee/site:lolz-fpm
	docker push $(registry)/brbcoffee/site:lolz-cron
	docker push $(registry)/brbcoffee/site:lolz-proxy

run: prod
	docker-compose up -d

down:
	docker-compose down

clean: down
	rm -rf data/db.sqlite node_modules vendor