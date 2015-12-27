.PHONY: test test-integration test-unit clean dep

CC_DIR=./cc
CC_FLAG=--coverage-html $(CC_DIR)

-include CONFIG

ifdef NOCOVER
CC_FLAG=
endif

test:
	@./vendor/bin/phpunit $(CC_FLAG)

test-unit:
	@./vendor/bin/phpunit $(CC_FLAG) --testsuite Unit

test-integration:
	@./vendor/bin/phpunit $(CC_FLAG) --testsuite Integration

clean:
	@rm -rf ./deploy/templates/compiled
	@rm -rf ./vendor/*
	@rm -rf ./cc/

dep:
	@./composer.phar install
