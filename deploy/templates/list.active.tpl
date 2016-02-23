	  <div class='active-players'>
	    <ul>
	      <li><span>Lurking ninja: </span></li>
{foreach from=$active_ninjas key="row" item="ninja"}
            <li class='active-ninja'>
                <a href='/player?target_id={$ninja.player_id|escape:'url'}'>{$ninja.uname|escape}</a>
            </li>
{/foreach}
	    </ul>
	  </div>
