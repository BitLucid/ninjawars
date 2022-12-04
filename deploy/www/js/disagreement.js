/* Simple defaults for the player and fight pages, attacking_possible (boolean)
 * and combatSkillsList (json array) are rendered by the server and passed in */
/* jshint browser: true, white: true, plusplus: true */
/* global NW, attacking_possible
 */

/* eslint max-statements: "Warn" */
/* eslint max-lines: "Warn" */
/* eslint max-lines-per-function: "Warn" */

// @ts-check

const initialCombatConfig = {
  blaze: false,
  deflect: false,
  evasion: false,
  duel: false,
};

// Helper functions
const getD = (key) => window && window.localStorage && window.localStorage.getItem(key);
const setD = (key, value) => window
  && window.localStorage && window.localStorage.setItem(key, value);
const saveD = (key, val) => setD(key, JSON.stringify(val));
const obtainD = (key) => {
  const intermediary = getD(key);
  return intermediary ? JSON.parse(intermediary) : undefined;
};

const isChecked = (key) => Boolean($(`#${key}`).prop('checked'));
const setChecked = (key, bol) => $(`#${key}`).prop('checked', bol);

const kickClanMember = () => window
  // eslint-disable-next-line no-alert
  && window.confirm('Are you sure you want to kick this player?');

// eslint-disable-next-line max-statements, max-lines-per-function
$(() => {
  console.info('Checking for saved combat configurations.');
  //  Pull var as defined in external template
  // @ts-ignore
  // eslint-disable-next-line camelcase
  const attackable = typeof attacking_possible !== 'undefined' ? attacking_possible : false;
  console.info(
    attackable ? 'Attacking enabled.' : 'No attacking this target',
  );
  // The player field has a kick button option for clan leaders
  $('#kick_form').on(
    'submit',
    kickClanMember,
  );

  if (attackable) {
    /*
       because some browsers store all values as strings, we need to store
       booleans as string representations of 1 and 0. We then need to get
       the int value upon retrieval
       Perhaps no longer necessary? -- RR Dec 4 2022
    */

    const combatKey = 'combat-config';

    const storedCombatConfig = obtainD(combatKey);
    const finalConfig = {
      ...initialCombatConfig,
      ...storedCombatConfig,
    };
    setChecked('blaze', finalConfig.blaze);
    setChecked('deflect', finalConfig.deflect);
    setChecked('evasion', finalConfig.evasion);
    setChecked('duel', finalConfig.duel);

    // Duel is a special case, non-skill combat choice

    $('#attack_player').on('submit', () => {
      const submitFinalConfig = {
        ...initialCombatConfig,
        blaze: isChecked('blaze'),
        deflect: isChecked('deflect'),
        evasion: isChecked('evasion'),
        duel: isChecked('duel'),
      };
      saveD(combatKey, submitFinalConfig);

      return true;
    });
  }

  // Cache and de-cache a favorite item to fight with
  const lastItemUsed = NW.storage.appState.get('last_item_used');
  if ($(`#item option[value='${lastItemUsed}']`).length) {
    $('#item').val(lastItemUsed);
  } else {
    $('#item').val($('#item option:first-child').val() || '');
  }

  $('#inventory_form').on('submit', () => {
    setD('last_item_used', $('#item').val());
  });
});
