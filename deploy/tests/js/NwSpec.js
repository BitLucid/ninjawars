"use strict"; // Strict checking.

// Pull in the environment from the nw.js load.
var environment = this.environment;

var NW = this.NW;

/*require(['jquery'], function($) {
    $('body').css('background-color', 'green');
});*/
/*requirejs(["nw.js"], function(NW) {
    //Called when nw.js is included

    //This function is called when scripts/helper/util.js is loaded.
    //If util.js calls define(), then this function is not fired until
    //util's dependencies have loaded, and the util argument will hold
    //the module value for "helper/util".
});*/

describe('NW', function() {
  describe('NW App testing context', function() {
    var app;
    beforeEach(function() {
        this.environment = environment;
    });

    afterEach(function() {
        this.environment = null;
    });
    it('should be able to see var defined in nw.js', function() {
      expect(this.environment).toEqual('NW App context');
    });
  });
});