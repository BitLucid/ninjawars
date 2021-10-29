/* eslint-disable semi */
/* global jQuery */
((function epicsRender(jQuery) {
    const $ = jQuery
    $('#stories section').hide()
    $('#sections-control a').click(function showHide(e) {
        e.preventDefault();
        const href = $(this).attr('href')
        $(href).toggle();
    })
})(jQuery))
