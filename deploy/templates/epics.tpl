<link src='/css/epics.css' rel='stylesheet' type='text/css' />
<script src='/js/epics.js'></script>
<main id='epics'>
    <span style='float:left'>
        <a href='/'><button type='button' class='btn btn-default'><i class='fa fa-home'></i></button></a>
    </span>
    <span style='float:left'>
        <a href='/ninjamaster'><button type='button' class='btn btn-default'><i
                    class='fa fa-arrow-left'></i></button></a>
</span>

<header>
<h1>UI Story Epics</h1>

        {if $error}
            <div class='parent'>
                <div class='child error'>
                    {$error|escape}
                </div>
            </div>
{/if}

        <nav class='nav parent' id='sections-control'>
            <div class='child'>
<a class='btn btn-info' href='#intro-section'>Intro</a>
<a class='btn btn-info' href='#aside-section'>Aside</a>
<a class='btn btn-info' href='#chat-section'>Chat</a>
<a class='btn btn-info' href='#clan-section'>Clan</a>
<a class='btn btn-info' href='#healthbar-section'>Healthbar</a>
<a class='btn btn-info' href='#typography-section'>Typography</a>
<a class='btn btn-info' href='#staff-section'>Staff</a>
<a class='btn btn-info' href='#about-section'>About</a>
<a class='btn btn-info' href='#signup-section'>Signup</a>
<a class='btn btn-info' href='#errors-section'>Errors</a>
<a class='btn btn-info' href='#events-section'>Events</a>
<a class='btn btn-info' href='#dojo-section'>Dojo</a>
<a class='btn btn-info' href='#field-section'>Field</a>
<a class='btn btn-info' href='#list-section'>List</a>
            </div>
</nav>

    </header>

<section id='stories' style='min-height:30vh'>
        <section id='intro-section'>
            <h2>Intro</h2>
            {include file="intro.tpl"}
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
		    {include file="clan.tpl"}
		</section>

		<section id='healthbar-section'>
		    <h2>Healthbar</h2>
		    {assign var="health" value="55"}
		    {assign var="level" value="30"}
		    {include file="health_bar.tpl"}
		    {assign var="health" value="0"}
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

<section id='about-section'>
    <h2>About</h2>
    {include file="about.tpl"}
</section>


<section id='signup-section'>
    <h2>Signup</h2>
    {assign var="submit_successful" value="1"}
    {assign var="submitted" value="0"}
    {include file="signup.tpl"}
</section>

<section id='error-section'>
    <h2>Errors</h2>
    {assign var="error" value="Some example error"}
    {*
    {include file="error.dead.tpl"}
    {include file="error.default.tpl"}
	*}
    {include file="error.frozen.tpl"}
    {include file="error.log_in.tpl"}
</section>

<section id='events-section'>
    <h2>Events</h2>
    {include file="events.tpl"}
</section>


<section id='dojo-section'>
    <h2>Dojo</h2>
    {include file="dojo.tpl"}
</section>

<section id='field-section'>
    <h2>Field</h2>
    {assign var="not_enough_energy" value="1"}
    {assign var="earned_gold" value="55"}
    {assign var="worked" value="5"}
    {assign var="authenticated" value="0"}
    {assign var="recommended_to_work" value="6"}
{include file="work.tpl"}
</section>

{* list is complicated, so putting that to the end *}
		<section id='list-section'>
<h2>List</h2>
commented out for now
{*  include file="list.tpl" *}
</section>
<h6>The end</h6>
</section>
{* js script at the top to prevent breaking from templates *}

</main>