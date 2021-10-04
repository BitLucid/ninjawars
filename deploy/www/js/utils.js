// eslint-disable-next-line import/prefer-default-export
export const debounce = (func, timeout = 300) => {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => { func.apply(this, args); }, timeout);
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
  return (typeof (process) !== 'undefined'
    && typeof (process.env) !== 'undefined'
    && process.env.NODE_ENV === 'development')
    || urlDebug
    || (typeof window !== 'undefined'
        && typeof window.NW !== 'undefined'
        && window.NW.debug
    );
};

// eslint-disable-next-line no-unused-vars
const noop = (_) => { /* no-op */ };

/**
 * Mimic console, except ignore certain loggings if not in debug mode
 * @returns {Function}
 */
export const logger = () => ({
  // eslint-disable-next-line no-console
  ...console,
  // eslint-disable-next-line no-console
  log: canDebug() ? console.log : noop,
  // eslint-disable-next-line no-console
  info: canDebug() ? console.info : noop,
  // eslint-disable-next-line no-console
  error: canDebug() ? console.error : noop,
  // eslint-disable-next-line no-console
  warn: canDebug() ? console.error : noop,
  // eslint-disable-next-line no-console
  dir: canDebug() ? console.dir : noop,
});
