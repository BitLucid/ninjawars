/* Functions for ninjamaster */
/*jshint browser: true, white: true, plusplus: true*/
/*global $, NW */
$(function () {
    'use strict';

    

    $('#shop_form').submit(function () {
        if (!auth) {
            return false;
        } else {
            NW.storage.appState.set('quantity', $('#quantity').val());
            return true;
        }
    });
});

function confirmDeactivation(e, charId, callback){
    const message = 'Are you sure you want to deactivate this player character?'
    result = window.confirm(message);
    if(result === true){
        e.preventDefault();
        e.stopPropagation();
        deactivateChar(charId, callback)
        return true
    }else {
        e.preventDefault();
        e.stopPropagation();
        return false
    }
}

// Hit the api to deactivate the character
function deactivateChar(charId, callback) {
        // Deactivate a player character by id
        return $.getJSON(
            '/api?type=deactivate_char&data=' +
                charId +
                '&jsoncallback=?',
            callback
        );
}

// Reload the page afterwards
function afterDeactivation(data){
    return ()=> {
        console.log(data);
        location.reload();
    }
}