<div class="dropdown btn-group" role='menu'>
    <!-- Link or button to toggle dropdown -->
    <span id='index-avatar' class='dropdown-toggle inline-block' data-toggle="dropdown">
        {include file="gravatar.tpl" gurl=$ninja->avatarUrl()}
        <b class="caret"></b>
    </span>
    <ul class="dropdown-menu dropdown-inverse dropdown-menu-right avatar-dropdown" role="menu"
        aria-labelledby="index-avatar">
        <li><a class='ninja-name' target="main" href="/player?player_id={$ninja->id()|escape:'url'|escape}"
                title='Display your ninja information' tabindex="-1">
                <strong class='char-name'>{$ninja->name()|escape}</strong>
            </a></li>
        <li><span class='class-name {$ninja->theme|escape}'>{$ninja->class_name|escape}</span></li>
        <li><span class='ninja-level text-muted'>Level {$ninja->level|escape}</span></li>
        <li><span class='ninja-ki text-muted'>Ki {$ninja->ki|number_format:0|escape}</span></li>
        <li><span class='ninja-karma text-muted'>Karma {$ninja->karma|number_format:0|escape}</span></li>
        {*
        <li><span class='ninja-health text-muted' style='display:flex;justify-content:flex-start'>
                <span>Health:&nbsp;</span>
                <span class='health-bar-area' title='Max health: {$ninja->getMaxHealth()|escape}'>
                {include file="health_bar.tpl" health=$ninja->health level=$ninja->level}
                </span>
            </span>
        </li>
        <li><span class='ninja-turns text-muted'>Turns: <span class='turns-count'>{$ninja->turns|number_format:0|escape}</span></span></li>
        *}
        <li><span class='ninja-kills text-muted'>Kills <span class='kills-bar-area'>{$ninja->kills|escape}</span></span></li>
        <li><a href='/stats' target='main' title='Your ninja stats, level, info, etc.'><i class="fa fa-heart"
                    tabindex="-1"></i> Ninja Stats</a></li>
        {if $clan}
            <li><a href="/clan/view?clan_id={$clan->id|escape}" target='main' title='Your clan members and clan chat'
                    tabindex="-1"><i class='fa fa-users'></i> My Clan</a></li>
        {/if}
        <li class="divider"></li>
        {if $ninja->isAdmin()}
            <li><a href='/ninjamaster'><button class='btn btn-default' type='button'>Ninjamaster</button></a></li>    
        {/if}
        <li><a href="/account" target="main" title='Your player account info, email, password, etc.' tabindex="-1"><i
                    class="fa fa-cog"></i> Account Info</a></li>
        <li class="divider"></li>
        <li><a href="/shrine/heal_and_resurrect" target="main" title='Fully heal and resurrect if necessary'
                tabindex="-1"><i class="fa-solid fa-torii-gate"></i> Heal</a>
        <li class="divider"></li>
        <!-- Recent Events count and target will get put in here via javascript -->
        <div id='recent-events' class="boxes active" style='display:none'>
            <div>
                <a target='main' id='recent-event-attacked-by' href='/events' title='View events'>You weren't recently
                    in combat</a> with <a id='view-event-char' target='main' href='#'
                    title="View a player's profile">anyone</a>.
            </div>
        </div><!-- End of recent events -->
        <li class="divider"></li>
        <li><a target='main' href='/intro'><i class="fa fa-question-circle" tabindex="-1"></i> Intro Guide</a></li>
        <li class="logout-item">
            <form method='post' action='/logout'>
                <input type='submit' name='logout' value='Logout' class='btn btn-default'>
            </form>
        </li>
    </ul>
</div><!-- end of dropdown -->
