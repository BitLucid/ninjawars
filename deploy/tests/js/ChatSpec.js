/* eslint-disable max-lines-per-function */
// Strict checking.
/* global describe, beforeEach, afterEach, it, expect, Chat, refreshpagechat */

describe('chat', () => {
  const previousConfig = Chat.config;
  describe('Introductory page testing context', () => {
    beforeEach(() => {
      const exampleUrl = 'https://www.example.com';
      Chat.config = Chat.setConfig(exampleUrl, '9999');
    });

    afterEach(() => {
      Chat.config = previousConfig;
    });

    it('should have initialized the Chat', () => {
      // Running the file should work without error
      expect(Chat).toBeDefined();
      expect(Chat.config).toBeDefined(
        'Chat.config was found to be undefined in tests',
      );
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
        console.info('checking https://www.ninjawars.net');
        expect(Chat.domain('https://www.ninjawars.net')).toEqual(
          'chatapi.ninjawars.net',
        );
        console.info('checking https://www.ninjawars.net');
        expect(
          Chat.domain('https://invaliddomainnamehere.com'),
        ).toEqual('chatapi.ninjawars.net');
        expect(Chat.domain('https://localhost:2345/wark/zig')).toEqual(
          'localhost',
          'Chat domain test did not allow localhost',
        );
        console.info('checking https://localhost:8765/#!/list');
        expect(Chat.domain('https://localhost:8765/#!/list')).toEqual(
          'localhost',
          'Chat domain test did not allow custom localhost',
        );
      });
    });
  });
});
