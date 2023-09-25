<?php

namespace NinjaWars\core\events;

require_once 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\EventBridge\EventBridgeClient;
use Aws\Exception\AwsException;
use Aws\Result;

/**
 * Send a raw event to the eventbridge
 */
function putEvent(object $eventBridgeClient, array $event): bool|object
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
 * Email config validation, which should perhaps eventually be moved away from event lib
 * @return string|null An error string if invalid
 */
function validateEmailIncomingConfig(array $config): ?string
{
    $required_keys = ['from', 'subject', 'text', 'html', 'to'];
    $missing_keys = array_diff($required_keys, array_keys($config));
    if (count($missing_keys) > 0) {
        return 'Missing required keys: ' . implode(', ', $missing_keys);
    }
    return null;
}

/**
 * @return bool Whether the event was sent successfully
 */
function sendCommandNWEmailRequest(?object $eventBridgeClient, string $email, array $emailParams): bool|object
{
    $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $final_config = ($emailParams + ['to' => $sanitized_email]);
    validateEmailIncomingConfig($final_config);
    $event = [ // REQUIRED
        'Detail' => json_encode(['emailParams' => $final_config]),
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


