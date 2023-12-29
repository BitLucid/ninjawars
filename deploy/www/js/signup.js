/**
 * General behaviors for the signup page.
 */
// @ts-check

// eslint-disable-next-line no-var
// const { location: tLocation, top: tTop } = window;
// const { location: tFrameLocation } = tTop || {};
const { log } = console || {};

/**
 * Executions on the signup page.
 */
(() => {
  log('iife run on signup.js');
  // // eslint-disable-next-line eqeqeq
  // if (tLocation != tFrameLocation) { // Framebreak on the signup page as well.
  //   if (window.top && window.top.location && window.top.location.href) {
  //     window.top.location.href = document.location.href;
  //   }
  // }
  $( // on document ready
    () => {
    },
  );
})();

const signupFormId = 'signup';

// eslint-disable-next-line no-unused-vars
function onSubmit(token) {
  // For recaptcha
  if (!document) {
    throw new Error('Invalid call to recapcha onSubmit, in environment with no document');
  } else {
    document && document.getElementById(signupFormId).submit();
  }
}
