<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\SessionFactory;
use \RuntimeException;

// get the quest model shortly


// Get the quests from the database.
function get_quests($quest_id=null){
    $many_or_one = $quest_id? "WHERE quest_id= :quest_id" : '';
    // quest_id, title, player_id, tags, description, rewards, obstacles, expiration, proof
    $query = "SELECT quest_id, uname as giver, player_id, title, tags, description, rewards, obstacles, expiration, proof FROM quests q join players p on p.player_id = q._player_id".$many_or_one;
    if($quest_id){
        //$quests = query($query);        
    } else {
        //$quests = query($query, array(':quest_id'=>array($quest_id, PDO::PARAM_INT)));
    }
    if(DEBUG){ // While debugging, mock a single quest
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


/**
 * Get player quests, accept, and view individual ones, etc.
 */
class QuestController extends AbstractController {
    const ALIVE = false;
    const PRIV  = false;

    /**
     *
     */
    public function __construct(){
    }

    /**
     * Display that list of public quests!
     */
    public function index(){
        $quest_id = in('quest_id');
        $quest_accepted = in('quest_accepted');
        $quests = format_quests(get_quests());
        $title = 'Quests';
        $tpl = 'quests.tpl';

        $parts = [
            'quests'=>$quests, 
            ];

        return [
            'template'=>$tpl,
            'title'=>$title,
            'parts'=>$parts,
            'options'=>null,
            ];
    }

    /**
     * Accept posted quest info to create a new quest.
     */
    public function create(){
        throw new RuntimeException('Creating quests not yet implemented.');
        $post = post();
        $title = 'Create a Quest';
        $parts = [
            'quest'=>$quest,
            ];

        return [
            'template'=>'quests.single_quest.tpl',
            'title'=>$title,
            'parts'=>$parts,
            'options'=>null,
            ];
    }

    /**
     * Try to view a single quest
     */
    public function view($qid = null){
        // Hack to get the quest/view/{id}
        $url_part = $_SERVER['REQUEST_URI'];
        if(preg_match('#\/(\w+)(\/)?$#',$url_part,$matches)){
            $in_quest_id=isset($matches[1])? $matches[1] : null;
        } else {
            $in_quest_id = null;
        }
        $quest_id = whichever($in_quest_id, $qid);
        $quests = null;
        $quest = null;

        // When accepting a quest, simply display that quest.
        if($quest_id){
            $quests = format_quests(get_quests($quest_id));
            $title = 'A Quest';
            $quest = reset($quests);
            $tpl = 'quests.single_quest.tpl';
        }

        $parts = [
            'quest'=>$quest, 
            'quests'=>$quests,
            ];

        return [
            'template'=>$tpl,
            'title'=>$title,
            'parts'=>$parts,
            'options'=>null,
            ];
    }

    /**
     * accept just wraps a single quest view for now
     */
    public function accept(){
        // Hack to get the quest/accept/{id}
        $url_part = $_SERVER['REQUEST_URI'];
        if(preg_match('#\/(\w+)(\/)?$#',$url_part,$matches)){
            $in_quest_id=isset($matches[1])? $matches[1] : null;
        } else {
            $in_quest_id = null;
        }
        return $this->view($in_quest_id);
    }

}
