.PHONY: test test-integration test-unit

CC_DIR=./cc

ifdef NOCOVER
CC_FLAG=
else
CC_FLAG=--coverage-html $(CC_DIR)
endif

test:
	@./vendor/bin/phpunit $(CC_FLAG)

test-unit:
	@./vendor/bin/phpunit $(CC_FLAG) --testsuite Unit

test-integration:
	@./vendor/bin/phpunit $(CC_FLAG) --testsuite Integration
