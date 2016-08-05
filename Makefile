composer.lock: composer.json
	composer update

.PHONY: test
test:
	phpunit
