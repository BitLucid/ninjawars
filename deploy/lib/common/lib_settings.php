<?php
// Get and set/save a changable array of a player's settings.


function get_settings($refresh=null){
    static $settings;
    if($refresh){
        $settings = null; // Nullify to pull from the database again.
    }
    if(!$settings){
        // If the static var isn't present yet
        global $sql;
        $user_id = get_user_id();
        $ser_settings = $sql->QueryItem("SELECT settings_store from settings where player_id = '".sql($user_id)."'");
        $settings = unserialize($ser_settings);
        if(!$settings){
            $settings = array();
        }
    }
    return $settings;
}


function set_setting($name, $setting){
    global $sql;
    $settings = get_settings();
    $user_id = get_user_id();
    if(!is_array($settings)){
        $settings = array($name=>$setting);
        $sql->Insert("INSERT into settings 
            (settings_store, player_id) values 
            ('".sql(serialize($settings))."', '".sql($user_id)."')");
    } else {
        $settings = $settings+array($name => $settings);
        global $sql;
        $sql->Update("Update settings set settings_store = '".sql(serialize($settings))."' where player_id = '".sql($user_id)."'");
    }
    return get_settings($refresh=true);
}

function save_setting($settings){
    global $sql;
    $user_id = get_user_id();
    $sql->Update("Update settings set settings_store = '".sql(serialize($settings))."' where player_id = '".sql($user_id)."'");
    return get_settings($refresh=true);
}


?>
