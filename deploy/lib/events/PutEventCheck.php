<?php
require_once __DIR__ . '/PutEvent.php';


// Create a EventBridgeClient
$eventBridgeClient = generateEventbridgeClient([
    'region' => 'us-east-1',
    'version' => '2015-10-07'
]);

$config = [
    'from' => 'tchalvak@ninjawars.net',
    'subject' => 'Test event fired via local php sdk 2384',
    'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
    'html' => '<h1>Simple Title for in body</h1><p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
];

$dirty_email = 'roy.ronalds@gmail.com';

$result = sendCommandNWEmailRequest($eventBridgeClient, $dirty_email, $config);
if ($result === false) {
    echo "Failed to send event to eventbridge.";
} else {
    echo "Event sent to eventbridge.";
    var_dump($result);
}
