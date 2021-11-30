/* eslint-disable func-names */
/* eslint-disable prefer-arrow-callback */
/* global $ */
$(function indexBehaviors() {
  const horizontalClass = 'partial';
  $('#chat-toggle').click(function () {
    $('#main-column').toggleClass(horizontalClass);
    $('aside').fadeToggle('slow');
  });
  $('aside').hide();
  $('#main-column').removeClass(horizontalClass);
  // Fix for weird viewport sizing
});
