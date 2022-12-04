/* eslint-disable quote-props */
module.exports = {
  env: {
    browser: true,
    es6: true,
    jquery: true,
  },
  extends: 'airbnb',
  globals: {
    Atomics: 'readonly',
    SharedArrayBuffer: 'readonly',
  },
  parserOptions: {
    ecmaFeatures: {
      jsx: true,
    },
    ecmaVersion: 2018,
    sourceType: 'module',
  },
  plugins: ['react'],
  rules: {
    'no-console': [
      'error',
      { allow: ['info', 'warn', 'error', 'debug', 'dir'] },
    ],
    'complexity': [
      'error',
      { max: 25 },
    ],
    'max-len': [
      'error',
      150,
    ],
    'max-lines': [
      'error',
      200,
    ],
    'max-lines-per-function': [
      'error',
      70,
    ],
    'max-statements': [
      'error',
      50,
    ],
  },
};
