describe('Array', function() {
  describe('#indexOf()', function() {
    it('should return -1 when the value is not present', function() {
      var res = [1,2,3].indexOf(5);
      expect(res).toEqual(-1);
      expect([1,2,3].indexOf(0)).toEqual(-1);
      expect([1,2,3].indexOf(3)).toEqual(2);
    });
  });
});