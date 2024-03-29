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
    ecmaVersion: 2020,
    sourceType: 'module',
  },
  plugins: ['react'],
  rules: {
    'no-console': [
      'error',
      { allow: ['info', 'warn', 'error', 'debug', 'dir'] },
    ],
    'no-unreachable': 'error',
    'complexity': [
      'error',
      10,
    ],
    'max-lines': [
      'error',
      103,
    ],
    'max-lines-per-function': [
      'error',
      53,
    ],
    'max-statements': [
      'error',
      23,
    ],
  },
};
