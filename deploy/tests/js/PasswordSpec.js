// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, presence */

describe('password', () => {
    describe('Introductory script testing context', () => {
        beforeEach(() => {});

        afterEach(() => {});

        it('should have initialized the password functionality', () => {
            // Running the file should work without error
            expect(presence).toBeDefined();
            expect(presence.passwords).toBeDefined();
        });
    });
});
