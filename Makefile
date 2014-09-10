test: phpunit phpcs bugfree
test-analysis: phpcs bugfree
test-upload: scrutinizer

.PHONY: test test-analysis test-upload pretest phpunit phpcs bugfree ocular scrutinizer clean clean-env clean-deps

pretest:
	composer install --dev
	
phpunit: pretest
	@vendor/bin/phpunit --coverage-text --coverage-clover=coverage.clover

ifndef STRICT
STRICT = 0
endif

ifeq "$(STRICT)" "1"
phpcs: pretest
	@vendor/bin/phpcs --standard=phpcs.xml src
else
phpcs: pretest
	@vendor/bin/phpcs --standard=phpcs.xml -n src
endif

bugfree: pretest
	@vendor/bin/bugfree lint src

ocular:
	@if [ ! -f ocular.phar ]; then wget https://scrutinizer-ci.com/ocular.phar; fi

ifdef OCULAR_TOKEN
scrutinizer: ocular
	@php ocular.phar code-coverage:upload --format=php-clover coverage.clover --access-token=$(OCULAR_TOKEN);
else
scrutinizer: ocular
	@php ocular.phar code-coverage:upload --format=php-clover coverage.clover;
endif

clean: clean-env clean-deps

clean-env:
	@rm -rf coverage.clover
	@rm -rf ocular.phar
	@rm -rf tests/output/
	
clean-deps:
	@rm -rf vendor/
