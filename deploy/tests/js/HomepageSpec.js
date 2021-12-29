// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, presence */

describe('homepage', () => {
  describe('Introductory homepage script testing context', () => {
    beforeEach(() => { });

    afterEach(() => { });

    it('should have initialized the homepage functionality', () => {
      // Running the file should work without error
      expect(presence.homepage).toBeDefined();
    });
  });
});
