{**
 * Shows a table of attacker/target stats for use before combat
 *}
<style>
.versus-start{
 width: 90%;
 margin: auto;
 display: flex;
 align-items: center;
 align-content: space-between;
}
.versus-start .combatant{
  display: inline-block;
  width: 45%;
}
.versus-start .joiner{
  display: inline-block;
  width: 10%;
}
</style>

 <section class='versus-start'>
  <div class='combatant'>
    <span class='player-name'>{$attacker->name()|escape}</span> <em class='de-em'>(strength {$attacker->getStrength()|escape})</em>
    <div>
      <span class='combat-health'>{include file="health_bar.tpl" health=$attacker->health level=$attacker->level}</span>
    </div>
  </div>
  <div class='joiner'>
  <strong>VS</strong>
  </div>
  <div class='combatant'>
    <span class='player-name'>{$target->name()|escape}</span> <em class='de-em'>(strength {$target->getStrength()|escape})</em>
    <div>
      <span class='combat-health'>{include file="health_bar.tpl" health=$target->health level=$target->level}</span>
    </div>
  </div>
 </section>
