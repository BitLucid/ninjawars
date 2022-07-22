/* Simple defaults for the casino page, attacking_possible (boolean)
 * and combatSkillsList (json array) are rendered by the server and passed in */
/* jshint browser: true, white: true, plusplus: true */
/* global $, NW, attacking_possible, combatSkillsList */
$(() => {
  //  Pull var as defined in external template
  // eslint-disable-next-line camelcase
  const attackable = typeof attacking_possible !== 'undefined' ? attacking_possible : false;
  console.info(
    attackable ? 'Attacking enabled.' : 'No attacking this target',
  );
  // eslint-disable-next-line no-alert
  $('#kick_form').submit(
    () => window
            && window.confirm('Are you sure you want to kick this player?'),
  );

  /*
       because some browsers store all values as strings, we need to store
       booleans as string representations of 1 and 0. We then need to get
       the int value upon retrieval
    */
  if (attackable) {
    if (
      undefined === combatSkillsList
            || !Array.isArray(combatSkillsList)
    ) {
      console.warn(
        'Combat_skills settings were not in proper array format',
      );
    }
    console.info('combat skills', combatSkillsList);
    // Duel is a special case, non-skill combat choice
    $('#duel').prop(
      'checked',
      parseInt(NW.storage.appState.get('duel_checked', false), 10),
    );
    $.each(combatSkillsList, (i, skill) => {
      const checkedOrNot = parseInt(
        NW.storage.appState.get(
          `${skill.skill_internal_name}_checked`,
          false,
        ),
        10,
      );
      $(`#${skill.skill_internal_name}`).prop('checked', checkedOrNot);
    });

    $('#attack_player').submit(() => {
      // the unary + operator converts the boolean to an int
      NW.storage.appState.set(
        'duel_checked',
        +$('#duel').prop('checked'),
      ); // Duel is special case
      $.each(combatSkillsList, (i, skill) => {
        NW.storage.appState.set(
          `${skill.skill_internal_name}_checked`,
          +$(`#${skill.skill_internal_name}`).prop('checked'),
        );
      });

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

  $('#inventory_form').submit(() => {
    NW.storage.appState.set('last_item_used', $('#item').val());
  });
});
