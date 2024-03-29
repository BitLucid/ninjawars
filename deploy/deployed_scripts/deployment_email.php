<?php

namespace NinjaWars\core\deployment;

require_once(dirname(__DIR__ . '..') . '/lib/base.inc.php');

// nmail is included by the base inc

use Nmail;


function sendDeploymentEmail($subject, $message)
{
    $nmail = new NMail(SUPPORT_EMAIL, $subject, $message, SYSTEM_EMAIL);
    return $nmail->send();
}

$subject = 'Ninjawars: Deployment Complete Notification (at ' . date('Y-m-d H:i:s') . ')';
$message = "
The deployment has been completed successfully, this message has been triggered as the end step after the deployment system completed.
<br /><br />
Please check the site to ensure that everything is working as expected.
https://www.ninjawars.net
<br /><br />
The most recently deployed features are likely here:
<br /><br />
https://github.com/BitLucid/ninjawars/pulls?q=is%3Apr+is%3Aclosed
<br /><br />
If you have any questions or concerns, please contact the development team.
<br /><br />
";

sendDeploymentEmail($subject, $message);

// print out the success message
echo 'Deployment email sent successfully.';
echo "\n";
echo "\n";
