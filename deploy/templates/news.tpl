<style type='text/css'>
{literal}
#news-list .parent{
  text-align:center;
}
#news-list .child{
  display:inline-block;text-align:left;
}
.success-notice{
  border-color:#00806C;
  border-style:solid;
  border-width:0.3em;
	padding:0.4em 0.7em 0.3em;
	margin: 0.1em auto 0.3em;
}
.search_title {
  margin-bottom:20px;
  font-style:italic;
  font-size:smaller;
  color: gray;
}
#make-news-post{
  margin-bottom:.5em;margin-top:1em;float:right;margin-right:1.5em;
}
.news h3{
  background:#2D2D2D;
  color:#FD4326;
  margin-bottom:1em;
}
.news h3:before, .news h3:after{
  content: "\00a0"; display:inline-block; font-size:32px; height:32px;width:32px;margin:0 0.3em;
  background-image:url('/images/icons/mono/article32.png');
}
#news-list article{
  display:block;
  background-color:#333;
  font-size:larger;
  width:60%;
  min-width:30em;
  margin:0 auto;
}
#news-list article .post-content{
  white-space:pre-wrap;
  padding:0 5%;
  width:90%;
}
#news-list article .post-content:first-child{
  padding-top:1em;
}
#news-list article + article{
  margin-top:3em;
}
#news-list article footer{
  color:gray;
  font-size:smaller;
  width:90%;
  padding:1em 5% .5em;
}
.news time{
  font-style:italic;
  color:grey;
}
{/literal}
</style>

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

{if is_logged_in()}
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
