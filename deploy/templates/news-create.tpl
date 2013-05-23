<h1>News Boars</h1>
<div id='full-news'>
{if is_logged_in()}
<form class='news-submit' id="post_msg" action="{$target|escape}" method="post" name="post_msg">
  	<fieldset>
  	  <legend>New post</legend>
  	  <div>
	      <textarea id="news_content" name="news_content" class="textField" cols="{$field_size+10}" rows="10" placeholder="The content..."></textarea>
	  </div>
  	  <div>
  	  	<label for="tag">Tag:</label>
	    <input id="tag" type="text" size="{$field_size}" maxlength="250" name="tag" class="textField" placeholder="separated with commas for multiple tags">
	  </div>
      <input name='news_submit' type='hidden' value='1'>
      <br/>
      <input type="submit" value="Post" class="formButton" style='padding:.2em .4em;font-size:1.3em;font-weight:bolder'>
	</fieldset>
</form>
{/if}
<a class='link-as-button' style='margin-bottom:.5em;margin-top:1em;float:right;margin-right:1.5em' href="news.php">Back</a>
</div>