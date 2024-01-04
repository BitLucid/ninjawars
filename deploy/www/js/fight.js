/* eslint-disable camelcase */
/* eslint-disable max-len */
/* eslint-disable import/extensions */

import api from './api.js';
import { logger, urlParam } from './utils.js';

const { debug, log } = logger();

// State handling for the current place
const [getOffset, setOffset] = [
  () => (localStorage && JSON.parse(localStorage.getItem('offset'))) || 0,
  (off) => localStorage
    && localStorage.setItem('offset', JSON.stringify(Math.max(0, off))),
];

const completeLoading = () => {
  $('.skeleton').addClass('off').removeClass('skeleton');
};

const placeTarget = (target) => {
  const classList = ['Crane', 'Dragon', 'Viper', 'Tiger'];
  // File ignores that these are not in camel case, since the database uses snake case
  const {
    // id: targetId,
    uname: name,
    health,
    max_health,
    level,
    class_name,
    avatar_url,
    difficulty,
    status_list,
  } = target;
  const healthPercent = Math.max(
    0,
    Math.min(100, Math.round((health / max_health) * 100)),
  );
  const areas = {
    'char-target-name': name,
    'char-numeric-health': health,
    'char-level': level,
    'char-class': class_name,
    'char-avatar': avatar_url,
    'char-difficulty': difficulty,
    'char-status': status_list,
  };
  Object.entries(areas).forEach(([keyName, info]) => {
    $(`.target-preview .${keyName}`).text(info);
  });
  $('.target-preview .char-class')
    .removeClass(classList)
    .addClass(areas['char-class']);
  $('.target-preview .char-avatar').attr('src', areas['char-avatar']);
  $('.target-preview .character-health-bar')
    .css({ width: `${healthPercent}%` })
    .html('&nbsp;');
  $('.target-preview .char-status')
    .removeClass(['Healthy', 'Dead'])
    .addClass(areas['char-status']);
};

const render = () => {
  $(() => {
    $('#add-enemy, #add-enemy a').click((e) => {
      e.preventDefault();
      e.stopPropagation();
      $('#ninja-enemy').removeClass('hidden');
    });
  });
};

$(() => {
  debug('next target IIFE run');
  const nextTarget = (offset = 0) => {
    debug('Getting next target', offset);
    return api
      .nextTarget(offset)
      .then((res) => res.json())
      .then((data) => {
        placeTarget(data);
        debug('Target health: ', data.health, data);
        completeLoading();
        return data;
      });
  };
  // Set initial offset from url, fallback to 0
  setOffset(parseInt(urlParam('offset'), 10) || 0);
  nextTarget(getOffset());
  $('.target-container .spin-enemy.up').click(() => {
    setOffset(getOffset() + 1);
    nextTarget(getOffset());
  });
  $('.target-container .spin-enemy.down').click(() => {
    setOffset(getOffset() - 1);
    nextTarget(getOffset());
  });

  // UI rendering
  render();
});
