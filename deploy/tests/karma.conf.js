/* eslint-disable max-lines */
const puppeteer = require('puppeteer');

process.env.CHROME_BIN = puppeteer.executablePath();
/* eslint-disable max-len */
// Karma configuration
// Generated on Sun Mar 20 2016 07:47:48 GMT-0400 (EDT)

// eslint-disable-next-line max-lines-per-function
module.exports = function karmaConfig(config) {
  config.set({
    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '../..',

    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine', 'requirejs', 'sinon'],

    // list of files / patterns to load in the browser
    files: [
      'test-main.js',
      'deploy/www/js/jquery.min.js',
      'deploy/www/js/nw.js',
      {
        pattern: 'deploy/www/js/utils.js',
        type: 'module',
        included: true,
      },
      {
        pattern: 'deploy/www/js/api.js',
        type: 'module',
        included: true,
      },
      {
        pattern: 'deploy/www/js/stats.js',
        type: 'module',
        included: true,
      },
      {
        pattern: 'deploy/www/js/fight.js',
        type: 'module',
        included: true,
      },
      {
        pattern: 'deploy/www/js/epics.js',
        type: 'module',
        included: true,
      },
      {
        pattern: 'deploy/www/js/homepage.js',
        type: 'module',
        included: true,
      },
      {
        pattern: 'deploy/www/js/intro.js',
        type: 'module',
        included: true,
      },
      /*
      {
        pattern: 'deploy/www/js/ninjamaster.js',
        type: 'module',
        included: true,
      },
      */
      {
        pattern: 'deploy/www/js/seededRandom.js',
        type: 'module',
        included: true,
      },
      {
        pattern: 'deploy/www/js/talk.js',
        type: 'module',
        included: true,
      },
      // { pattern: 'deploy/www/js/nw.js', type: 'module', included: true },
      { pattern: 'deploy/www/js/*.js', included: true },
      { pattern: 'deploy/tests/js/*Spec.js', included: false },
    ],

    // list of files to exclude
    exclude: [
      'deploy/www/js/jquery.timeago.js',
      'deploy/www/js/jquery.linkify.js',
      'deploy/www/js/jquery.linkify.min.js',
      'deploy/www/js/bootstrap.min.js',
      'deploy/www/js/imgur.min.js',
      // 'deploy/www/js/api.js', // Temporary excludes from here because of import/export keyword
      // 'deploy/www/js/seededRandom.js',
      'deploy/www/js/ninjamaster.js',
      // 'deploy/www/js/talk.js',
      'deploy/www/js/TalkSpec.js',
      'deploy/tests/js/NinjamasterSpec.js',
      'deploy/tests/js/ApiSpec.js',
    ],

    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {},

    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['progress'],

    // web server port
    port: 9876,

    // enable / disable colors in the output (reporters and logs)
    colors: true,

    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,

    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,

    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['ChromeHeadless'],

    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false,

    // Concurrency level
    // how many browser should be started simultaneous
    concurrency: Infinity,
  });
};
