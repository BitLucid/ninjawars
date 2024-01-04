<link href='/css/epics.css' rel='stylesheet' type='text/css' />
<script src='/js/epics.js'></script>
<main id='epics'>
    <nav style='float:left'><span>
            <a href='/'><button type='button' class='btn btn-default'><i class='fa fa-home'></i></button></a>
        </span><span>
            <a href='/ninjamaster'><button type='button' class='btn btn-default'><i
                        class='fa fa-arrow-left'></i></button></a>
        </span>
    </nav>

    <header>
        <h1>UI Story Epics</h1>

        {if $error}
            <div class='parent'>
                <div class='child error'>
                    {$error|escape}
                </div>
            </div>
        {/if}

        <div class='expose-area-error error glassbox' style='display:none'>
            Area not found to display!
        </div>

        <nav class='nav parent' id='sections-control'>
            <div class='child'>
                <a class='btn btn-info' href='#staging-section'>Staging</a>
                <a class='btn btn-info' href='#intro-section'>Intro</a>
                <a class='btn btn-info' href='#intro-small-section'>Intro - Small</a>
                <a class='btn btn-info' href='#login-section'>Login</a>
                <a class='btn btn-info' href='#signup-section'>Signup</a>
                <a class='btn btn-info' href='#profile-section'>Profile</a>
                <a class='btn btn-info' href='#npcs-section'>Npcs</a>
                <a class='btn btn-info' href='#npcs-abstract-section'>Npcs: Abstract</a>
                <a class='btn btn-info' href='#npcs-oni-section'>Npcs: Oni</a>
                <a class='btn btn-info' href='#npcs-theif-group-section'>Npcs: Thief Group</a>
                <a class='btn btn-info' href='#aside-section'>Aside</a>
                <a class='btn btn-info' href='#chat-section'>Chat</a>
                <a class='btn btn-info' href='#clan-section'>Clan</a>
                <a class='btn btn-info' href='#healthbar-section'>Healthbar</a>
                <a class='btn btn-info' href='#typography-section'>Typography</a>
                <a class='btn btn-info' href='#staff-section'>Staff</a>
                <a class='btn btn-info' href='#about-section'>About</a>
                <a class='btn btn-info' href='#errors-section'>Errors</a>
                <a class='btn btn-info' href='#events-section'>Events</a>
                <a class='btn btn-info' href='#single-event-section'>Single Event</a>
                <a class='btn btn-info' href='#dojo-section'>Dojo</a>
                <a class='btn btn-info' href='#dojo-scroll-section'>Dojo.Scroll</a>
                <a class='btn btn-info' href='#casino-section'>Casino</a>
                <a class='btn btn-info' href='#bath-house-section'>Bath House</a>
                <a class='btn btn-info' href='#field-section'>Field</a>
                <a class='btn btn-info' href='#shop-section'>Shop</a>
                <a class='btn btn-info' href='#shop-items-section'>Shop.Items</a>
                <a class='btn btn-info' href='#shop-buy-section'>Shop.Buy</a>
                <a class='btn btn-info' href='#shrine-section'>Shrine</a>
                <a class='btn btn-info' href='#map-section'>Map</a>
                <a class='btn btn-info' href='#nodes-section'>Nodes</a>
                <a class='btn btn-info' href='#list-section'>List</a>
                <a class='btn btn-info' href='#fight-section'>Fight</a>
                <a class='btn btn-info' href='#wip-fight-section'>WIP Fight</a>
                <a class='btn btn-info' href='#footer'>Footer</a>
                <a class='btn btn-info' href='#ninjamaster-section'>Ninjamaster</a>
            </div>
        </nav>
    </header>

    <section id='stories' style='min-height:30vh'>

        <section id='staging-section'>
            <h2>Staging</h2>
            {assign var="example" value="1"}
            {include file="staging.tpl"}
        </section>

        <section id='wip-fight-section'>
            <h2>WIP New Fight</h2>
            {include file="staging.fight.tpl"}
        </section>

        <section id='signup-section'>
            <h2>Signup</h2>
            {assign var="classes" value=['dragon'=>['name'=>'dragon','expertise'=>'healing']]}
            {assign var="submit_successful" value="0"}
            {assign var="signupRequest" value=$signupRequest}
            {assign var="error" value="Some error string for signup"}
            {assign var="submitted" value=false}
            {assign var="class_display" value="Some Class Here"}
            {assign var="completedPhase" value="2"}
            {include file="signup.tpl"}
        </section>

        <section id='login-section'>
            <h2>Login</h2>
            {assign var="login_error_message" value="Some error string for login"}
            {assign var="authenticated" value="0"}
            <div class='login-page'>
                {include file="login.tpl"}
            </div>
        </section>

        <section id='profile-section'>
            <h2>Profile Pieces</h2>
            {* ninja and cln are assigned in the controller *}
            <div style='display:flex;justify-content:center;' class='pop'>
                <div style='width:50%;'>
                    {include file="selfmenu.partial.tpl"}
                </div>
            </div>
        </section>

        <section id='shrine-section'>
            <h2>Shrine</h2>
            {assign var="error" value=''}
            {assign var="player" value=$char}
            {assign var="freeResurrection" value=true}
            {assign var="has_chi" value=true}
            {assign var="shrineSections" value=['entrance', 'result-resurrect', 'result-heal', 'form-heal','reminder-full-hp','form-cure','form-resurrect','reminder-resurrect-cost']}
            {include file="shrine.tpl"}
        </section>

        <section id='npcs-section'>
            <h2>Npcs</h2>
            {include file="npc.list.tpl"}
        </section>


        <section id='npcs-oni-section'>
            <h2>Npcs: Oni</h2>
            {assign var="npco" value=$npco}
            {assign var="player" value=$char}
            {assign var="item" value=$item}
            {assign var="victory" value=true}
            {assign var="multiple_rewards" value=true}
            {assign var="oni_health_loss" value=9999}
            {assign var="oni_killed" value=true}
            {include file="npc.oni.tpl"}
        </section>


        <section id='npcs-theif-group-section'>
            <h2>Npcs: Theif Group</h2>
            {assign var="npco" value=$npco}
            {assign var="victory" value=true}
            {assign var="gold" value=44444}
            {assign var="attack" value=22222}
            {assign var="powerful_attack" value=true}
            {include file="npc.thief-group.tpl"}
        </section>

                        {*

        <section id='npcs-abstract-section'>
            <h2>Npcs: Abstract</h2>
            {assign var="npco" value=$npco}
            {assign var="image_path" value='/images/characters/firefly.jpg'}
            {assign var="is_quick" value=true}
            {assign var="race" value='OniMonster'}
            {assign var="display_name" value='OniOrWhatever'}
            {assign var="tagline" value='Some cool npc tagline'}
            {assign var="much_stronger" value=true}
            {assign var="is_weaker" value=true}
            {assign var="kill_npc" value=true}
            {assign var="is_villager" value=true}
            {assign var="survive_fight" value=true}
            {assign var="attack_damage" value=99999}
            {assign var="received_gold" value=33333}
            {assign var="added_bounty" value=4444}
            {assign var="ninja_damage" value=7777}
            {assign var="ninja_damage_class" value='nick'}
            {assign var="display_statuses" value='poisoned, frozen, dead, healthy'}
            {assign var="display_statuses_classes" value='poisoned frozen dead healthy'}
            {assign var="received_display_items" value=['shuriken', 'fireorsomething', 'stick', 'brick']}
            {assign var="npc_stats" value=['short'=>'A short description of the npc here']}
            {assign var="player" value=$char}
            {include file="npc.abstract.tpl"}
        </section>



        *}

        <section id='intro-section'>
            <h2>Intro</h2>
            {include file="intro.tpl"}
        </section>

        <section id='intro-small-section'>
            <h2>Intro Width Constrained</h2>
            <div style='width:300px;margin:auto;'>
                {include file="intro.tpl"}
            </div>
        </section>

        <section id='aside-section'>
            <h2>Aside</h2>
            {include file="aside.tpl"}
        </section>

        <section id='chat-section'>
            <h2>Chat</h2>
            {include file="mini-chat.section.tpl"}
        </section>

        <section id='clan-section'>
            <h2>Clan</h2>
            {assign var="error" value='Some clan error'}
            {assign var="pageSections" value=['info', 'member-list', 'list']}
            {include file="clan.tpl"}
        </section>

        <section id='healthbar-section'>
            <h2>Healthbar</h2>
            {assign var="health" value="0"}
            {assign var="level" value="30"}
            {include file="health_bar.tpl"}
            {assign var="health" value="1"}
            {assign var="level" value="30"}
            {include file="health_bar.tpl"}
            {assign var="health" value="5"}
            {assign var="level" value="30"}
            {include file="health_bar.tpl"}
            {assign var="health" value="55"}
            {assign var="level" value="30"}
            {include file="health_bar.tpl"}
            {assign var="health" value="100"}
            {assign var="level" value="30"}
            {include file="health_bar.tpl"}
            {assign var="health" value="300"}
            {assign var="level" value="30"}
            {include file="health_bar.tpl"}
            {assign var="health" value="390"}
            {assign var="level" value="30"}
            {include file="health_bar.tpl"}
            {assign var="health" value="999"}
            {assign var="level" value="30"}
            {include file="health_bar.tpl"}
        </section>

        <section id='typography-section'>
            <h2>Typography</h2>
            {include file="typography.section.tpl"}
        </section>

        <section id='staff-section'>
            <h2>Staff</h2>
            {include file="staff.tpl"}
        </section>

        <section id='errors-section'>
            <h2>Errors</h2>
            {assign var="error_type" value="frozen"}
            {include file="error.dead.tpl"}
            {include file="error.default.tpl"}
            {include file="error.frozen.tpl"}
            {include file="error.log_in.tpl"}
            {include file="error.tpl"}
        </section>

        {*
        404 bleeds it's page body, so can't have that currently
        <section id='404-section'>
            <h2>404</h2>
            {include file="404.tpl"}
        </section>
        *}

        <section id='events-section'>
            <h2>Events</h2>
            {assign var="events" value=[]}
            {include file="events.tpl"}
        </section>

        <section id='single-event-section'>
            <h2>Single Event</h2>
            {assign var="event" value=['send_from'=>'55555', 'from'=>'Example sender', 'unread'=>true, 'message'=>'Example Message', 'date'=>'']}
            {include file="event.single.tpl"}
        </section>

        <section id='shop-buy-section'>
            <h2>Shop.Buy</h2>
            {assign var="authenticated" value=true}
            {assign var="gold" value=88888888}
            {assign var="valid" value=true}
            {assign var="quantity" value=8888888}
            {assign var="item_text" value='Some example item'}
            {include file="shop.buy.tpl"}
        </section>

        <section id='shop-section'>
            <h2>Shop</h2>
            {assign var="valid" value=true}
            {assign var="quantity" value=8888888}
            {assign var="item_text" value='Some example item'}
            {assign var="gold" value=999999}
            {assign var="shopSections" value=['index', 'buy']}
            {include file="shop.tpl"}
        </section>

        <section id='shop-items-section'>
            <h2>Shop.Items</h2>
            {* item_costs is set in the controller *}
            {assign var="gold" value=88888888}
            {assign var="item_costs" value=$full_item_costs}
            {include file="shop.items.tpl"}
        </section>


        <section id='dojo-section'>
            <h2>Dojo</h2>
            {assign var="player" value=$char}
            {assign var="error" value="Some error string for dojo"}
            {assign var="dojoSections" value=['access-denied', 'form-class-change', 'form-dim-mak', 'reminder-class-change', 'reminder-class', 'reminder-dim-mak', 'reminder-level', 'reminder-next-level', 'scroll', 'success-class-change', 'success-dim-mak']}
            {include file="dojo.tpl"}
        </section>

        <section id='dojo-scroll-section'>
            <h2>Dojo Scroll</h2>
            {assign var="player" value=$char}
            {assign var="error" value="Some error string for dojo"}
            {include file="dojo.scroll.tpl"}
        </section>


        <section id='casino-section'>
            <h2>Casino</h2>
            {assign var="player" value=$char}
            {assign var="error" value="Some error string for casino"}
            {assign var="pageParts" value=[]}
            {include file="casino.tpl"}
        </section>

        {* Field is below *}

        <section id='bath-house-section'>
            <h2>Bath House</h2>
            {assign var="player" value=$char}
            {assign var="error" value="Some error string for bath house/duel"}
            {assign var="duels" value=[]}
            {assign var="vicious_killer" value='SomeViciousKiller'}
            {assign var="player_count" value=9999}
            {assign var="rich_haul" value=77777}
            {assign var="recently_active" value=23232323}
            {include file="duel.tpl"}
        </section>

        <section id='work-section'>
            <h2>Work</h2>
            {assign var="player" value=$char}
            {assign var="error" value="Some error string for work"}
            {assign var="work_multiplier" value="10"}
            {assign var="not_enough_energy" value="1"}
            {assign var="earned_gold" value="55"}
            {assign var="worked" value="5"}
            {assign var="authenticated" value="0"}
            {assign var="recommended_to_work" value=555}
            {include file="work.tpl"}
        </section>

        <section id='map-section'>
            <h2>Map</h2>
            {* Nodes assigned in controller *}
            {assign var="show_ad" value="1"}
            {include file="map.tpl"}
        </section>

        <section id='nodes-section'>
            <h2>Nodes</h2>
            {* Nodes assigned in controller *}
            {include file="nodes.tpl"}
        </section>

        {* list is complicated, so putting that to the end *}
        <section id='list-section'>
            <h2>List</h2>
            {*  include file="list.tpl" *}
        </section>

        <section id='field-section'>
            <h2>Field</h2>
            {assign var="work_multiplier" value="10"}
            {assign var="not_enough_energy" value="1"}
            {assign var="earned_gold" value="55"}
            {assign var="worked" value="5"}
            {assign var="authenticated" value="0"}
            {assign var="recommended_to_work" value=555}
            {include file="work.tpl"}
        </section>

        <section id='fight-section'>
            <h2>Fight - Attack Next</h2>
            {assign var="player" value=$char}
            {include file="enemies.attack-next.tpl"}
        </section>

        <section id='footer'>
            <h2>Footer</h2>
            {include file="footer.tpl"}
        </section>

        <section id='ninjamaster-section'>
            <h2>Ninjamaster</h2>
            <p>Currently commented out</p>
            {* {include file="ninjamaster.tpl"} *}
        </section>

        <section id='about-section'>
        
            <h2>About</h2>
            <p>Special case to avoid the base tag used in about</p>
            <iframe frameborder='0' id='epics-iframe' name='epics-iframe' src='/about'>
            </iframe>
        </section>

        <footer style='border-top:thick dashed white;margin-top:3rem;'>
            <h6>The end</h6>
        </footer>
    </section>
    {* js script at the top to prevent breaking from templates *}

</main>




