<?php

/**
 * A prototyping file for checking SES integration,
 * @todo remove this prototype check once event email sdk sending is stabilized
 * Example run: php deploy/lib/events/PutEventCheck.php
 */

require_once(dirname(__DIR__ . '..') . '/base.inc.php');

use function NinjaWars\core\events\generateEventbridgeClient;
use function NinjaWars\core\events\sendCommandNWEmailRequest;

// throw new Exception('This file is not meant to be run except during prototyping.');

// Create a EventBridgeClient
$eventBridgeClient = generateEventbridgeClient();

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
