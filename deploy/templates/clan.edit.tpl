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
            <strong>Clan Avatar:</strong> <input name="clan-avatar-url" type="text" value="{$clan->getAvatarUrl()|escape}">
		  </p>
		  <small class='de-em'>
		    To create a clan avatar, upload an image to <a href="http://www.imageshack.com" target="_blank" class="extLink">imageshack.com</a>
		    Then paste the image's full url here. Image can be .jpg or .png
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
        <input type="submit" value="Save Changes">
      </form>
    </div>
  </div>  <!-- End of leader-options div -->
</section>  <!-- End of leader-panel options -->
