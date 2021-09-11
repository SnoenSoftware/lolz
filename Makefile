.DEFAULT_GOAL := localdev

.PHONY: prod push down run localdev

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

vendor:
	docker run --rm -it -v $$(pwd):$$(pwd) -w $$(pwd) composer/composer composer install

node_modules:
	docker run --rm -it -v $$(pwd):$$(pwd) -w $$(pwd) node yarn


localdev:
	bash -c "trap 'trap - SIGINT SIGTERM ERR; docker-compose down; exit 1' SIGINT SIGTERM ERR; $(MAKE) localdev-internal"

localdev-internal: vendor node_modules
	docker-compose up -d cron
	npx yarn local-dev