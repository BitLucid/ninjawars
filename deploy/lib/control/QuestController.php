<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\data\Quest;
use \RuntimeException;

/**
 * Get player quests, accept, and view individual ones, etc.
 * at example urls like /quest and /quest/view/##
 */
class QuestController extends AbstractController {
    const ALIVE = false;
    const PRIV  = false;

    /**
     * Display that list of public quests!
     * @return StreamedViewResponse
     */
    public function index($p_dependencies){
        $request = RequestWrapper::$request;
        $quest_id = $request->get('quest_id');
        $quest_accepted = $request->get('quest_accepted');
        $quests = Quest::hydrate_quests(Quest::get_quests());
        $title = 'Quests';
        $tpl = 'quests.tpl';

        $parts = [
            'quests'=>$quests, 
            ];

        return new StreamedViewResponse($title, $tpl, $parts);
    }

    /**
     * Accept posted quest info to create a new quest.
     */
    public function create($p_dependencies){
        throw new RuntimeException('Creating quests not yet implemented.', 500);
        $post = '';
        $title = 'Create a Quest';
        $parts = [
            'quest'=>$quest,
            ];

        return new StreamedViewResponse($title, 'quests.single_quest.tpl', $parts);
    }

    /**
     * Try to view a single quest
     */
    public function view($p_dependencies, $qid = null){
        // Hack to get the quest/view/{id}
        $url_part = $_SERVER['REQUEST_URI'];
        if(preg_match('#\/(\w+)(\/)?$#',$url_part,$matches)){
            $quest_id=isset($matches[1])? $matches[1] : $qid;
        } else {
            $quest_id = $qid;
        }
        $quests = null;
        $quest = null;
        $tpl = 'quests.single_quest.tpl';
        $title = 'A Quest';
        $error = null;

        if($quest_id){
            try{
                $quest = Quest::hydrate_quests([Quest::where('quest_id', $quest_id)->get()]);
            } catch(\Exception $e){
                $error = $e->getMessage()? 'There was a problem viewing this quest' : null;
            }
        }

        $parts = [
            'quest'=>$quest, 
            'quests'=>$quests,
            'error'=>$error,
            ];

        return new StreamedViewResponse($title, $tpl, $parts);
    }

    /**
     * accept just wraps a single quest view for now, eventually will make viewer as one of the questors
     */
    public function accept($p_dependencies){
        // Hack to get the quest/accept/{id}
        $url_part = $_SERVER['REQUEST_URI'];
        if(preg_match('#\/(\w+)(\/)?$#',$url_part,$matches)){
            $in_quest_id=isset($matches[1])? $matches[1] : null;
        } else {
            $in_quest_id = null;
        }
        return $this->view($p_dependencies, $in_quest_id);
    }
}
