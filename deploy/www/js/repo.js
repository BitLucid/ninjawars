/* Display last repo commit. */
/* jshint browser: true, white: true, plusplus: true */
/* global $ */
function loadLastCommitMessage() {
  const logger = console || {
    warn: () => {
      /* noop */
    },
  };
  const owner = 'BitLucid';
  const repo = 'ninjawars';
  const oauthToken = ''; // TODO: Figure out how to store this info here.
  const access = oauthToken ? `access_token=${oauthToken}&` : '';
  const githubUrl = `https://api.github.com/repos/${owner}/${repo}/commits/HEAD?${access}callback=?`;
  const placeCommit = function fnPlaceCommit(data) {
    if (!data.data || !data.data.commit) {
      logger.warn('No github commit api data:', data);
      return;
    }
    const { message, author } = data.data.commit;
    const mess = message
      && message.replace(/Merge pull request #....{1,2} from BitLucid\//g, '');
    // Load latest commit message.
    $('#latest-commit-section')
      .find('#latest-commit')
      .html(mess)
      .append(`<div id='commit-author'>--${author.name}</div>`)
      .show()
      .end()
      .find('#latest-commit-title')
      .show();
  };

  // https://api.github.com/repos/BitLucid/ninjawars/commits/HEAD
  $.getJSON(githubUrl, placeCommit);
}

$(document).ready(() => {
  loadLastCommitMessage(); // To display commits on the main page.
});
