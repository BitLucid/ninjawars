// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, performTalk */

describe('talk', () => {
  describe('Introductory talk script testing context', () => {
    it('should have initialized the talk functionality', () => {
      // Running the file should work without error
      expect(performTalk).toBeDefined();
    });

    it('should be able to run performTalk', () => {
      const output = performTalk();
      expect(output).toBeTrue();
    });
  });
});
