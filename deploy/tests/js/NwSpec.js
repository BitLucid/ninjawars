/* global g_isIndex, g_isRoot, g_isSubpage, NW */

/* require(['jquery'], function($) {
    $('body').css('background-color', 'green');
}); */
/* requirejs(["nw.js"], function(NW) {
    //Called when nw.js is included

    //This function is called when scripts/helper/util.js is loaded.
    //If util.js calls define(), then this function is not fired until
    //util's dependencies have loaded, and the util argument will hold
    //the module value for "helper/util".
}); */

describe('NW', () => {
    describe('NW App testing context', () => {
        const app = NW;
        const env = window && window.nwEnvironment;

        it('should be able to see var defined in nw.js', () => {
            expect(env).toBeDefined();
            expect(env).toMatch(/NW.+context/);
        });
        it('should be able to get the NW object', () => {
            expect(app).toBeDefined();
        });
        it('should have access to the location checks', () => {
            expect(g_isIndex).toBeDefined();
            expect(g_isRoot).toBeDefined();
            expect(g_isSubpage).toBeDefined();
        });
        describe('NW app method calls', () => {
            it('should call the update function without error', () => {
                const data = {
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
