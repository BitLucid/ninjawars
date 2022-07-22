/* eslint-disable indent */
/* eslint-disable semi */

// eslint-disable-next-line no-var
var presence = window.presence || {};
presence.epics = true;

/* global jQuery $ */
$(() => {
    const hash = window && window.location && window.location.hash;
    const $ = jQuery;
    $('#stories > section').hide();
    $('#sections-control a').click(function showHide(e) {
        e.preventDefault();
        $('#stories > section').hide();
        const href = $(this).attr('href');
        if (href && $(href).length) {
            $(href).show();
        } else {
            $('.expose-area-error').show();
        }
        window.location.hash = href;
    });
    if (hash) {
        $(hash).show();
    } else {
        $('#stories > section:first-child').show();
    }
});
