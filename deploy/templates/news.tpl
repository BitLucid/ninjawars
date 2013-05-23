<h1>News Board</h1>
<style type='text/css'>
{literal}
.green-border{
	border:green .3em solid;
	padding:.2em;
	margin: .3em auto;
}
.search_title {
  margin-bottom:20px;
  font-style:italic;
  font-size:smaller;
  color: gray;
}

dl.news dt {
  display: block;
  float: none !important;
  color: red;
  font-weight: bold;
  margin-bottom:5px;
  font-style:bold;
  font-weight:larger;
}

dl.news dd {
  display: block;
  margin-bottom: 10px;
}

dl.tags {
  margin-bottom: 20px;
}

dl.tags dt {
  display: inline-block;
  color: gray;
  font-weight: normal;
  margin-right: 4px;
}

dl.tags dd {
  display: inline-block;
  margin-bottom: 0px;
  font-style: italic;
}

{/literal}
</style>

<div id='message-list'>
{if isset($new_successful_submit) and $new_successful_submit}
  <div class='green-border'>
    Your news successfully posted!
  </div>
{/if}
  
<a class='link-as-button' style='margin-bottom:.5em;margin-top:1em;float:right;margin-right:1.5em' href="news.php">Refresh</a>
{if is_logged_in()}
<a class='link-as-button' style='margin-bottom:.5em;margin-top:1em;float:right;margin-right:1.5em' href="news.php?new=true">New Post</a>
{/if}

{if isset($search_title)}
<p class="search_title">{$search_title}</p>
{/if}

{foreach from=$all_news key=index_news item=single_news}
{assign var="news_account" value=$single_news->getAccountss()}
<dl class="news">
  {if $single_news->getTitle()}
  <dt>{$single_news->getTitle()}</dt>
  {else}
  <dt>Untitled</dt>
  {/if}
  <dd>{$single_news->getContent()}</dd>
  <dl class="tags">
    <dt>Tags:</dt>
    <dd>{$single_news->getTags()|to_tags}</dd>
    <dt>Published:</dt>
    {if $single_news->getCreated()}
    <dd>{$single_news->getCreated()|date_format:"%A, %B %e, %Y"}</dd>
    {else}
    <dd>{$smarty.now|date_format:"%A, %B %e, %Y"}</dd>
    {/if}
    <dt>Author:</dt>
    <dd><a target="main" href="player.php?player_id={$news_account->getFirst()|to_playerid}">{$news_account->getFirst()|to_playername}</a></dd>
  </dl>
</dl>
{/foreach}
</div>