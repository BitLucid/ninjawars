"use strict"; // Strict checking.
describe('Debug', function() {
  describe('dump()', function() {
    it('should return formatted var text of arguments', function() {
        var arr = {jim:'bob'};
        var res = dump(arr, 0);
        expect(res).not.toEqual('');
    });
  });
});
describe('Echo', function() {
  describe('echo()', function() {
    it('should just return a parameter passed in', function() {
        var res = testEcho('something');
        expect(res).toEqual('something');
    });
  });
});

describe('Hello world', function(){
    it('says hello', function(){
        expect(helloWorld()).toEqual('hello world');
    });
});