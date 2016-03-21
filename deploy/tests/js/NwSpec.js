"use strict"; // Strict checking.

/*require(['jquery'], function($) {
    $('body').css('background-color', 'green');
});*/
requirejs(["nw.js"], function(NW) {
    //Called when nw.js is included

    //This function is called when scripts/helper/util.js is loaded.
    //If util.js calls define(), then this function is not fired until
    //util's dependencies have loaded, and the util argument will hold
    //the module value for "helper/util".
});
dump('Check if nw gets defined');
dump(require.defined('nw.js'));

describe('NW', function() {
  describe('debug is available', function() {
    var app;
    beforeEach(function(NW) {
        app = NW;
    });

    afterEach(function(NW) {
        app = NW;
    });
    it('should return false when console.log is not present', function() {
      var debug = app.debug();
      expect(debug).toEqual(false);
    });
  });
});