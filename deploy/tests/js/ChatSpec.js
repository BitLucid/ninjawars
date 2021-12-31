// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, Chat, refreshpagechat */

// @ts-ignore
describe('chat', () => {
  // @ts-ignore
  describe('Introductory page testing context', () => {
    beforeEach(() => { });

    afterEach(() => { });

    it('should have initialized the Chat', () => {
      // Running the file should work without error
      expect(Chat).toBeDefined();
      expect(Chat.config).toBeDefined();
    });
    it('should have helper functions', () => {
      // @ts-ignore
      expect(refreshpagechat).toBeDefined();
    });
    it('should be able to run helper functions', () => {
      // expect(refreshpagechat()).toBe(true); // Refreshes the page, which breaks tests
    });
    it('should have methods defined', () => {
      expect(Chat.getExistingChatMessages).toBeDefined();
    });
    it('should be able to call initializations', () => {
      expect(Chat.chatReady()).toBe(true);
      expect(Chat.canSend()).toBe(false); // Housing element will be unavailable
    });
    // Test the domain parsing
    describe('Chat domain parsing', () => {
      it('should be able to get the chat domain', () => {
        expect(Chat.domain('https://www.ninjawars.net')).toEqual(
          'chatapi.ninjawars.net',
        );
        expect(
          Chat.domain('https://invaliddomainnamehere.com'),
        ).toEqual('chatapi.ninjawars.net');
        expect(Chat.domain('https://localhost:2345/wark/zig')).toEqual(
          'localhost',
          'Chat domain test did not allow localhost',
        );
        expect(Chat.domain('https://localhost:8765/#!/list')).toEqual(
          'localhost',
          'Chat domain test did not allow custom localhost',
        );
      });
    });
  });
});
