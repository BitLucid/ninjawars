<h1>News Board</h1>
<div id='full-news'>
<style>
{literal}
.news-submit #post-submit{
    padding:.2em .4em;font-size:1.3em;font-weight:bolder;
}
.link-as-button{
    margin-bottom:.5em;margin-top:1em;float:right;margin-right:1.5em;
}
#full-news .parent{
  text-align:center;
}
#full-news .child{
  display:inline-block;text-align:left;
}
#full-news fieldset{
  padding:0.7em 1.3em;
}
#full-news fieldset > div{
  margin-bottom:1em;
}
{/literal}
</style>
{if is_logged_in()}
<div class='parent'>
  <div class='child'>
    <form class='news-submit' id="post_msg" action="{$target|escape}" method="post" name="post_msg">
      	<fieldset>
      	  <legend>New post</legend>
          <div>
            <label for="news_title">Title:</label>
            <input id="news_title" type="text" size="{$field_size}" maxlength="100" name="news_title" class="textField" placeholder="News title">
          </div>
          <div>
              <label for="tag">Tag:</label>
            <input id="tag" type="text" size="{$field_size}" maxlength="250" name="tag" class="textField" placeholder="separated with commas for multiple tags">
          </div>
      	  <div>
            <textarea id="news_content" name="news_content" class="textField" cols="{$field_size+10}" rows="10" required placeholder="The content, what do you want to make news about?"></textarea>
      	  </div>
          <input name='news_submit' type='hidden' value='1'>
          <br/>
          <input id='post-submit' type="submit" value="Add News" class="formButton">
    	</fieldset>
    </form>
  </div>
</div>
{/if}
<a class='link-as-button' href="news.php">Back</a>
</div>
