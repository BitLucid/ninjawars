/**
 * General behaviors for the signup page.
 */
// @ts-check

const { location: tLocation, top: tTop } = window;
const { location: tFrameLocation } = tTop || {};

/**
 * Executions on the signup page.
 */
(() => {
  // eslint-disable-next-line no-unused-vars
  const { log, debug } = console || { log: () => { /** noop */ }, debug: () => { /** noop */ } };
  debug('iife run on signup.js');
  // // eslint-disable-next-line eqeqeq
  if (tLocation != tFrameLocation) { // Framebreak on the signup page as well.
    if (window.top && window.top.location && window.top.location.href) {
      window.top.location.href = document.location.href;
    }
  }
  $( // on document ready
    () => {
      $('#become-a-ninja').hide().fadeIn(1500);
      const response = $('#signup input[name=g-recaptcha-response]').val();
      debug(response);
    },
  );
})();

/**
 * Callback for the recaptcha widget.
 * https://www.google.com/recaptcha/admin/site/692084162/settings
 */
// Is handled in the form itself by recFormSubmit
