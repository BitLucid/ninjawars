<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/PutEvent.php';

use function NinjaWars\core\events\generateEventbridgeClient as generateEventbridgeClient;
use function NinjaWars\core\events\sendCommandNWEmailRequest as sendCommandNWEmailRequest;
use function NinjaWars\core\events\validateEmailIncomingConfig as validateEmailIncomingConfig;

class PutEventTest extends NWTest
{
    public function testGenerateEventbridgeClient()
    {
        $client = generateEventbridgeClient([
            'region' => 'us-east-1',
            'version' => '2015-10-07'
        ]);
        $this->assertInstanceOf('Aws\EventBridge\EventBridgeClient', $client);
    }

    public function testValidateEmailIncomingConfigPass()
    {
        // These emails are technically valid, but we're just going to validate input here
        $dirty_email = 'ninjawarstchalvak+invalid@gmail.com';
        $config = [
            'from' => 'ninjawarstchalvak+invalidfrom@gmail.com',
            'subject' => 'Test event fired via local php sdk ' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
            'to' => $dirty_email,
        ];
        $errorOrNone = validateEmailIncomingConfig($config);
        $this->assertNull($errorOrNone);
    }

    public function testValidateEmailIncomingConfigFail()
    {
        // These emails are technically valid, but we're just going to validate input here
        $config = [
            //'from' => 'ninjawarstchalvak+invalidfrom@gmail.com',
            'subject' => 'Test event fired via local php sdk ' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
            //'to' => $dirty_email,
        ];
        $errorOrNone = validateEmailIncomingConfig($config);
        $this->assertIsString($errorOrNone);
    }

    public function testEmailSendShouldFailWhenClientTransportMocked()
    {
        $eventBridgeClient = new class () {
            public function putEvents($params)
            {
                return false;
            }
        };
        $dirty_email = 'ninjawarstchalvak+invalid@gmail.com';
        $config = [
            'from' => 'ninjawarstchalvak+invalidfrom@gmail.com',
            'subject' => 'Test event fired via PutEventTest test file' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
        ];
        $result = sendCommandNWEmailRequest($eventBridgeClient, $dirty_email, $config);
        $this->assertFalse($result);
    }

    public function testEmailSendShouldSucceedWhenClientTransportMocked()
    {
        $eventBridgeClient = new class () {
            public function putEvents($params)
            {
                return true;
            }
        };
        $dirty_email = 'ninjawarstchalvak+invalid@gmail.com';
        $config = [
            'from' => 'ninjawarstchalvak+invalidfrom@gmail.com',
            'subject' => 'Test event fired via PutEventTest test file' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
        ];
        $result = sendCommandNWEmailRequest($eventBridgeClient, $dirty_email, $config);
        $this->assertTrue($result);
    }

    public function testEmailSendShouldValidate()
    {
        $eventBridgeClient = new class () {
            public function putEvents($params)
            {
                return true;
            }
        };
        $dirty_email = 'ninjawarstchalvak+invalid@gmail.com';
        $config = [
            //'from' => 'ninjawarstchalvak+invalidfrom@gmail.com',
            'subject' => 'Test event fired via PutEventTest test file' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
        ];
        $result = sendCommandNWEmailRequest($eventBridgeClient, $dirty_email, $config);
        $this->assertFalse($result);
    }
}
