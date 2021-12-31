// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, presence */

describe('casino', () => {
  describe('Introductory casino script testing context', () => {
    beforeEach(() => { });

    afterEach(() => { });

    it('should have initialized the casino functionality', () => {
      // Running the file should work without error
      expect(presence.casino).toBeDefined();
    });

    /*
    it('should be able to run casino.whatever', () => {
      const output = casino.whatever();
      expect(output).toBeTruthy();
    });
    */
  });
});
