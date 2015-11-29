        {if $ninja->id()}
        <div class='ninja-name'>
            <a target="main" href="player.php?player_id={$ninja->id()|escape:'url'|escape}" title='Display your ninja information'>
              <strong class='char-name'>{$ninja->name()|escape}</strong>
            </a>
        </div>
        <div class='ninja-level'>
          Level {$ninja->level()|escape}
        </div>
        {if $ninja->level() < 5}
        <div id='helpful-info'>
          <a target='main' href='tutorial.php'>Helpful Info</a>
        </div>
        {/if}
        <div class='ninja-info thick'>
          <a href='stats.php' target='main' title='Your ninja stats, level, info, etc.'><img src="/images/icons/mono/heart32.png" height="16" width="16" alt="">Ninja Stats</a>
        </div>
        {/if}
        <div class='account-info thick'>
          <a href="account.php" target="main" title='Your player account info, email, password, etc.'><img src="/images/icons/mono/gear32.png" height="16" width="16" alt="">Account Info</a>
        </div>
        <!-- Recent Events count and target will get put in here via javascript -->
        <div id='recent-events' class="boxes active" style='display:none'>
          <div>
            <a target='main' id='recent-event-attacked-by' href='events.php' title='View events'>You weren't recently in combat</a> with <a id='view-event-char' target='main' href='#' title="View a player's profile">anyone</a>.
          </div>
        </div><!-- End of recent events -->
        <div class='parent'>
          <div id='logout' class='child thick'>
              <a href="logout.php" class='btn btn-default'>
                Logout
              </a>
          </div>
        </div>