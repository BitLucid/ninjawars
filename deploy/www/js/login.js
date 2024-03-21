/**
 * General behaviors for the login page.
 */
// @ts-check

const { location: lLocation, top: lTop } = window;
const { location: lFrameLocation } = lTop || {};
// eslint-disable-next-line no-unused-vars
const loginInitialized = true;
let loginFinalized = false;

const framebreakIfNeeded = () => {
  if (!window) {
    return;
  }
  // @ts-ignore
  // eslint-disable-next-line no-underscore-dangle
  if (window && window._testEnvironment) {
    // don't framebreak for test environment
    return;
  }
  // eslint-disable-next-line eqeqeq
  if (lLocation != lFrameLocation) { // Framebreak
    if (window.top && window.top.location && window.top.location.href) {
      window.top.location.href = document.location.href;
    }
  }
};

/**
 * Executions on the signup page.
 */
(() => {
  // eslint-disable-next-line no-unused-vars
  const { log, debug } = console || { log: () => { /** noop */ }, debug: () => { /** noop */ } };
  debug('login.js initialized');
  framebreakIfNeeded();

  // Just to indicate that the login js got finalized for testing
  loginFinalized = true;
  debug('login.js finalized');
})();
