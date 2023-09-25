<?php

require_once 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\EventBridge\EventBridgeClient;
use Aws\Exception\AwsException;
use Aws\Result;

/**
 * Send a raw event to the eventbridge
 */
function putEvent($eventBridgeClient, $event): bool|object
{
    try {
        $result = $eventBridgeClient->putEvents([
            'Entries' => [$event]
        ]);
        return $result;
    } catch (AwsException $e) {

        // output error message if fails
        error_log($e->getMessage());
        return false;
    }
}

/**
 * Create a EventBridgeClient with a default config
 */
function generateEventbridgeClient($config = [
    'region' => 'us-east-1',
    'version' => '2015-10-07'
]): EventBridgeClient
{
    return new EventBridgeClient($config);
}

/**
 * @return bool Whether the event was sent successfully
 */
function sendCommandNWEmailRequest($eventBridgeClient, string $email, array $emailParams): bool|object
{
    if (count($emailParams) < 4) {
        throw new \InvalidArgumentException('Email params must be set.');
    }
    $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $event = [ // REQUIRED
        'Detail' => json_encode(['emailParams' => (['to' => $sanitized_email] + $emailParams)]),
        'DetailType' => 'CommandNWEmailRequest',
        'EventBusName' => 'NWEventBus',
        'Source' => 'php.nwmail.sdk.call',
        'Time' => time(),
        "Version" => "0",

    ];
    return putEvent($eventBridgeClient, $event);
}

/**
 * Eventbridge interaction for email sending
 *
 * This code expects that you have AWS credentials set up per:
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
 */


