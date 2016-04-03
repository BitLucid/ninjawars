        {if $ninja->id()}
        <nav class='ninja-popup-profile parent'>
          <div class='ninja-menu-area-interior child'>
            <div class='ninja-name'>
              <a target="main" href="/player?player_id={$ninja->id()|escape:'url'|escape}" title='Display your ninja information'>
                <strong class='char-name'>{$ninja->name()|escape}</strong>
              </a>
            </div>
            <div class='ninja-level'>
              Level {$ninja->level|escape}
            </div>
            {if $ninja->level < 5 || $ninja->isAdmin()}
            <div id='helpful-info'>
              <a target='main' href='/intro'><i class="fa fa-question-circle"></i> Helpful Info</a>
            </div>
            {/if}
          </div>
        </nav>
        <div class='ninja-stats-link thick'>
          <a href='/stats' target='main' title='Your ninja stats, level, info, etc.'><i class="fa fa-heart"></i> Ninja Stats</a>
        </div>
        {/if}
        <div class='account-info thick'>
          <a href="/account" target="main" title='Your player account info, email, password, etc.'><i class="fa fa-gear"></i> Account Info</a>
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
