/* eslint-disable func-names */
/* eslint-disable prefer-arrow-callback */

// @ts-check

// eslint-disable-next-line no-var
var presence = window.presence || {};
presence.homepage = true;

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
