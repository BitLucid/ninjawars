/* Assist in clan operations (currently only clan leave) */

// @ts-check

const clan = {
  leave: () => {
    const navig = window && window.location && window.location.href ? (url) => { window.location.href = url; } : (url) => { console.info('Unable to navigate to url:', url); };
    if (window && window.confirm('Do you really want to exit the clan?')) {
      navig('/clan/leave');
    }

    return false;
  },
};

$(() => {
  $('#leave-clan').click(() => {
    clan.leave();
    return false;
  });
});
