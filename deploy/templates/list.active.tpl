	  <div class='active-players'>
	    <ul style='display:flex;justify-content:space-evenly'>
	      <li><span>Lurking ninja: </span></li>
{foreach from=$active_ninjas key="row" item="ninja"}
            <li class='active-ninja'>
                <a href='/player?player_id={$ninja.player_id|escape:'url'}'>{$ninja.uname|escape}</a>
            </li>
{/foreach}
	    </ul>
	  </div>
