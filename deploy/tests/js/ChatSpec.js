"use strict"; // Strict checking.
/* global describe, beforeEach, afterEach, it, expect, Chat, refreshpagechat */

// @ts-ignore
describe('chat', function () {
  // @ts-ignore
  describe('Introductory page testing context', function () {
    beforeEach(function () {
    });

    afterEach(function () {
    });

    it('should have initialized the Chat', function () {
      // Running the file should work without error
      expect(Chat).toBeDefined();
      expect(Chat.config).toBeDefined();
    });
    it('should have helper functions', function () {
      // @ts-ignore
      expect(refreshpagechat).toBeDefined();
    });
    it('should be able to run helper functions', function () {
      // expect(refreshpagechat()).toBe(true); // Refreshes the page, which breaks tests
    });
    it('should have methods defined', function () {
      expect(Chat.getExistingChatMessages).toBeDefined();
    });
    it('should be able to call initializations', function () {
      expect(Chat.chatReady()).toBe(true);
      expect(Chat.canSend()).toBe(false); // Housing element will be unavailable
    });
    // Test the domain parsing
    describe('Chat domain parsing', function () {
      it('should be able to get the chat domain', function () {
        expect(Chat.domain('https://www.ninjawars.net')).toEqual('chatapi.ninjawars.net');
        expect(Chat.domain('https://invaliddomainnamehere.com')).toEqual('chatapi.ninjawars.net');
        expect(Chat.domain('https://localhost:2345/wark/zig')).toEqual('localhost');
        expect(Chat.domain('https://localhost:8765/#!/list')).toEqual('localhost');
      });
    });
  });
});