// Strict checking.
/* global describe, beforeEach, afterEach, xit, expect, apiMethods */

// @ts-ignore
describe('talk', () => {
  // @ts-ignore
  describe('Introductory talk script testing context', () => {
    beforeEach(() => {});

    afterEach(() => {});

    xit('should have initialized the talk functionality', () => {
      // Running the file should work without error
      expect(apiMethods).toBeDefined();
    });

    xit('should be able to run api.whatever', () => {
      const output = apiMethods.whatever();
      expect(output).toBeTruthy();
    });
  });
});
