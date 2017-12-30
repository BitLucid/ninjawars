/* Accent certain areas of the intro page in animated ways*/
/*jshint browser: true, white: true, plusplus: true*/
/*global $, NW, document, console*/
$(function () {
  'use strict';
  if(NW && NW.loggedIn){ // Depended on this script being called after NW.loggedIn gets set
    $('.not-user').hide();
  }
  var show_faqs = false; // Set faqs hidden by default.
  console.info('Hiding FAQ.');
  var faqsNode = document.getElementById('faqs');
  if(faqsNode){
    faqsNode.style.display = 'none'; // Hide fast to avoid layout flash
  }
  var showfaqsLink = $('#show-faqs');
  var faqsArea = $(faqsNode);
  if(!show_faqs){
    showfaqsLink.show();
  } else {
    showfaqsLink.hide();
    faqsArea.show();
  }
  showfaqsLink.click(function(event){
    faqsArea.slideToggle('slow');
    $(event.target).toggle();
    return false;
  });

  // Fade the colors for the links in gradually to slowly introduce the concepts.
  var cssLinkify = {color:'steelBlue'};
  $('#later-progression a')
      .each(function(secs, element){
      setTimeout(function (){
          $(element).css(cssLinkify);
      }, 1000*(secs+1)*1);
  });
  // Finally, accentuate the join link after a while.
  var cssAccent = {color:'steelBlue', 'font-size':'1.5em'};
  $('#join-link').each(function(index, element){
      setTimeout(function (){
          $(element).css(cssAccent);
      }, 1000*26);
  });
});