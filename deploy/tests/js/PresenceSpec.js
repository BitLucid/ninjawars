// Strict checking.
/* global describe, it, expect, presence, loadLastCommitMessage, charSuggest */

describe('check presence of scripts', () => {
  describe('Check whether scripts have run and left their presence', () => {
    it('should have left behind it\'s presence', () => {
      expect(presence).toBeDefined();
      expect(presence.homepage).toBeDefined();
      expect(presence.intro).toBeDefined();
      expect(presence.shop).toBeDefined();
      expect(presence.passwords).toBeDefined();
      expect(loadLastCommitMessage).toBeDefined(); // repo.js
      expect(presence.epics).toBeDefined();
      expect(presence.casino).toBeDefined();
      // expect(presence.utils).toBeDefined(); //not sure how to test these with their exports
      // expect(presence.api).toBeDefined();
      // Clan and chat handled in their own spec file
      expect(presence.stats).toBeDefined();
      expect(charSuggest).toBeDefined();
    });
  });
});
