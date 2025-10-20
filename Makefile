SHELL := /bin/bash

tests:
	docker compose exec php bin/console doctrine:schema:drop --force --full-database --env=test
	docker compose exec php bin/console doctrine:migrations:migrate -n --env=test
	docker compose exec php bin/console doctrine:fixtures:load -n --env=test
	docker compose exec php bin/phpunit $(MAKECMDGOALS)
.PHONY: tests
