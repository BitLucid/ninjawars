<?php
// Get and set/save a changable array of a player's settings.


function get_settings($refresh=null){
    static $settings;
    if($refresh){
        $settings = null; // Nullify to pull from the database again.
    }
    if(!$settings){
        // If the static var isn't present yet, so get it
        global $sql;
        $user_id = get_user_id();
        $serial_settings = $sql->QueryItem("SELECT settings_store from settings where player_id = '".sql($user_id)."'");
        if($serial_settings){
            $settings = unserialize($serial_settings);
        }
        if(!$settings){
            $settings = null;
        }
    }
    return $settings;
}

function get_setting($name){
    $set = get_settings();
    return (isset($set[$name])? $set[$name] : null);
}


function set_setting($name, $setting){
    global $sql;
    $cur = get_settings();
    $new = array($name=>$setting);
    if($cur){
        $joined = $new + $cur;
    } else {
        $joined = $new;
    }
    $set = save_settings($joined);
    return $set;
}

function save_settings($settings){
    global $sql;
    $user_id = get_user_id();
    assert($user_id);
    $settings_exist = $sql->QueryItem("SELECT count(settings_store) from settings where player_id = '".sql($user_id)."'");
    if($settings_exist){
        $sql->Update("Update settings 
            set settings_store = '".sql(serialize($settings))."' 
            where player_id = '".sql($user_id)."'");
    } else {
        $sql->Insert("INSERT into settings 
            (settings_store, player_id) 
            values ('".sql(serialize($settings))."', '".sql($user_id)."')");
    }
    return get_settings($refresh=true);
}


?>
