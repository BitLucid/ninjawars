/* For the /intro page */
$(function () {
  if(NW && NW.loggedIn){ // Is this a race condition?
    $('.not-user').hide();
  }
  var show_faqs = false; // Set faqs hidden by default.
  var showfaqsLink = $('#show-faqs');
  var faqsArea = $('#faqs');
  if(!show_faqs){
    faqsArea.hide(); // To avoid flashing of content hide early on.
  } else {
    showfaqsLink.hide(); // Otherwise, hide the show-hide link.
  }
  showfaqsLink.click(function(event){
    faqsArea.slideToggle('slow');
    $(event.target).toggle();
    return false;
  });

  // Fade the link colors in gradually, one at a time.
  $('#later-progression a')
      .each(function(secs, element){
      setTimeout(function (){
          $(element).css({'color':'steelBlue'});
      }, 1000*(secs+1)*5);
  });
  // Finally, fade in the join link last.
  $('#join-link').each(function(index, element){
      setTimeout(function (){
          $(element).css({'color':'steelBlue', 'font-size':'1.5em'});
      }, 1000*26);
  });
});