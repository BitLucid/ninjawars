<?php
class SetupTest extends \PHPUnit\Framework\TestCase {
    public function testRequiredConstants() {
        // Main Resources constants
        $this->assertTrue(defined('DATABASE_HOST'));
        $this->assertTrue(defined('DATABASE_USER'));
        $this->assertTrue(defined('DATABASE_NAME'));
        $this->assertTrue(defined('OFFLINE'));
        $this->assertTrue(defined('DEBUG'));
        $this->assertTrue(defined('SERVER_ROOT'));
        $this->assertTrue(defined('WEB_ROOT'));
        $this->assertTrue(defined('ADMIN_EMAIL'));
        $this->assertTrue(defined('SUPPORT_EMAIL'));
        $this->assertTrue(defined('SYSTEM_MESSENGER_EMAIL'));
        $this->assertTrue(defined('SYSTEM_MESSENGER_NAME'));
        $this->assertTrue(defined('ALERTS_EMAIL'));
        $this->assertTrue(defined('TRAP_ERRORS'));
        $this->assertTrue(defined('TEMPLATE_LIBRARY_PATH'));
        $this->assertTrue(defined('COMPILED_TEMPLATE_PATH'));
        $this->assertTrue(defined('LOGS'));
        $this->assertTrue(defined('CONNECTION_STRING'));

        // Derived constants
        $this->assertTrue(defined('VENDOR_ROOT'));
        $this->assertTrue(defined('CONF_ROOT'));
        $this->assertTrue(defined('ROOT'));
        $this->assertTrue(defined('CSS_ROOT'));
        $this->assertTrue(defined('JS_ROOT'));
        $this->assertTrue(defined('IMAGE_ROOT'));
        $this->assertTrue(defined('SERVER_IMAGE_ROOT'));
        $this->assertTrue(defined('LIB_ROOT'));
        $this->assertTrue(defined('DB_ROOT'));
        $this->assertTrue(defined('TEMPLATE_PATH'));
        $this->assertTrue(defined('TEMPLATE_PLUGIN_PATH'));
        $this->assertTrue(defined('LOCAL_JS'));
        $this->assertTrue(defined('MAX_MSG_LENGTH'));
        $this->assertTrue(defined('MAX_CLAN_MSG_LENGTH'));
        $this->assertTrue(defined('UNAME_LOWER_LENGTH'));
        $this->assertTrue(defined('UNAME_UPPER_LENGTH'));
        $this->assertTrue(defined('GRAVATAR'));
    }
}
