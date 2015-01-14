    <script type="text/javascript" src="/js/clan.js"></script>

    <h1 id='clan-page-title'>Clan Panel</h1>

{if $action_message}
<div class='ninja-notice'>{$action_message}</div>
{elseif isset($kick_success) and $kick_success}
<div class='ninja-notice'>You have removed {$kicked_name|escape} from your clan.</div>
{elseif $command eq 'invite' and isset($person_invited) and $person_invited}
	{if !isset($char_id_invited) or !$char_id_invited}
<div class='ninja-notice'>No such ninja as <i>{$person_invited|escape}</i> exists.</div>
	{elseif $invite_failure_message}
<div class='ninja-notice'>You cannot invite {$person_invited|escape}. {$invite_failure_message}</div>
	{else}
<div class='ninja-notice'>You have invited {$person_invited|escape} to join your clan.</div>
	{/if}
{/if}

{if $player_id}
	{if $own_clan_id }
		{if $leader_of_own_clan}
			{if $command == 'rename'} {* Clan Leader Clan renaming *}

				{if $clan_renamed}
<p>Your new clan name is <strong>{$new_clan_name|escape}</strong>.</p>
				{else}
<div class='notice'>
  Clan names must be from 3 to 24 characters, and can only contain letters, numbers, spaces, underscores, or dashes, although you can request exceptions if they're fun.
</div>
<form id='clan_rename' action='clan.php' name='clan_rename'>
  <div>
    <input id='command' type='hidden' value='rename' name='command'>
    <input id='new_clan_name' type='text' name='new_clan_name' class='textField'>
    <input type='submit' class='formButton' value='Rename Clan'>
  </div>
</form>
				{/if}
			{elseif $command == 'kick'}
				{if $kicked == ''} {* Clan Leader Action Kick a chosen member *}

<form id='kick_form' action='clan.php' method='get' name='kick_form'>
  <div>
    Kick:
    <select id='kicked' name='kicked'>
      <option value=''>--Pick a Member--</option>
{* TODO - change this html_options control *}
					{foreach from=$members_and_ids key=row item=l_member}
      <option value='{$l_member.player_id|escape}'>{$l_member.uname|escape}</option>
					{/foreach}

    </select>
    <input id='command' type='hidden' value='kick' name='command'>
    <input type='submit' value='Kick' class='formButton'>
  </div>
</form>
				{/if}
<!-- Otherwise the kick was successful and the action message will be displayed. -->
			{elseif $command == 'disband'}
			    {if !$clan_disbanded}

Are you sure you want to continue? This will remove all members from your clan.<br>
<form id='disband' method='get' action='clan.php' name='disband'>
  <div>
    <input type='submit' value='Disband' class='formButton'>
    <input id='command' type='hidden' value='disband' name='command'>
    <input id='sure' type='hidden' value='yes' name='sure'>
  </div>
</form>
			    {/if}
<!-- Otherwise the clan was disbanded, and that will show in the action message section. -->
			{elseif $command == 'invite'}
<!-- Other outcomes are handled by the action message, this form will be displayed success or failure -->
Name of potential clan member:<br>
<form id='clan_invite' action='clan.php' name='clan_rename'>
  <div>
    <input id='command' type='hidden' value='invite' name='command'>
    <input id='person_invited' type='text' name='person_invited' class='textField'>
    <input type='submit' class='formButton' value='Invite'>
  </div>
</form>
<hr>
			{/if}

            {if $leader_of_own_clan} {* Checks whether the viewer is the leader to display these sections. *}
<div id='leader-panel'>
  <div id='leader-panel-title' title='Show or hide the clan leader options'>
    {$own_clan_name|escape} Clan Leader Actions
  </div>
  <div id='show-leader-options' style='display:none'>
    <a class='show-hide' href='#show-leader-options'>Show leader options</a>
  </div>
  <div id='leader-options' style='margin: 0 inherit 0'>
    <script type="text/javascript">
	        {literal}
        $().ready(function (){
            $('#leader-panel-title, #show-leader-options').click(function(){
                $('#leader-options, #show-leader-options').toggle();
                return false;
            });
			$('#leader-options, #show-leader-options').toggle();
        });
	        {/literal}
    </script>
    <ul id='leader-options-list'>
      <li><a href='clan.php?command=invite'>Recruit for your Clan</a></li>
      <li><a href='clan.php?command=rename'>Rename Clan</a></li>
      <li><a href='clan.php?command=disband'>Disband Your Clan</a></li>
      <li><a href='clan.php?command=kick'>Kick a Clan Member</a></li>
    </ul>

    <div>
      <div><strong>Change Clan Image</strong></div>
      To create a clan avatar, upload an image to <a href='http://www.imageshack.com' target='_blank' class='extLink'>imageshack.com</a>
      Then put the image's full url here:
      <form action='clan.php' name='avatar_and_message'>
        <input type='hidden' name='command' value='view'>
        <input type='hidden' name='avatar_or_message_change' value='1'>
        <input type='hidden' name='clan_id' value='{$own_clan_id|escape}'>
        <input name='clan-avatar-url' type='text' value='{$clan_avatar_current|escape}'>
        (Image can be .jpg or .png)
        <div><strong>Change Clan Message</strong></div>
        Change your clan description below (max of 500 characters):
        <textarea name='clan-description'>{$clan_description_current|escape}</textarea>
        <input type='submit' value='Save Changes'>
      </form>
    </div>
  </div>  <!-- End of leader-options div -->
</div>  <!-- End of leader-panel options -->
            {/if}
		{else} {* Part of a clan, but not the leader - NON LEADER CLAN MEMBER OPTIONS *}
			{if $command != 'leave'} {* Clan Member Action to Leave their Clan *}
<p>You are currently a member of the {$own_clan_name|escape} Clan.</p>
<p style='margin-top:1.2em; margin-bottom:1.2em;'>
  <a href='clan.php?command=leave' onclick='leave_clan(); return false;'>Leave Current Clan</a>
</p>
			{/if}
{* If the clan member left their clan, the command -was- leave, and they
should have had their clan, clan_id and such membership variables revoked.
As such, after the leave command, no clan membership display information should occur. *}
		{/if} {* End of options for clan-members *}

		{if $own_clan_id} {* OPTIONS FOR ALL IN-CLAN PLAYERS *}
		    {if $message_sent}
<!-- When a message is sent, refocus on the text input section -->
<script type='text/javascript'>
		    	{literal}
   	$().ready(function(){
   		$('input#message').focus();
   	});
		    	{/literal}
</script>
		    {/if}

{* Note that these should not display after a clan "leave" option occurs. *}
<ul id='clan-options'>
  <li>
    <!--  *** Clan Member Input for Messaging their Entire Clan *** -->
    <form id='msg_clan' action='clan.php' method='get' name='msg_clan'>
      <div>
        Message clan: <input id='message' type='text' size='30' maxlength='{$smarty.const.MAX_CLAN_MSG_LENGTH|escape}' name='message' class='textField'>
        <input type='submit' value='Send This Message' class='formButton'>
      </div>
    </form>
  </li>
  <li><a href='clan.php?command=view&amp;clan_id={$own_clan_id|escape:'url'}'>View Your Clan</a></li>
</ul>
		{/if}
	{else} {* VIEWER NOT YET PART OF ANY CLAN *}
		{if $command == "join"} {* Clan Joining Action *}
			{if $process == 1}
<div id='clan-join-request-sent' class='ninja-notice'>
  Your request to join {$viewed_clan.clan_name|escape} has been sent to {$leader.uname|escape}
</div>
			{else}
<h2>Clans Available to Join</h2>
<ul>
				{foreach from=$leaders item="leader_vo"}
      <li>
        <a target='main' class='clan-join' href="clan.php?command=join&amp;clan_id={$leader_vo.clan_id|escape:'url'|escape}&amp;process=1"><img alt='' src='/images/icons/mono/usersplus32.png' height=16 width=16 style='vertical-align:middle'> Join {$leader_vo.clan_name|escape}</a>
        Its leader is <a href="player.php?player_id={$leader_vo.player_id|escape:'url'|escape}">{$leader_vo.uname|escape}</a>, level {$leader_vo.level|escape}.
        <a target='main' href="clan.php?command=view&amp;clan_id={$leader_vo.clan_id|escape:'url'|escape}">View This Clan <img alt='' src='/images/icons/mono/circleright32.png' height=16 width=16 style='vertical-align:middle'></a>
      </li>
				{/foreach}
</ul>
			{/if}
		{/if}
<section class='glassbox'>
<div>You are a lone ninja, not a member of any clan.</div>
<div><a href='clan.php?command=join'>View clans available to join</a></div>
		{if $clan_id_viewed}
<div>
  <a href='clan.php?command=join&amp;clan_id={$clan_id_viewed|escape}&amp;process=1'>Send a request to join the Clan {$viewed_clan_name|escape}</a>
</div>
    	{/if}
</section>

		{if $can_create_a_clan}
  <div><a href='clan.php?command=new'>Start a New Clan</a></div>
		{else}
  <small class='glassbox'>You can start your own clan when you reach level {$clan_creator_min_level}.</small>
		{/if}
	{/if} {* End of viewer not part of any clan section *}
{/if} {* End of logged-in-only display section *}

{if $command == "view"} {* A view of the member list of any clan *}
{include file='clan.info.tpl' members_array=$members clan_name=$clan_name avatar_url=$clan.clan_avatar_url clan_name=$clan.clan_name clan_description=$clan.description}

    {if $leader_of_viewed_clan}
<div class='ninja-notice'>You are the leader of this clan.</div>
    {/if}
{/if}

<!-- *** Display all the clans in their tag list. *** -->

{include file="clan.list.tpl" clans=$clans}
