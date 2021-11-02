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
<a class='btn btn-info' href='#list-section'>List</a>
<a class='btn btn-info' href='#clan-section'>Clan</a>
<a class='btn btn-info' href='#healthbar-section'>Healthbar</a>
            </div>
</nav>

    </header>

<div id='stories'>
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

{* list is complicated, so putting that to the end *}
		<section id='list-section'>
<h2>List</h2>
		    {include file="list.tpl"}
</section>
</div>
{* js script at the top to prevent breaking from templates *}

</main>