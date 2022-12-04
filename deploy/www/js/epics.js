// @ts-check
/* eslint-disable indent */
/* eslint-disable semi */
/* global jQuery, $ */

// eslint-disable-next-line no-var
var presence = window.presence || {};
presence.epics = true;

$(() => {
  const hash = window && window.location && window.location.hash;
  const $ = jQuery;
  $('#stories > section').hide();
  $('#sections-control a').click(function showHide(e) {
    e.preventDefault();
    $('#stories > section').hide();
    const href = $(this).attr('href');
    if (href && $(href).length) {
      $(href).show();
      $('.expose-area-error').hide();
    } else {
      $('.expose-area-error').show();
    }
    window.location.hash = href;
  });
  if (hash) {
    $(hash).show();
  } else {
    $('#stories > section:first-child').show();
  }
});
