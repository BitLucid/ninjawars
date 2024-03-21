<?php


use function NinjaWars\core\events\generateEventbridgeClient as generateEventbridgeClient;
use function NinjaWars\core\events\sendCommandNWEmailRequest as sendCommandNWEmailRequest;
use function NinjaWars\core\events\validateEmailIncomingConfig as validateEmailIncomingConfig;
use function NinjaWars\core\events\sanitizeAndFormatEmail as sanitizeAndFormatEmail;
use function NinjaWars\core\events\generateEmailEvent as generateEmailEvent;

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
        $dirty_email = 'ninjawarstchalvak+invalid@example.com';
        $config = [
            'from' => 'ninjawarstchalvak+shouldnotbesentunto@example.com',
            'subject' => 'Test event fired via local php sdk ' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
            'to' => $dirty_email,
        ];
        $errorOrNone = validateEmailIncomingConfig($config);
        $this->assertNull($errorOrNone);
    }

    public function testSanitizeAndFormatEmail()
    {
        $this->assertEquals('"Ninja Wars" <nw@example.com>', sanitizeAndFormatEmail(['nw@example.com' => 'Ninja Wars']));
        $this->assertEquals('nw@example.com', sanitizeAndFormatEmail('nw@example.com'));
        $this->assertEquals('"From Ninja Wars System" <ninjawarstchalvak+shouldnotbesentunto@example.com>', sanitizeAndFormatEmail(['ninjawarstchalvak+shouldnotbesentunto@example.com' => 'From Ninja Wars System']));
    }

    public function testValidateEmailIncomingConfigFail()
    {
        // These emails are technically valid, but we're just going to validate input here
        $config = [
            //'from' => 'ninjawarstchalvak+shouldnotbesentunto@example.com',
            'subject' => 'Test event fired via local php sdk ' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
            //'to' => $dirty_email,
        ];
        error_log('Expect an error on the lack of a from address here: ');
        $errorOrNone = validateEmailIncomingConfig($config);
        $this->assertIsString($errorOrNone);
    }

    public function testGenerateEmailStringEvent()
    {
        $config = [
            'to' => 'someaddress@example.com',
            'from' => 'ninjawarstchalvak+shouldnotbesentunto@example.com',
            'subject' => 'Test event fired via PutEventTest test file' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
        ];
        $event = generateEmailEvent($config);
        $this->assertEquals('php.nwmail.sdk.call', $event['Source']);
        $this->assertEquals('CommandNWEmailRequest', $event['DetailType']);
        $this->assertEquals('ninjawarstchalvak+shouldnotbesentunto@example.com', json_decode($event['Detail'], true)['emailParams']['from']);
    }

    public function testGenerateEmailComplexArrayEvent()
    {
        $config = [
            'to' => ['someaddress@example.com' => 'Some Player'],
            'from' => ['ninjawarstchalvak+shouldnotbesentunto@example.com' => 'From Ninja Wars System'],
            'subject' => 'Test event fired via PutEventTest test file' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
        ];
        $event = generateEmailEvent($config);
        $this->assertEquals('php.nwmail.sdk.call', $event['Source']);
        $this->assertEquals('CommandNWEmailRequest', $event['DetailType']);
        // Assert detail decoded is a string
        $generated_email = json_decode($event['Detail'], true)['emailParams']['from'];
        $this->assertIsString($generated_email);
        $this->assertEquals('"From Ninja Wars System" <ninjawarstchalvak+shouldnotbesentunto@example.com>', $generated_email);
    }

    public function testEmailSendShouldFailWhenClientTransportMocked()
    {
        $eventBridgeClient = new class () {
            public function putEvents($params)
            {
                return false;
            }
        };
        $dirty_email = 'ninjawarstchalvak+invalid@example.com';
        $config = [
            'from' => 'ninjawarstchalvak+shouldnotbesentunto@example.com',
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
        $dirty_email = 'invalid@example.com';
        $config = [
            'from' => 'shouldnotbesentunto@example.com',
            'subject' => 'Test event fired via PutEventTest test file' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
        ];
        $result = sendCommandNWEmailRequest($eventBridgeClient, $dirty_email, $config);
        $this->assertTrue($result);
    }

    public function testEmailSendShouldValidateAndRejectSending()
    {
        $eventBridgeClient = new class () {
            public function putEvents($params)
            {
                return true;
            }
        };
        $dirty_email = 'ninjawarstchalvak+invalid@example.com';
        $config = [
            //'from' => 'ninjawarstchalvak+shouldnotbesentunto@example.com',
            'subject' => 'Test event fired via PutEventTest test file' . hash('SHA512', time()),
            'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
            'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
        ];
        echo PHP_EOL . "Expect an error log during test run here: " . PHP_EOL;
        $result = sendCommandNWEmailRequest($eventBridgeClient, $dirty_email, $config);
        $this->assertFalse($result);
    }
}
