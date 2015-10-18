<?php
$private    = false;
$alive      = false;
$quickstat  = false;
$page_title = "Quests";
//require_once(LIB_ROOT."control/lib_player_list.php");

init($private=false, $alive=false);


// Model
function get_quests($quest_id=null){
    $many_or_one = $quest_id? "WHERE quest_id= :quest_id" : '';
    // quest_id, title, player_id, tags, description, rewards, obstacles, expiration, proof
    $query = "SELECT quest_id, uname as giver, player_id, title, tags, description, rewards, obstacles, expiration, proof FROM quests q join players p on p.player_id = q._player_id".$many_or_one;
    if($quest_id){
        //$quests = query($query);        
    } else {
        //$quests = query($query, array(':quest_id'=>array($quest_id, PDO::PARAM_INT)));
    }
    if(DEBUG){
        $quests = array(
            array('quest_id'=>1, 'giver'=>'glassbox', 'player_id'=>10, 'title'=>'some quest', 'tags'=>'fake bob jim',
        'description'=>'A description', 'rewards'=>'gold:30,kills:7,karma:35', 'obstacles'=>'wall, enemy , some guy,monster',
        'expiration'=>'10/20/30.12.14.96', 'proof' => 'have to show a screenshot'),
        );
    }
    return $quests;
}

// Controller, so to speak?
function format_quests($quests_data){
    foreach($quests_data as $quest){
        $quest['rewards'] = json_decode($quest['rewards']);
        // Eventually linkify the tags here.
        $quest['obstacles'] = json_decode($quest['obstacles']);
        // Unfold the questers here.
        $quest['questers'] = get_questors($quest['quest_id']);
        $quest['questers']= array('10'=>'glassbox');
    }
    return $quests_data;
}

function save_quest($quest){
}

function create_quest($quest){
}

// Get the questors 
function get_questors($quest_id){
    $sel = "SELECT p.player_id, p.uname 
        from players p join questers q on p.player_id = q._player_id 
        where quest_id = :quest_id";
    //$questers = query($sel, array(":quest_id"=>array($quest_id, PDO::PARAM_INT)));
    if(DEBUG){
        $questers = array(
            array('player_id'=>10, 'uname'=>'glassbox')
        );
    }
    return $questers;
}


$quest_id = in('quest_id');
$quest_accepted = in('quest_accepted');
$quests = null;

// When accepting a quest, simply display that quest.
if($quest_accepted){
    $quest_id = $quest_accepted;
}
// Process the single quest view is quest_id isn't null, otherwise get all quests.
$quests = format_quests(get_quests($quest_id));
if(count($quests) == 1){
    $quest = reset($quests);
    $tpl = 'quests.single_quest.tpl';
} else {
    $tpl = 'quests.tpl';
}

$parts = array('quest'=>$quest, 'quests'=>$quests, 'quest_accepted'=>$quest_accepted);

$options = array('quickstat'=>'player');

display_page($tpl, 'Quests', $parts, $options);
