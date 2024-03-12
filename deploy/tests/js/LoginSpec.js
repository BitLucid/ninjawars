// Strict checking.
/* global describe, it, expect */

describe('login', () => {
  describe('Check that the login script runs/ran', () => {
    it('should have initialized the login functionality', () => {
      // Running the file should work without error
      expect(loginFinalized).toBeTruthy();
    });
  });
});
