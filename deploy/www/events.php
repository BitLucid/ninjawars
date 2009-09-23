<?php
$private    = true;
$alive      = false;
$quickstat  = false;
$page_title = "Events";
include SERVER_ROOT."interface/header.php";

$user_id = get_user_id();
$events = get_events($user_id);

read_events($user_id); // mark events as viewed.



$event_list = '';
if(!empty($events)){
    foreach($events as $loop_event){
        $loop_event['message'] = out($loop_event['message']);
        $event_list .= render_template('single_event.tpl', array('event' => $loop_event));
    }
}

$parts = get_certain_vars(get_defined_vars());
echo render_template('events.tpl', $parts);


include SERVER_ROOT."interface/footer.php";

?>
