<link rel="stylesheet" type="text/css" href="{cachebust file="/css/news.css"}">
<section id='full-news'>
  <h1>{$heading}</h1>
  {include file="flash-message.tpl"}
  {if is_logged_in()}
  <div class='parent'>
    <div class='child'>
      <form class='news-submit' id="post_msg" action="/news/store/" method="post" name="post_msg">
        	<fieldset>
        	  <legend>New post</legend>
            <div>
              <label for="news_title">Title:</label>
              <input id="news_title" type="text" size="40" maxlength="100" name="news_title" class="textField" placeholder="News title" required>
            </div>
            <div>
                <label for="tag">Tag:</label>
              <input id="tag" type="text" size="40" maxlength="250" name="tag" class="textField" placeholder="separated with commas for multiple tags">
            </div>
        	  <div>
              <textarea id="news_content" name="news_content" class="textField" cols="50" rows="10" required placeholder="The content, what do you want to make news about?"></textarea>
        	  </div>
            <input name='news_submit' type='hidden' value='1'>
            <br/>
            <input id='post-submit' type="submit" value="Add News" class="formButton">
      	</fieldset>
      </form>
    </div>
  </div>
  {/if}
  <nav>
    <a id='back-to-news' class='btn btn-default' href="/news">Back</a>
  </nav>
</section>
