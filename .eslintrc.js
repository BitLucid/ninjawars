/* eslint-disable quote-props */
module.exports = {
  env: {
    browser: true,
    es6: true,
    jest: true,
    jquery: true,
    // node: true,
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
    indent: ['error', 2, { SwitchCase: 1 }],
    'no-console': [
      'error',
      { allow: ['info', 'warn', 'error', 'debug', 'dir'] },
    ],
    'max-params': ['error', 4],
    'max-len': ['error', { code: 500 }],
    'max-statements': ['error', 30],
    'max-lines-per-function': ['error', 100],
    'max-lines': ['error', 500],
    'max-statements-per-line': ['error', { max: 2 }],
  },
};
