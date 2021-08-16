/* Functions for ninjamaster */
/*jshint browser: true, white: true, plusplus: true*/
/*global $ */
$(function () {
    'use strict';

});

// Check in the ui for an additional confirm of deactivation
function confirmDeactivation(e, charId, callback) {
    var message = 'Are you sure you want to deactivate this player character?';
    var result = window.confirm(message);
    if (result === true) {
        e.preventDefault();
        e.stopPropagation();
        deactivateChar(charId, function(){
            afterDeactivation(charId, callback);
        });
        return true;
    } else {
        e.preventDefault();
        e.stopPropagation();
        return false;
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
function afterDeactivation(data, callback) {
    return function() {
        console.log(data);
        location.reload();
        return callback && callback();
    }
}

/* exported deactivateChar afterDeactivation confirmDeactivation */