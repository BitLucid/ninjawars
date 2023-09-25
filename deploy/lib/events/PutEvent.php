<?php

namespace NinjaWars\core\events;

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

function sanitizeAndFormatEmail(array|string $email_complex): string
{
    list($email, $display) = is_array($email_complex) ? [array_key_first($email_complex), reset($email_complex)] : [$email_complex, null];
    $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (false === $sanitized_email) {
        throw new \Exception('Invalid email address: ' . $email);
    }
    return $display ? '"' . $display . '" <' . $sanitized_email . '>' : $sanitized_email;
}

/**
 * Email config validation, which should perhaps eventually be moved away from event lib
 * @return string|null An error string if invalid
 */
function validateEmailIncomingConfig(array $config): ?string
{
    // replyto is optional
    $required_keys = ['from', 'subject', 'text', 'html', 'to'];
    $missing_keys = array_diff($required_keys, array_keys($config));
    if (count($missing_keys) > 0) {
        return 'Missing required keys: ' . implode(', ', $missing_keys);
    }
    try {
        sanitizeAndFormatEmail($config['from']); // throws if invalid
        $to = sanitizeAndFormatEmail($config['to']); // throws if invalid
    } catch (\Exception $e) {
        return $e->getMessage();
    }
    if (!preg_match(
        "/\.|@/",
        $to
    )) {
        return 'Invalid to email address: ' . $to;
    }

    return null;
}

/**
 * @return bool Whether the event was sent successfully
 */
function sendCommandNWEmailRequest(object $eventBridgeClient, array|string $email, array $emailParams): bool|object
{
    $validation = validateEmailIncomingConfig(($emailParams + ['to' => $email]));
    if (null !== $validation) {
        error_log('Email validation failed: ' . $validation);
        return false;
    }
    $to = sanitizeAndFormatEmail($email);
    $from = sanitizeAndFormatEmail($emailParams['from']);
    $final_config = ($emailParams + ['to' => $to, 'from' => $from]);
    $validation = validateEmailIncomingConfig($final_config);
    if (null !== $validation) {
        error_log('Email validation failed: ' . $validation);
        return false;
    }    

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
