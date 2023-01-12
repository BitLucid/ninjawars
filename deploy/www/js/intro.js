/* Accent certain areas of the intro page in animated ways */
/* global NW */

/* eslint-disable prefer-arrow-callback */
/* eslint-disable func-names */

// @ts-check

// eslint-disable-next-line no-var
var presence = window.presence || {};
presence.intro = true;

$(function introManipulations() {
  // Page css hides the faq section to avoid FOUC
  const showFaqs = false; // Set faqs hidden by default.
  const showfaqsLink = $('#show-faqs');
  const faqsArea = $('#faqs');

  if (NW && NW.loggedIn) {
    // Depends on this script being called after NW.loggedIn gets set
    $('.not-user').hide();
  }
  if (!showFaqs) {
    showfaqsLink.show();
  } else {
    showfaqsLink.hide();
    faqsArea.show();
  }
  showfaqsLink.on('click', function (event) {
    event.preventDefault();
    faqsArea.slideToggle('slow');
    $(event.target).toggle();
    return false;
  });
}); // On ready
