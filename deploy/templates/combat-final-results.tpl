{**
 * Shows a table of attacker/target stats for use after combat
 *}
<style>
.combat-results .combat-health {
  min-width: 13rem;
}
.combat-results .low-health {
  color:red;
  font-weight:bold;
}
.combat-results .self-health {
  color:brown;
}
.combat-results .damage-area {
  text-align: center;
}

</style>

    <table class='combat-results' style="border: 0;">
      <tr>
        <th colspan="3">Results of the Attack</th>
      </tr>
      <tr>
        <td>Name</td>
        <td>Hurt For</td>
        <td>HP</td>
      </tr>

      <tr>
        <td>{$attacker->name()|escape}</td>
        <td class='damage-area'>
          {$starting_attacker->health - $attacker->health|escape}
        </td>
        <td class='combat-health self-health {if $low_health}low-health{/if}'>
          {include file="health_bar.tpl" health=$attacker->health level=$attacker->level}
        </td>
      </tr>
      <tr>
        <td>{$target->name()|escape}</td>
        <td class='damage-area'>{$starting_target->health - $target->health|escape}</td>
        <td  class='combat-health'>{include file="health_bar.tpl" health=$target->health level=$target->level}</td>
      </tr>
    </table>
    <hr>
