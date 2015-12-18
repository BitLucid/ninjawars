.PHONY: test test-integration test-unit

test:
	@./vendor/bin/phpunit --coverage-html ./deploy/tests/cc

test-unit:
	@./vendor/bin/phpunit --coverage-html ./deploy/tests/cc --testsuite Unit

test-integration:
	@./vendor/bin/phpunit --coverage-html ./deploy/tests/cc --testsuite Integration
