<?php
require_once(realpath(__DIR__).'/resources.php');
require_once(realpath(__DIR__) . '/checkbase.php');

// Check for database
$connected = (bool) query_item('select 1 from players limit 1');
$is_superuser = (bool) query_item('select usesuper from pg_user where usename = CURRENT_USER;') === true;

function passfailB($passed, $pass, $fail)
{
    $messaging = ($passed? '[PASSING]: Reason '.$pass : '[FAILING]: Reason '.$fail);
    echo "$messaging\n";
    return $passed;
}

// Executing and outputing checks, to try to run all before final return
$outcomes = [
    passfailB($connected, 'Able to connect and list a player from the players table of the database', 'Unable to select from players table of the database'),
    passfailB(!$is_superuser, 'Connected to database as appropriate user level', 'Connected as database superuser, you want to connect as a lower permission role')
];

return (($outcomes[0] && $outcomes[1]) ? 0 : 1); // Reversed logic due to linux script return values expected
