<link href='/css/epics.css' rel='stylesheet' type='text/css' />
<script src='/js/epics.js'></script>
<script type='module' src='/js/ninjamaster.js'></script>
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
                <a class='btn btn-info' href='#logout-section'>Logout</a>
                <a class='btn btn-info' href='#signup-section'>Signup</a>
                <a class='btn btn-info' href='#profile-section'>Profile</a>
                <a class='btn btn-info' href='#npcs-section'>Npcs</a>
                <a class='btn btn-info' href='#npcs-abstract-section'>Npcs: Abstract</a>
                <a class='btn btn-info' href='#npcs-oni-section'>Npcs: Oni</a>
                <a class='btn btn-info' href='#npcs-blockers-section'>Npcs.blockers</a>
                <a class='btn btn-info' href='#npcs-theif-group-section'>Npcs: Thief Group</a>
                <a class='btn btn-info' href='#npcs-samurai-section'>Npcs.Samurai</a>
                <a class='btn btn-info' href='#chat-section'>Chat</a>
                <a class='btn btn-info' href='#aside-section'>Aside</a>
                <a class='btn btn-info' href='#clan-section'>Clan</a>
                <a class='btn btn-info' href='#clan-list-section'>Clan.list</a>
                <a class='btn btn-info' href='#clan-info-section'>Clan.info</a>
                <a class='btn btn-info' href='#clan-manage-section'>Clan.manage</a>
                <a class='btn btn-info' href='#clan-edit-section'>Clan.edit</a>
                <a class='btn btn-info' href='#healthbar-section'>Healthbar</a>
                <a class='btn btn-info' href='#typography-section'>Typography</a>
                <a class='btn btn-info' href='#staff-section'>Staff</a>
                <a class='btn btn-info' href='#about-section'>About</a>
                <a class='btn btn-info' href='#errors-section'>Errors</a>
                <a class='btn btn-info' href='#errors-dead-section'>Errors.dead</a>
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
                <a class='btn btn-info' href='#shrine-resurrect-section'>Shrine.Resurrect</a>
                <a class='btn btn-info' href='#map-section'>Map</a>
                <a class='btn btn-info' href='#nodes-section'>Nodes</a>
                <a class='btn btn-info' href='#list-section'>List</a>
                <a class='btn btn-info' href='#fight-section'>Fight</a>
                <a class='btn btn-info' href='#wip-fight-section'>WIP Fight</a>
                <a class='btn btn-info' href='#footer-section'>Footer</a>
                <a class='btn btn-info' href='#footer-linkbar-section'>Footer.linkbar</a>
                <a class='btn btn-info' href='#footer-footerlinks-section'>Footer.footerlinks</a>
                <a class='btn btn-info' href='#news-section'>News</a>
                <a class='btn btn-info' href='#interview-section'>Interview</a>
                <a class='btn btn-info' href='#staff-section'>Staff</a>
                <a class='btn btn-info' href='#ninjamaster-section'>Ninjamaster</a>
                <a class='btn btn-info' href='#ninjamaster-items-section'>Ninjamaster.items</a>
                <a class='btn btn-info' href='#ninjamaster-clans-section'>Ninjamaster.clans</a>
                <a class='btn btn-info' href='#email-messages-section'>Email.messages</a>
                <a class='btn btn-info' href='#homepage-unread-section'>Homepage.unread</a>
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
            {assign var="classes" value=['dragon'=>['name'=>'Dragon','expertise'=>'healing'], 'tiger'=>['name'=>'Tiger','expertise'=>'fire'], 'viper'=>['name'=>'Viper','expertise'=>'poison'], 'crane'=>['name'=>'Crane','expertise'=>'speed']]}
            {assign var="submit_successful" value="0"}
            {assign var="signupRequest" value=$signupRequest2}
            {assign var="error" value="Some error string for signup"}
            {assign var="submitted" value=false}
            {assign var="class_display" value="Some Class Here"}
            {assign var="completedPhase" value="4"}
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

        <section id='logout-section'>
            <h2>Logout</h2>
            <div class='logout-page'>
                {include file="logout.tpl"}
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
        <section id='shrine-resurrect-section'>
            <h2>Shrine</h2>
            {assign var="error" value=''}
            {assign var="player" value=$char}
            {assign var="freeResurrection" value=true}
            {assign var="has_chi" value=true}
            {assign var="shrineSections" value=['entrance', 'result-resurrect', 'result-heal', 'form-heal','reminder-full-hp','form-cure','form-resurrect','reminder-resurrect-cost']}
            {include file="shrine.result-resurrect.tpl"}
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

        <section id='npcs-blockers-section'>
            <h2>Npcs.blockers</h2>
            {assign var="ninja" value=$char}
            {assign var="health" value=0}
            {assign var="turns" value=0}
            {assign var="npc_template" value='npc.oni.tpl'}
            {include file="npc.tpl"}
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


        <section id='npcs-abstract-section'>
            <h2>Npcs: Abstract</h2>
            {assign var="npco" value=$npco}
            {assign var="victim" value='firefly'}
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


        <section id='npcs-samurai-section'>
            <h2>Npcs: Samurai</h2>
            {include file="npc.samurai-too-weak.tpl"}
            {include file="npc.samurai-too-tired.tpl"}
            {assign var="victory" value=true}
            {assign var="gold" value=44444}
            {assign var="samurai_damage_array" value=[22222, 343434, 565656]}
            {assign var="drop" value=true}
            {assign var="drop_display" value='some ginseng powder or whatever'}
            {include file="npc.samurai.tpl"}
            {assign var="samurai_damage_array" value=[7777, 36666, 565656]}
            {assign var="drop" value=true}
            {assign var="drop_display" value='some ginseng powder or whatever'}
            {assign var="victory" value=0}
            {include file="npc.samurai.tpl"}
        </section>

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

        <section id='clan-list-section'>
            <h2>Clan List</h2>
            {include file="clan.list.tpl"}
        </section>

        <section id='clan-info-section'>
            <h2>Clan.info</h2>
            {include file="clan.info.tpl"}
        </section>

        <section id='clan-manage-section'>
            <h2>Clan.manage</h2>
            {include file="clan.manage.tpl"}
        </section>

        <section id='clan-edit-section'>
            <h2>Clan.edit</h2>
            {include file="clan.edit.tpl"}
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
            <h2>Various Errors Scroll Long</h2>
            {assign var="error_type" value="frozen"}
            {assign var="error" value='Some error message or another here'}
            {include file="error.default.tpl"}
            {include file="error.frozen.tpl"}
            {include file="error.log_in.tpl"}
            <h2>(This one is the main error template)</h2>
            {include file="error.tpl"}
            {include file="error.dead.tpl"}
        </section>

        <section id='errors-dead-section'>
            <h2>Dead Go To Shrine Errors.dead</h2>
            {include file="error.dead.tpl"}
        </section>

        <section id='errors-log-in-section'>
            <h2>Login Errors</h2>
            {include file="error.log_in.tpl"}
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
            {assign var="required_kills" value=999999}
            {assign var="dim_mak_cost" value=545454}
            {assign var="classOptions" value=[]}
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
            {assign var="stats" value=['player_count'=>2343443, 'rich_haul'=>23232323, 'recently_active'=>23232323]}
            {assign var="duels" value=[['attacker_id'=>23232323, 'attacker'=>'whoever', 'defender'=>'someoneelse', 'defender_id'=>555555, 'won'=>true, 'killpoints'=>23424, 'date'=>'34343434']]}
            {include file="bath-house.tpl"}
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
            {assign var="enemy" value=$char}
            {assign var="combat_skills" value=[]}
            {assign var="targeted_skills" value=[]}
            {assign var="shift" value=66}
            {assign var="player_count" value=66666}
            {assign var="items" value=['shuriken'=>['item_id'=>232323, 'other_usable'=>true, 'name'=>'Shuriken', 'count'=>4321]]}
            {include file="enemies.attack-next.tpl"}
        </section>


        <section id='news-section'>
            <h2>News</h2>
            {assign var="create_successful" value=null}
            {assign var="create_role" value=true}
            {assign var="search_title" value='Bug search/filter'}
            {assign var="all_news" value=$all_news}
            {assign var="error" value="Some error string for news page"}
            {include file="news.tpl"}
        </section>

        <section id='footer-section'>
            <h2>Footer</h2>
            {include file="footer.tpl"}
        </section>

        <section id='footer-footerlinks-section'>
            <h2>Footer.footerlinks</h2>
            {include file="footerlinks.tpl"}
        </section>

        <section id='footer-linkbar-section'>
            <h2>Footer.linkbar</h2>
            {include file="footer.linkbar.tpl"}
        </section>

        <section id='interview-section'>
            <h2>Interview</h2>
            {include file="interview.tpl"}
        </section>

        <section id='staff-section'>
            <h2>Staff</h2>
            {include file="staff.tpl"}
        </section>

        <section id='ninjamaster-section'>
            <h2>Ninjamaster</h2>
            <p class='epic-note'>Currently commented out</p>
            {* {include file="ninjamaster.tpl"} *}
        </section>

        <section id='ninjamaster-items-section'>
            <h2>Ninjamaster.items</h2>
            {assign var="items" value=['shuriken'=>['item_id'=>232323, 'usage'=>'Thow the sharp end at someone', 'other_usable'=>true, 'name'=>'Shuriken', 'item_internal_name'=> 'shuriken', 'item_display_name'=>'Shuriken', 'image'=>'', 'item_cost'=>999, 'count'=>4321], 'shuriken2'=>['item_id'=>2324444, 'usage'=>'Thow the sharp end at someone', 'other_usable'=>true, 'name'=>'Shuriken2', 'item_internal_name'=> 'shuriken', 'item_display_name'=>'Shuriken', 'image'=>'', 'item_cost'=>999, 'count'=>4321]]}
            {include file="ninjamaster.css.tpl"}
            {include file="ninjamaster.items.tpl"}
        </section>

        <section id='ninjamaster-clans-section'>
            <h2>Ninjamaster.clans</h2>
            {include file="ninjamaster.clans.tpl"}
        </section>

        <section id='email-messages-section'>
            <h2>Email.messages</h2>
            <div>
            {include file="email.messages.tpl"}
            </div>
        </section>

        <section id='homepage-unread-section'>
            <h2>Homepage.unread</h2>
            <div>
                <a href="/messages" target="main">
                <i class='fa fa-envelope'></i> <span class='badge' id='unread-count'>•</span>
                </a>
            </div>
            <script type='module' src='/js/homepage.js'></script>
        </section>

        <section id='about-section'>
        
            <h2>About</h2>
            <p>Special case to avoid the base tag used in about</p>
            <iframe frameborder='0' id='epics-iframe' name='epics-iframe' src='/about'>
            </iframe>
        </section>

        <footer style='border-top:3rem dashed white;margin-top:3rem;'>
            <h6>END OF TEMPLATE</h6>
        </footer>
    </section>
    {* js script at the top to prevent breaking from templates *}

</main>




