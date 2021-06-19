/* Assist in clan operations (currently only clan leave) */
/*jshint browser: true, white: true, plusplus: true*/
/*global $, window*/
$(function () {
    'use strict';
    function leaveClan() {
        if (window.confirm('Do you really want to exit the clan?')) {
            window.location = '/clan/leave';
        }

        return false;
    }

    $('#leave-clan').click(function () {
        leaveClan();
        return false;
    });
});
