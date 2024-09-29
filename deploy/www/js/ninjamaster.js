/* eslint-disable func-names */
/* eslint-disable prefer-arrow-callback */
/* Functions for ninjamaster.js */
/* eslint max-lines: ["error", 200] */

// eslint-disable-next-line import/extensions
import api from './api.js';

import {
  variantStableSeeds, seededString, seededRandom,
  seededInt, seededNinjaName,
  // eslint-disable-next-line import/extensions
} from './seededRandom.js';

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
            <h4>${clan.clan_name}</h4>
            <div>
              id: ${clan.clan_id}
              <br />
              Founded <time class='timeago' datetime='${escape(clan.clan_created_date)}'>${escape(clan.clan_created_date)}</time>
            </div>
            <div>by ${escape(clan.clan_founder)}</div>
            <a href='/clan/view?clan_id=${escape(clan.clan_id)}'><i class='fa-solid fa-eye'></i> Clan Page</a>
            <a href='${escape(clan.clan_avatar_url ?? '')}'>Avatar</a>
            <figure>
              <img style='max-height:35rem;max-width:35rem' src='${escape(clan.clan_avatar_url)}' alt='' />
              <figcaption>Clan Avatar: ${clan.clan_avatar_url ? '' : 'X'}</figcaption>
            </figure>
            <blockquote>${escape(clan.clan_description)}</blockquote>
            <details>
              <summary>Data Summary</summary>
              ${escape(JSON.stringify(clan))}
            </details>
          </div>
        </li>
`;

const genericComponent = ({ datum }) => `
        <li class='card'>
          <div class='glassbox'>
            <h4>Generic</h4>
            <div>
              <pre>
              ${escape(JSON.stringify(datum))}
              </pre>
            </div>
          </div>
        </li>
`;

// Adds a delay to a promise
function sleeper(ms) {
  return function (x) {
    return new Promise((resolve) => {
      setTimeout(() => resolve(x), ms);
    });
  };
}

const addClanCard = (clan) => {
  $('#clan-list-area').append(clanComponent({ clan }));
};

const addAuthenticationAttempt = (authen) => {
  $('#login-attempts-list-area').append('<div>added</div>');
  $('#login-attempts-list-area').append(genericComponent({ datum: authen }));
}

const clearClanCards = () => {
  $('#clan-list-area').empty();
};

const dotString = (num) => {
  let dots = '';
  for (let i = 0; i < num; i += 1) {
    // utf-8 bullet point
    dots += '\u2022';
  }
  return dots;
};

// An array of ints cached by localStorage
const variantSeeds = variantStableSeeds(100);

// Template random clan data, seed is a Math.random equivalent
const clanData = (seed, dotted = false) => ({
  clan_name: dotted ? dotString(13) : `Cln ${seededString(seed, 13, 5)}`,
  clan_id: dotted ? dotString(4) : `${1 + seededInt(seed)}`,
  clan_created_date: dotted ? `${dotString(5)} years ago` : '2020-01-01',
  clan_avatar_url: (seededRandom(seed) < 0.5 && !dotted) ? 'https://i.imgur.com/eflshHR.gif' : '',
  clan_founder: dotted ? dotString(15) : `${seededNinjaName(seed)}`,
  description: dotted ? dotString(30) : `Description ${seededString(seed, 30, 1)}`,
});

// Display seeded random clans
const provideInitialTemplate = (num = 7, seeded = true) => {
  // go through the seeds and add a clan card based on seeded data
  [...Array(num).keys()].forEach((n) => {
    addClanCard(clanData(variantSeeds[n], !seeded));
  });
};

/**
 * Initialize the CLANS list with a template for scenarios like /epics
 */
const initializeClans = () => {
  $('#clan-list-progress').hide();
  $('#load-clans').on('click', () => {
    $('#clan-list-progress').show();
    api.clans().then(sleeper(100)).then((response) => {
      clearClanCards();
      response.json().then((data) => {
        // loop over the clan list and add the data of each clan to it
        data.clans.forEach((clan) => {
          addClanCard(clan);
        });
        $('#clan-list-progress').hide();
        $('#load-clans').hide();
      });
    });
  });
  // For epics page, display usable template immediately
  if (window?.location.href.indexOf('epics') > -1) {
    provideInitialTemplate(7);
    $('#clan-list-progress').show();
  } else {
    // Provide dotted skeleton template
    provideInitialTemplate(7, false);
  }
};

/**
 * Initialize the auth attempt stats
 */
const initializeAuthAttempts = () => {
  $('#login-attempts-list-progress').hide();
  $('#load-login-attempts').on('click', () => {
    $('#login-attempts-list-progress').show();
    api.authenticationAttemptStats().then(sleeper(100)).then((response) => {
      response.json().then((data) => {
        console.error(data, data.authentication_stats);
        addAuthenticationAttempt(data);
        // loop over the clan list and add the data of each clan to it
        data['authentication_stats'].forEach((attempt) => {
          addAuthenticationAttempt(attempt);
        });
        $('#login-attempts-list-progress').hide();
        $('#load-login-attempts').hide();
      });
    });
  });
};

const performDeactivation = (charId) => api.deactivateChar(charId);

const doubleCheckDeactivation = ({ lastLogin }) => {
  if (lastLogin) {
    // eslint-disable-next-line no-alert
    const result = window.confirm('Are you sure? This character has a last_login');
    if (result === false) {
      return false;
    }
  }
  return true;
};

const initializeDeactivation = () => {
  $('#start-deactivate').on('click', () => {
    $('#start-deactivate').hide();
    $('#deactivate-character').fadeIn('slow');
  });

  // So that we can turn off problematic characters
  $('#deactivate-character').on('click', function () {
    const charId = $(this).data('char-id');
    const charqactive = $(this).data('char-last-login');
    const checked = doubleCheckDeactivation({ last_login: charqactive });
    if (checked) {
      performDeactivation(charId).then(() => {
        window?.location?.reload();
      });
    }
  });

  // Batch deactivations
  $('button.deactivate-character').on('click', function () {
    const refer = $(this);
    const charId = $(this).data('char-id');
    const charqactive = $(this).data('char-last-login');
    doubleCheckDeactivation({ lastLogin: charqactive });
    performDeactivation(charId).then(() => {
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
}; // end of deactivation section

$(function initializeNMPage() {
  // Handle the show/hide sections
  $('.show-hide-next').parent().next().toggle();
  $('.show-hide-next')
    .on('click', function showHideNext() {
      $(this).parent().next().slideToggle();
    })
    .html("<span class='slider'><span class='dot'></span></span>");

  initializeClans();
  initializeAuthAttempts();

  initializeDeactivation();
});
