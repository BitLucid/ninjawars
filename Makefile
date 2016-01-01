.PHONY: test test-integration test-unit clean dep

COMPOSER=./composer.phar
CC_DIR=./cc
CC_FLAG=--coverage-html $(CC_DIR)
TEST_RUNNER=php -d zend_extension=xdebug.so ./vendor/bin/phpunit

-include CONFIG

ifdef NOCOVER
CC_FLAG=
endif

test:
	@$(TEST_RUNNER) $(CC_FLAG)

test-unit:
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Unit

test-integration:
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Integration

clean:
	@rm -rf ./deploy/templates/compiled/*
	@rm -rf ./vendor/*
	@rm -rf ./cc/

dep:
	@$(COMPOSER) install
