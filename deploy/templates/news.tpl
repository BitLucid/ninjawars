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
  {assign var="news_account" value=$single_news->getAccountss()}
  <article class="news">
    {if $single_news->getTitle()}
    <h3>{$single_news->getTitle()|escape}</h3>
    {/if}
    <section class='post-content'>{$single_news->getContent()|escape}</section>
    <footer>
      - <span class='tags'> {$single_news->getTags()|to_tags} </span>
      {if $single_news->getCreated()} <time class='timeago' datetime='{$single_news->getCreated()}' title='{$single_news->getCreated()|date_format:"%A, %B %e, %Y"}'>{$single_news->getCreated()|date_format:"%A, %B %e, %Y"}</time>
      {else}
      <time class='timeago' datetime='{$smarty.now}' title='{$smarty.now|date_format:"%A, %B %e, %Y"}'>{$smarty.now|date_format:"%A, %B %e, %Y"}</time>
      {/if} by <a target="main" href="/player?player_id={$news_account->getFirst()|to_playerid}">{$news_account->getFirst()|to_playername|escape}</a>
    </footer>
  </article>
  {/foreach}



</section><!-- End of news-list -->
