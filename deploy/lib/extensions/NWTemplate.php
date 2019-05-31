<?php
namespace NinjaWars\core\extensions;

use NinjaWars\core\data\Player;
use \Smarty;

/**
 * Wrap smarty and do a little bit more
 */
class NWTemplate extends Smarty {
    public function __construct() {
        parent::__construct();
        $this->caching = 1;

        $this->addTemplateDir(TEMPLATE_PATH);

        $this->setCompileDir(COMPILED_TEMPLATE_PATH);

        $this->addPluginsDir(TEMPLATE_PLUGIN_PATH);
    }

    /**
     * Displays a template wrapped in the header and footer as needed.
     */
    public function displayPage($template, $title=null, $local_vars=array(), $options=null) {
        // Updates the quickstat via javascript if requested.
        $quickstat        = isset($options['quickstat'])? $options['quickstat'] : null;
        $quickstat        = ($quickstat ? $quickstat : (isset($local_vars['quickstat'])? $local_vars['quickstat'] : null) );
        $body_classes     = isset($options['body_classes'])? $options['body_classes'] :
            (isset($local_vars['body_classes'])? $local_vars['body_classes'] : null);
        $is_index         = isset($options['is_index'])? $options['is_index'] : false;
        $user_id          = SessionFactory::getSession()->get('player_id');
        $player           = Player::find($user_id);
        $public_char_info = ($player ? $player->publicData() : []); // Char info to pass to javascript.

        $this->assign($local_vars);
        $this->assign('logged_in', $user_id);
        $this->assign('user_id', $user_id);
        $this->assign('title', $title);
        $this->assign('quickstat', $quickstat);
        $this->assign('is_index', $is_index);
        $this->assign('json_public_char_info', ($public_char_info ? json_encode($public_char_info) : null));
        $this->assign('body_classes', $body_classes);
        $this->assign('main_template', $template);

        $this->display('full_template.tpl');
    }
}
