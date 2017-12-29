/* For the /intro page */
$(function () {
  if(NW && NW.loggedIn){ // Is this a race condition?
    $('.not-user').hide();
  }
  var show_faqs = false; // Set faqs hidden by default.
  document.getElementById('faqs').style.visibility = 'hidden'; // Hide fast to avoid layout flash
  var showfaqsLink = $('#show-faqs');
  var faqsArea = $('#faqs');
  if(!show_faqs){
    faqsArea.hide(); // To avoid flashing of content hide early on.
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