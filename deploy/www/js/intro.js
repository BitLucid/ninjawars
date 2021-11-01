/* Accent certain areas of the intro page in animated ways */
/* jshint browser: true, white: true, plusplus: true */
/* global $, NW */


function fadeIntro($) {
    // Fade the colors for the links in gradually to slowly introduce the concepts.
    $('#later-progression a').each(function slowFade(secs, element) {
        setTimeout(function fade() {
            $(element).removeClass('dull-link');
        }, 1000 * (secs + 1) * 1);
    });
    // Finally, accentuate the join link after a while.
    $('#join-link').each(function nearFinalFade(index, element) {
        setTimeout(function finalFade() {
            $(element).removeClass('dull-link');
        }, 1000 * 15);
    });

}


(function introManipulations($) {
    // Page css hides the faq section to avoid FOUC
    var showFaqs = false; // Set faqs hidden by default.
    var showfaqsLink = $('#show-faqs');
    var faqsArea = $('#faqs');

    fadeIntro($);

    if (NW && NW.loggedIn) {
        // Depended on this script being called after NW.loggedIn gets set
        $('.not-user').hide();
    }
    if (!showFaqs) {
        showfaqsLink.show();
    } else {
        showfaqsLink.hide();
        faqsArea.show();
    }
    showfaqsLink.click(function (event) {
        faqsArea.slideToggle('slow');
        $(event.target).toggle();
        return false;
    });
}($)); // End of IIFE
