// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, presence */

describe('shop', () => {
    describe('Introductory script testing context', () => {
        beforeEach(() => {});

        afterEach(() => {});

        it('should have initialized the shop functionality', () => {
            // Running the file should work without error
            expect(presence).toBeDefined();
            expect(presence.shop).toBeDefined();
        });
    });
});
