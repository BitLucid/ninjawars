/* Simple defaults for the player and fight pages, attacking_possible (boolean)
 * and combatSkillsList (json array) are rendered by the server and passed in */
/* jshint browser: true, white: true, plusplus: true */
/* global NW, attacking_possible, combatSkillsList
 */

/* eslint max-statements: "Warn" */
/* eslint max-lines: "Warn" */
/* eslint max-lines-per-function: "Warn" */

// @ts-check

const config = {};

const getD = (key, defaultV) => NW.storage.appState.get(key, defaultV);

const setD = (key, value) => NW.storage.appState.set(key, value);

const kickClanMember = () => window
  // eslint-disable-next-line no-alert
  && window.confirm('Are you sure you want to kick this player?');

// eslint-disable-next-line max-statements, max-lines-per-function
$(() => {
  console.info('Checking for saved combat configurations.');
  //  Pull var as defined in external template
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

  /*
       because some browsers store all values as strings, we need to store
       booleans as string representations of 1 and 0. We then need to get
       the int value upon retrieval
    */

  // @ts-ignore
  const localCombatSkills = combatSkillsList || undefined;

  if (attackable) {
    console.info('combat skills', localCombatSkills);
    if (!Array.isArray(localCombatSkills)) {
      console.warn(
        'Combat_skills settings were not in proper array format',
      );
    }
    // Duel is a special case, non-skill combat choice
    $('#duel').prop(
      'checked',
      Boolean(getD('duel_checked', false)),
    );
    $.each(
      localCombatSkills,
      (_i, skill) => {
        $(`#${skill.skill_internal_name}`).prop('checked', Boolean(getD(
          `${skill.skill_internal_name}_checked`,
          false,
        )));
      },
    );

    $('#attack_player').on('submit', () => {
      // the unary + operator converts the boolean to an int
      setD(
        'duel_checked',
        Boolean($('#duel').prop('checked')),
      ); // Duel is special case
      $.each(
        localCombatSkills,
        (i, skill) => {
          setD(
            `${skill.skill_internal_name}_checked`,
            Boolean($(`#${skill.skill_internal_name}`).prop('checked')),
          );
        },
      );

      return true;
    });
  }

  // Cache and de-cache a favorite item to fight with
  const lastItemUsed = NW.storage.appState.get('last_item_used');
  if ($(`#item option[value='${lastItemUsed}']`).length) {
    $('#item').val(lastItemUsed);
  } else {
    $('#item').val($('#item option:first-child').val());
  }

  $('#inventory_form').on('submit', () => {
    setD('last_item_used', $('#item').val());
  });
});
