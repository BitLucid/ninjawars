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
<a class='btn btn-info' href='#typography-section'>Typography</a>
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

		<section id='typography-section'>
		    <h2>Typography</h2>
		    <h1>Heading 1</h1>
		    <h2>Heading 2</h2>
		    <h3>Heading 3</h3>
		    <h4>Heading 4</h4>
		    <h5>Heading 5</h5>
		    <h6>Heading 6</h6>
		    <div class='title'>Title</div>
		    <div class='subtitle'>Subtitle. Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>

		    <p>Paragraph text. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos blanditiis tenetur unde
		        suscipit, quam beatae rerum inventore consectetur, neque doloribus, cupiditate numquam dignissimos laborum
		        fugiat deleniti? Eum quasi quidem quibusdam.</p>
		    <div><button>Button text</button></div>
		    <div>Raw div body text. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos blanditiis tenetur unde
		        suscipit, quam beatae rerum inventore consectetur, neque doloribus, cupiditate numquam dignissimos laborum
		        fugiat deleniti? Eum quasi quidem quibusdam.</div>
		    <div>
		        <figure>Some figure<figcaption>Some figure caption</figcaption>
		        </figure>
		    </div>
		    <p class='speech'>Speech text. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos blanditiis tenetur
		        unde suscipit, quam beatae rerum inventore consectetur, neque doloribus, cupiditate numquam dignissimos
		        laborum fugiat deleniti? Eum quasi quidem quibusdam.</p>
		    <div class='description'>Description.Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos blanditiis
		        tenetur unde suscipit, quam beatae rerum inventore consectetur, neque doloribus, cupiditate numquam
		        dignissimos laborum fugiat deleniti? Eum quasi quidem quibusdam.</div>
</section>
{* list is complicated, so putting that to the end *}
		<section id='list-section'>
<h2>List</h2>
		    {include file="list.tpl"}
</section>
</div>
{* js script at the top to prevent breaking from templates *}

</main>