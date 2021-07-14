'use strict'; // Strict checking.
/*global describe, beforeEach, afterEach, it, expect, g_isIndex, g_isRoot, g_isSubpage, NW, environment */

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

describe('NW', function () {
    describe('NW App testing context', function () {
        var app = NW;
        var env = environment;
        beforeEach(function () {});

        afterEach(function () {});

        it('should be able to see var defined in nw.js', function () {
            expect(env).toBeDefined();
            expect(env).toMatch(/NW.+context/);
        });
        it('should be able to get the NW object', function () {
            expect(app).toBeDefined();
        });
        it('should have access to the location checks', function () {
            expect(g_isIndex).toBeDefined();
            expect(g_isRoot).toBeDefined();
            expect(g_isSubpage).toBeDefined();
        });
        describe('NW app method calls', function () {
            it('should call the update function without error', function () {
                var data = {
                    player: true,
                    inventory: true,
                    message: true,
                    member_counts: true,
                    event: true,
                    unread_messages_count: true,
                };
                expect(NW.checkAPI_callback).toBeDefined();
                expect(NW.checkAPI_callback(data)).toBe(false);
            });
        });
    });
});
