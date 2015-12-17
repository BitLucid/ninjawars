.PHONY: test test-integration test-unit

test:
	@./vendor/bin/phpunit

test-unit:
	@./vendor/bin/phpunit --testsuite Unit

test-integration:
	@./vendor/bin/phpunit --testsuite Integration
