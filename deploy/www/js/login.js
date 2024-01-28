/**
 * General behaviors for the login page.
 */
// @ts-check

const { location: lLocation, top: lTop } = window;
const { location: lFrameLocation } = lTop || {};

/**
 * Executions on the signup page.
 */
(() => {
  // eslint-disable-next-line no-unused-vars
  const { log, debug } = console || { log: () => { /** noop */ }, debug: () => { /** noop */ } };
  debug('iife run on login.js');
  // eslint-disable-next-line eqeqeq
  if (lLocation != lFrameLocation) { // Framebreak
    if (window.top && window.top.location && window.top.location.href) {
      window.top.location.href = document.location.href;
    }
  }
})();
