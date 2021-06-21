'use strict'; // Strict checking.

describe('intro', function () {
    describe('Introductory page testing context', function () {
        beforeEach(function () {
            //var document = {getElementById:function(whatever){return {style:{visibility:true}}}};
            //console.log(document.getElementById('blah').style.visibility);
            var document = {};
            var dummyElement = { style: { visibility: 'visible' } };
            document.getElementById = jasmine
                .createSpy('HTML Element')
                .and.returnValue(dummyElement);
        });

        afterEach(function () {});

        it('should be able to initialize without failing', function () {
            // Running the file should work without error
        });
        it('should reach the end of the intro js', function () {
            //expect(reached).toBeDefined();
        });
    });
});
