<?php

require_once 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\EventBridge\EventBridgeClient;
use Aws\Exception\AwsException;


/**
 * List your Amazon S3 buckets.
 *
 * This code expects that you have AWS credentials set up per:
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
 */

// Create a EventBridgeClient
$eventBridgeClient = new EventBridgeClient([
    'region' => 'us-east-1',
    'version' => '2015-10-07'
]);

$config = [
    'from' => 'tchalvak@ninjawars.net',
    'subject' => 'Test event fired via local php sdk 2384',
    'text' => 'Some Raw text: of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function',
    'html' => '<p>Some html of the email body that is sent in: Test event fired via lambda email sendout nwEmailSendout function</p>',
];

$dirty_email = 'roy.ronalds@gmail.com';


// send an event to eventbridge
try {
    $sanitized_email = filter_var($dirty_email, FILTER_SANITIZE_EMAIL);
    $entries = [ // REQUIRED
        [
            'Detail' => json_encode(['emailParams' => (['to' => $sanitized_email] + $config)]),
            'DetailType' => 'CommandNWEmailRequest',
            'EventBusName' => 'NWEventBus',
            'Source' => 'php.nwmail.sdk.call',
            'Time' => time(),
            "Version" => "0",

        ],
        // ...
    ];
    $result = $eventBridgeClient->putEvents([
        'Entries' => $entries
    ]);
    print_r($result);
    var_dump($result);
} catch (AwsException $e) {
    // output error message if fails
    error_log($e->getMessage());
}

// //Create a S3Client 
// $s3Client = new S3Client([
//     'region' => 'us-east-1',
//     'version' => '2015-10-07'
// ]);

// //Listing all S3 Bucket
// $buckets = $s3Client->listBuckets();
// foreach ($buckets['Buckets'] as $bucket) {
//     echo $bucket['Name'] . "\n";
// }
