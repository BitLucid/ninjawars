/**
 * General behaviors for the signup page.
 */
// @ts-check

// eslint-disable-next-line no-var
// const { location: tLocation, top: tTop } = window;
// const { location: tFrameLocation } = tTop || {};
const { log } = console || {};

/**
 * Executions on the signup page.
 */
(() => {
  log('iife run on signup.js');
  // // eslint-disable-next-line eqeqeq
  // if (tLocation != tFrameLocation) { // Framebreak on the signup page as well.
  //   if (window.top && window.top.location && window.top.location.href) {
  //     window.top.location.href = document.location.href;
  //   }
  // }
  $( // on document ready
    () => {
      // Click the button to reveal the signup form button
      const become = $('#become-a-ninja');
      const reveal = $('#reveal-signup');
      become.hide();
      reveal.show();
      reveal.on('click', () => {
        log('reveal-signup click');
        reveal.hide('slow');
        become.show('slow');
      });
    },
  );
})();
