// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, clan */

describe('clan', () => {
  describe('Introductory script testing context', () => {
    beforeEach(() => { });

    afterEach(() => { });

    it('should have initialized the clan functionality', () => {
      // Running the file should work without error
      expect(clan).toBeDefined();
      expect(clan.leave).toBeDefined();
    });

    it('should be able to run clan.leave()', () => {
      const output = clan.leave();
      expect(output).toBeFalsy();
    });
  });
});
