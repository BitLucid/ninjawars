{**
 * Shows a table of attacker/target stats for use after combat
 *}
<style>
.combat-results{
  width: 90%;
  margin: auto;
}
.combat-results .combat-health {
  min-width: 13rem;
  min-width: 30%;
}
.combat-results .low-health {
  color:red;
  font-weight:bold;
}
.combat-results .damage-area {
  text-align: center;
}
.comparison{
  display:flex;
  align-items: space-between;
}
.comparison .damage, .comparison .outcome{
  width: 45%;
}
.comparison .joiner{
  width: 10%;
  display: inline-block;
}

</style>

<section class='combat-results'>
  <div class='comparison'>
    <div class='damage'>You took <span class='damage-area'>{$starting_attacker->health - $attacker->health|escape}</span> damage</div>
    <div class='joiner'></div>
    <div class='damage'>{$target->name()|escape} took <span class='damage-area'>{$starting_target->health - $target->health|escape} damage</span></div>
  </div>
  <div class='comparison'>
    <div class='outcome'>
      <span class='combat-health self-health {if $low_health}low-health{/if}'>
          {include file="health_bar.tpl" health=$attacker->health level=$attacker->level}
      </span>
    </div>
    <div class='joiner'></div>
    <div class='outcome'>
      <span class='combat-health'>
        {include file="health_bar.tpl" health=$target->health level=$target->level}
      </span>
    </div>
  </div>
</section>
