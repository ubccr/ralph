help:
	@ echo "Usage: make [target]"
	@ echo "Targets:"
	@ echo "  all               Clean, build, verify, test and sniff the code."
	@ echo "                    This target should succeed prior to opening a"
	@ echo "                    pull request."
	@ echo "  build             Builds the project."
	@ echo "  clean             Clean all artifacts managed by make."
	@ echo "  lint              Run the PHP linter on all .php files."
	@ echo "  phpcs             Run PHP_CodeSniffer."
	@ echo "  test              Run unit tests."

all: clean build lint test phpcs

build:
	composer install -o --no-suggest

clean:
	rm -rf ./vendor
	rm -f ./composer.lock

lint:
	! find . -type f -name "*.php" \
		| grep -v "^./vendor" \
		| xargs -I {} php -l '{}' \
		| grep -v "No syntax errors detected"
	@ echo "No syntax errors detected."

phpcs: build
	./vendor/bin/phpcs

test: build
	./vendor/bin/codecept run unit --fail-fast
