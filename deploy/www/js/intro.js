/* eslint-disable prefer-arrow-callback */
/* eslint-disable func-names */
/* Accent certain areas of the intro page in animated ways */
/* jshint browser: true, white: true, plusplus: true */
/* global $, NW */

$(function introManipulations() {
  // Page css hides the faq section to avoid FOUC
  const showFaqs = false; // Set faqs hidden by default.
  const showfaqsLink = $('#show-faqs');
  const faqsArea = $('#faqs');

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
    event.preventDefault();
    faqsArea.slideToggle('slow');
    $(event.target).toggle();
    return false;
  });
}); // On ready
