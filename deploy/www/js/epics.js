/* eslint-disable indent */
/* eslint-disable semi */

/* global jQuery $ */
$(() => {
    const hash = window && window.location && window.location.hash
    const $ = jQuery
    $('#stories > section').hide()
    $('#sections-control a').click(function showHide(e) {
        e.preventDefault();
        const href = $(this).attr('href')
        $(href).toggle();
        window.location.hash = href;
    })
    if (hash) {
        $(hash).show();
    } else {
        $('#stories > section:first-child').show();
    }
})
