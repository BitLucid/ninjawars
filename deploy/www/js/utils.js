/* eslint-disable no-console */
export const debounce = (func, timeout = 300) => {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      func.apply(this, args);
    }, timeout);
  };
};

/**
 * Check whether in a debuggings mode
 * @returns boolean
 */
export const canDebug = () => {
  let urlDebug = false;
  if (window && window.location && window.location.search) {
    const urlParams = new URLSearchParams(window.location.search);
    const debug = urlParams.get('debug');
    urlDebug = debug === '1';
  }
  return (
    (typeof process !== 'undefined'
      && typeof process.env !== 'undefined'
      && process.env.NODE_ENV === 'development')
    || urlDebug
    || (typeof window !== 'undefined'
      && typeof window.NW !== 'undefined'
      && window.NW.debug)
  );
};

// eslint-disable-next-line no-unused-vars
const noop = (_) => {
  /* no-op */
};

/**
 * Mimic console, except ignore certain loggings if not in debug mode
 * @returns {Function}
 * @example logger().debug('hello world');
 */
export const logger = () => ({
  // console enabled for this file
  ...console,
  // eslint-disable-next-line no-console
  log: canDebug() ? console.log : noop,
  info: canDebug() ? console.info : noop,
  error: canDebug() ? console.error : noop,
  warn: canDebug() ? console.error : noop,
  dir: canDebug() ? console.dir : noop,
  debug: canDebug() ? console.debug : noop,
});

export const urlParam = (key) => {
  const urlParams = new URLSearchParams(window && window.location.search);
  return urlParams.get(key);
};

/**
 * See seededRandom.js for all the seeded random needs
 * (e.g. when you want a set of different things that stays by seed)
 */
