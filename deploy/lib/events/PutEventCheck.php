<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/PutEvent.php';

use function NinjaWars\core\events\generateEventbridgeClient as generateEventbridgeClient;
use function NinjaWars\core\events\sendCommandNWEmailRequest as sendCommandNWEmailRequest;
use function NinjaWars\core\events\validateEmailIncomingConfig as validateEmailIncomingConfig;

throw new Exception('This file is not meant to be run except during prototyping.');
// Checkrun: php deploy/lib/events/PutEventCheck.php
// TODO: remove this prototype check once event email sdk sending is stabilized

// Create a EventBridgeClient
$eventBridgeClient = generateEventbridgeClient([
    'region' => 'us-east-1',
    'version' => '2015-10-07'
]);

$config = [
    'from' => 'tchalvak@ninjawars.net',
    'subject' => 'Test event fired via local php run file ' . hash('SHA512', time()),
    'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
    'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
    'replyto' => 'ninjawarstchalvak@gmail.com',
];

$dirty_email = 'roy.ronalds@gmail.com';

$result = sendCommandNWEmailRequest($eventBridgeClient, $dirty_email, $config);
if ($result === false) {
    echo "Failed to send event to eventbridge.";
} else {
    echo "Event sent to eventbridge.";
    var_dump($result);
}
