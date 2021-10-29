<style>
    {literal}
        main#epics section {
            max-height: 50vh;
            overflow-y: auto
        }

    {/literal}
</style>
<main id='epics'>
    <span style='float:left'><a href='/'><button type='button' class='btn btn-default'><i
                    class='fa fa-home'></i></button></a></span>

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
<a class='btn btn-info' href='#aside-section'>Aside</a>
<a class='btn btn-info' href='#chat-section'>Chat</a>
            </div>
</nav>

    </header>

<div id='stories'>
        <section id='aside-section'>
            <h2>Aside</h2>
            {include file="aside.tpl"}
</section>

        <section id='chat-section'>
            <h2>Chat</h2>
            {include file="mini-chat.section.tpl"}
        </section>
</div>
    <script src='/js/epics.js'></script>

</main>