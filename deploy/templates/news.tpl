<link rel="stylesheet" type="text/css" href="{cachebust file="/css/news.css"}">
<section id='news-list'>
  <h1>News</h1>
{if isset($create_successful) and $create_successful}
  <div class='parent'>
    <div class='success-notice child'>
      <strong>Your news successfully posted!</strong>
    </div>
  </div>
{/if}
  {include file="flash-message.tpl"}

{if $create_role}
	<div id='make-news-post'>
		<a class='btn btn-primary' href="/news/create/">Post News</a>
	</div>
{/if}

  {if isset($search_title)}
  <p class="search_title">{$search_title}</p>
  {/if}

  {foreach from=$all_news key=index_news item=single_news}
  <article class="news">
    {if $single_news->title}
    <h3>{$single_news->title|escape}</h3>
    {/if}
    <section class='post-content'>{$single_news->content|escape}</section>
    <footer>
      - <span class='tags'> {$single_news->tags|to_tags} </span>
      {if $single_news->created} 
        <time class='timeago' datetime='{$single_news->created}' title='{$single_news->created|date_format:"%A, %B %e, %Y"}'>
          {$single_news->created|date_format:"%A, %B %e, %Y"}
        </time>
      {else}
      <time class='timeago' datetime='{$smarty.now}' title='{$smarty.now|date_format:"%A, %B %e, %Y"}'>
        {$smarty.now|date_format:"%A, %B %e, %Y"}
      </time>
      {/if} by 
      <a target="main" href="/player?player_id={$single_news->author_id|to_playerid}">
        {$single_news->author|to_playername|escape}
      </a>
    </footer>
  </article>
  {/foreach}

</section><!-- End of news-list -->
