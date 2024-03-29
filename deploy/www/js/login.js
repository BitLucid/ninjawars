/**
 * General behaviors for the login page.
 */
// @ts-check

// eslint-disable-next-line no-unused-vars
const loginInitialized = true;
// eslint-disable-next-line no-unused-vars
let loginFinalized = false;

/**
 * Because we're using iframe navigation, hitting the [continue] button inside the frame
 * and lead to a login page inside the frame... ...then if a user were to login
 * inside the frame, they might get a site inside the with a third frame inside that
 * not what we want, so we are framebreaking on the login page to prevent that
 * @returns void
 */
// eslint-disable-next-line complexity
const framebreakIfNeeded = () => {
  const { debug } = console || { log: () => { /** noop */ }, debug: () => { /** noop */ } };
  // skipcq: JS-W1044
  // @ts-ignore
  // eslint-disable-next-line no-underscore-dangle
  if (!window || (window && window._testEnvironment)) { // don't framebreak for test environment
    return;
  }
  // check for a top frame NW object
  const { location: lLocation, top: tTop } = window ?? {};
  const { href: lHref } = lLocation ?? {};
  const { location: tFrameLocation } = tTop ?? {};
  const { href: tFrameHref } = tFrameLocation ?? {};

  // If cypress in the tFrameHref, then don't framebreak
  if (tFrameHref && tFrameHref.includes('cypress')) {
    return;
  }

  /**
   * If there is a top frame, and top isn't the same as current,
   * and the top actually has a NW object, then framebreak.
   * */
  // eslint-disable-next-line eqeqeq
  if (tFrameHref && tFrameHref !== lHref) { // then Framebreak
    if (window.top && window.top.location && window.top.location.href) { // skipcq: JS-W1044
      debug('outer frame on login page, to framebreaking...');
      window.top.location.href = document.location.href;
    }
  }
};

/**
 * Executions on the signup page.
 */
(() => {
  // eslint-disable-next-line no-unused-vars
  const { debug } = console || { log: () => { /** noop */ }, debug: () => { /** noop */ } };
  debug('login.js initialized');
  framebreakIfNeeded();

  // Just to indicate that the login js got finalized for testing
  loginFinalized = true;
  debug('login.js finalized');
})();
