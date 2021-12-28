// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, presence */

describe('ninjamaster', () => {
  describe('Introductory ninjamaster script testing context', () => {
    beforeEach(() => { });

    afterEach(() => { });

    it('should have initialized the ninjamaster functionality', () => {
      // Running the file should work without error
      expect(presence.ninjamaster).toBeDefined();
    });
  });
});
