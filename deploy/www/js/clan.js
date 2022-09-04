/* Assist in clan operations (currently only clan leave) */
// jquery vars are globally available

const clan = {
  leave: () => {
    if (window && window.confirm('Do you really want to exit the clan?')) {
      window.location = '/clan/leave';
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
