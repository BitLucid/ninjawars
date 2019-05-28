<link href="/css/imgur.min.css" rel="stylesheet" media="screen">
<style>
#avatar-preview-image{
	max-height: 300px;
	max-width: 300px;
}
#avatar-preview-image-area{
	text-align:center;
}
</style>

<section id="leader-panel">
  <h2 id="leader-panel-title">
    {$clan->getName()|escape} Leader Actions
  </h2>
  <div id="leader-options" style="margin: 0 inherit 0;">
    <ul id="leader-options-list" class="clearfix">
      <li>
{include file="clan.invite.tpl"}
	  </li>
      <li><form action="/clan/disband" method="post"><input class="btn btn-warning" type="submit" value="Disband Your Clan"></form></li>
      <li>
		  <form id='kick_form' action='/clan/kick' method='get' name='kick_form'>
			<div>
				<input type='submit' value='Kick' class='formButton'><br>
				<select id='kicked' name='kicked'>
					<option value=''>--Pick a Member--</option>
{assign var="members" value=$clan->getMembers()}
{foreach from=$members item="member"}
	{if $member.player_id neq $player->id()}
					<option value='{$member.player_id|escape}'>{$member.uname|escape}</option>
	{/if}
{/foreach}
				</select>
			</div>
		</form>
	  </li>
    </ul>

    <div class="glassbox" style="text-align: left;">
      <h3>Change Clan Details</h3>
      <form action="/clan/update" name="avatar_and_message">
		<div>
		  <p>
		    <strong>Clan Name:</strong> <input id="new_clan_name" type="text" name="new_clan_name" class="textField" {literal}pattern="[A-Za-z0-9_- ]{3,24}"{/literal} required value="{$clan->getName()|escape}">
		  </p>
		  <small class='de-em'>
		    Clan names must be from 3 to 24 characters, and can only contain letters, numbers, spaces, underscores, or dashes, although you can request exceptions if they are fun.
		  </small>
		</div>
		<div>
			<p>
						<strong>Clan Description:</strong> <textarea name="clan-description">{$clan->getDescription()|escape}</textarea>
				</p>
			<small class='de-em'>
						(500 character limit)
			</small>
		</div>
		<div>
		  <p>
            <strong>Clan Avatar:</strong> <input name="clan-avatar-url" type="text" value="{$clan->getAvatarUrl()|escape}">
		  </p>
			<h6>Upload Clan Avatar</h6>

			<div class="dropzone">
			</div>
			<p class='avatar-upload-error error' hidden>There was a problem uploading your avatar</p>
			<div id='avatar-preview-image-area'>
				<img id='avatar-preview-image' src='{$clan->getAvatarUrl()}'/>
			</div>
		</div>
        <input type="submit" value="Save Changes">
      </form>
    </div>
  </div>  <!-- End of leader-options div -->
</section>  <!-- End of leader-panel options -->
<!-- Image upload script -->
<script src="/js/imgur.min.js"></script>
<script>
    var callback = function (res) {
        if (res.success === true) {
					document.querySelector('input[name=clan-avatar-url]').value = res.data.link;
					document.querySelector('#avatar-preview-image').src = res.data.link;
					document.querySelector('#avatar-preview-image-area').hidden = false;
        } else {
					document.querySelector('.avatar-upload-error').hidden = false;
				}
    };

    new Imgur({
        clientid: 'ab74db84f23bc1f',
        callback: callback
    });
</script>