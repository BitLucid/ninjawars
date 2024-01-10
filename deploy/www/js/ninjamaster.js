/* eslint-disable func-names */
/* eslint-disable prefer-arrow-callback */
/* Functions for ninjamaster */

// eslint-disable-next-line import/extensions
import api from './api.js';

// eslint-disable-next-line no-var
var presence = window.presence || {};
presence.ninjamaster = true;

const escape = (unsafe) => {
  const div = document.createElement('div');
  div.innerText = unsafe ?? '';
  return div.innerHTML;
};

const clanComponent = ({ clan }) => `
        <li class='card'>
          <div class='glassbox'>
            ${clan.clan_name} 
            ${clan.clan_id} 
            <time class='timeago' datetime='${escape(clan.clan_created_date)}'>${escape(clan.clan_created_date)}</time>
            <a href='${escape(clan.clan_avatar_url ?? '')}'>Avatar</a>
            <figure>
              <img style='max-height:35rem;max-width:35rem' src='${escape(clan.clan_avatar_url)}' alt='' />
              <figcaption>Clan Avatar: ${clan.clan_avatar_url ? '' : 'None'}</figcaption>
            </figure>
            Founded by ${escape(clan.clan_founder)}
            <a href='/clan/view?clan_id=${escape(clan.clan_id)}'><i class='fa-solid fa-eye'></i> Clan Page</a>
            <details>
              <summary>Summary</summary>
              ${escape(JSON.stringify(clan))}
            </details>
          </div>
        </li>
`;

function sleeper(ms) {
  return function (x) {
    return new Promise((resolve) => {
      setTimeout(() => resolve(x), ms);
    });
  };
}

const initializeClans = () => {
  $('#clan-list-progress').hide();
  $('#load-clans').on('click', () => {
    $('#clan-list-progress').show();
    api.clans().then(sleeper(1000)).then((response) => {
      response.json().then((data) => {
        // loop over the clan list and add the data of each clan to it
        data.clans.forEach((clan) => {
          $('#clan-list-area').append(clanComponent({ clan }));
        });
        $('#clan-list-progress').hide();
        $('#load-clans').hide();
      });
    });
  });
};

const initializeDeactivation = () => {
  $('#start-deactivate').on('click', () => {
    $('#start-deactivate').hide();
    $('#deactivate-character').fadeIn('slow');
  });

  // So that we can turn off problematic characters
  $('#deactivate-character').on('click', function () {
    const charId = $(this).data('char-id');
    api.deactivateChar(charId).then(() => {
      // eslint-disable-next-line no-unused-expressions
      window?.location?.reload();
    });
  });

  // Batch deactivations
  $('button.deactivate-character').on('click', function () {
    const refer = $(this);
    const charId = $(this).data('char-id');
    api.deactivateChar(charId).then(() => {
      // eslint-disable-next-line no-unused-expressions
      refer.parent().hide();
    });
  });

  // Turn back on So that we can turn off problematic characters
  $('#reactivate-character').on('click', function () {
    const charId = $(this).data('char-id');
    api.reactivateChar(charId).then(() => {
      // eslint-disable-next-line no-unused-expressions
      window?.location?.reload();
    });
  });
};

$(function initializeNMPage() {
  // Handle the show/hide sections
  $('.show-hide-next')
    .click(function showHideNext() {
      $(this).parent().next().slideToggle();
    })
    .html("<span class='slider'><span class='dot'></span></span>");
  $('.show-hide-next').parent().next().toggle();

  initializeClans();

  initializeDeactivation();
});
