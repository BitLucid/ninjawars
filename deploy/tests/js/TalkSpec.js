// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, performTalk */

// @ts-ignore
describe('talk', () => {
  // @ts-ignore
  describe('Introductory talk script testing context', () => {
    beforeEach(() => { });

    afterEach(() => { });

    it('should have initialized the talk functionality', () => {
      // Running the file should work without error
      expect(performTalk).toBeDefined();
    });

    it('should be able to run performTalk', () => {
      const output = performTalk();
      expect(output).toBeTruthy();
    });
  });
});
