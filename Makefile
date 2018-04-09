help:
	@ echo "Usage: make [target]"
	@ echo "Targets:"
	@ echo "  analyze Run the PHPStan static analysis tool on the codebase."
	@ echo "  build   Builds the project for deployment."
	@ echo "  clean   Clean all artifacts managed by make."
	@ echo "  lint    Run the PHP CodeSniffer tool on the codebase and attempts"
	@ echo "          to fix any errors found with the PHP Code Beautifier and"
	@ echo "          Fixer tool."
	@ echo "  test    Run PHPUnit tests."

analyze: vendor
	php ./vendor/bin/phpstan analyse -l 7 src
	php ./vendor/bin/phpstan analyze -l 7 tests

build: vendor

clean:
	rm -rf ./vendor
	rm -f ./composer.lock

lint: vendor
	php ./vendor/bin/phpcbf | true
	php ./vendor/bin/phpcs

test: vendor
	php ./vendor/bin/phpunit tests

vendor: composer.json $(wildcard composer.lock)
	composer validate
	composer install --no-suggest
