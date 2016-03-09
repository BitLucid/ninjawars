        {if $ninja->id()}
        <div class='ninja-name'>
            <a target="main" href="/player?player_id={$ninja->id()|escape:'url'|escape}" title='Display your ninja information'>
              <strong class='char-name'>{$ninja->name()|escape}</strong>
            </a>
        </div>
        <div class='ninja-level'>
          Level {$ninja->level|escape}
        </div>
        {if $ninja->level < 5}
        <div id='helpful-info'>
          <a target='main' href='/intro'>Helpful Info</a>
        </div>
        {/if}
        <div class='ninja-info thick'>
          <a href='/stats' target='main' title='Your ninja stats, level, info, etc.'><img src="{cachebust file="/images/icons/mono/heart32.png"}" height="16" width="16" alt="">Ninja Stats</a>
        </div>
        {/if}
        <div class='account-info thick'>
          <a href="/account" target="main" title='Your player account info, email, password, etc.'><img src="{cachebust file="/images/icons/mono/gear32.png"}" height="16" width="16" alt="">Account Info</a>
        </div>
        <!-- Recent Events count and target will get put in here via javascript -->
        <div id='recent-events' class="boxes active" style='display:none'>
          <div>
            <a target='main' id='recent-event-attacked-by' href='/events' title='View events'>You weren't recently in combat</a> with <a id='view-event-char' target='main' href='#' title="View a player's profile">anyone</a>.
          </div>
        </div><!-- End of recent events -->
        <div class='parent'>
          <div id='logout' class='child thick'>
            <form method='post' action='/logout'>
              <input type='submit' name ='logout' value='Logout' class='btn btn-default'>
            </form>
          </div>
        </div>
