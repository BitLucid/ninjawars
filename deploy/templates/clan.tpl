<script type="text/javascript" src="/js/clan.js"></script>

<h1 id='clan-page-title'>Clan Panel</h1>

{if $action_message}
    <div class='ninja-notice'>{$action_message}</div>
{/if}


{if $player_id}
	{if $own_clan_id }
		{if $leader_of_own_clan}
			{if $command == 'rename'}
				<!-- //Clan Leader Clan renaming -->
				
				{if $clan_renamed}
				
    				<p>Your new clan name is <strong>{$new_clan_name}.</strong></p>
				
				{else}
				
				    <div class='notice'>
				        Clan names must be from 3 to 24 characters, and can only contain letters,
				         numbers, spaces, underscores, or dashes, although you can request exceptions if they're fun.
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
				{if $kicked == ''}
							    <!-- Clan Leader Action Kick a chosen member -->

					<form id='kick_form' action='clan.php' method='get' name='kick_form'>
					<div>
					Kick: 
					<select id='kicked' name='kicked'>
					<option value=''>--Pick a Member--</option>
					
					{foreach from=$members_and_ids key=row item=l_member}
					    <option value='{$l_member.player_id}'>{$l_member.uname}</option>
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



            {if $leader_of_own_clan}

	<!-- Checks whether the viewer is the leader to display these sections.  -->
	<div id='leader-panel'>
	      <div id='leader-panel-title'>{$own_clan_name|escape} Leader Actions</div>
	        <ul id='leader-options'>
	            <li><a href='clan.php?command=invite'>Recruit for your Clan</a></li>
	            <li><a href='clan.php?command=rename'>Rename Clan</a></li>
	            <li><a href='clan.php?command=disband'>Disband Your Clan</a></li>
	            <li><a href='clan.php?command=kick'>Kick a Clan Member</a></li>
	        </ul>
	      
	    <div>
	    <div><strong>Change Clan Image</strong></div>
	    To create a clan avatar, upload an image to <a href='http://www.imageshack.com' class='extLink'>imageshack.com</a> 
	    Then put the image's full url here:
    	    <form action='clan.php' name='avatar_and_message'>
    	        <input type='hidden' name='command' value='view'>
    	        <input type='hidden' name='avatar_or_message_change' value='1'>
    	        <input type='hidden' name='clan_id' value='{$own_clan_id}'>
    	        <input name='clan-avatar-url' type='text' value='{$clan_avatar_current}'>
    	        (Image can be .jpg or .png)
	        <div><strong>Change Clan Message</strong></div>
    	        Change your clan description below (max of 500 characters):
    	        <textarea name='clan-description'>{$clan_description_current}</textarea>
    	        <input type='submit' value='Save Changes'>
	        </form>
	        
	    </div>
	        
	        
    </div>
        
            <!-- End of leader-panel options -->
            {/if}
		{else}
		<!-- Part of a clan, but not the leader -->
		
		<!-- ***  NON LEADER CLAN MEMBER OPTIONS ***  -->
		
		
			{if $command != 'leave'}	
			    <!-- *** Clan Member Action to Leave their Clan ***-->
			    
                <p>You are currently a member of the {$own_clan_name|escape} Clan.</p>
    			<p style='margin-top:1.2em;margin-bottom:1.2em;'><a href='clan.php?command=leave' onclick='leave_clan(); return false;'>Leave Current Clan</a></p>
				
			{/if}
			<!-- If the clan member left their clan, the command -was- leave, and they
			should have had their clan, clan_id and such membership variables revoked.
			As such, after the leave command, no clan membership display information should occur. -->


		{/if}<!-- End of options for clan-members -->

		{if $own_clan_id }
		

    		{if $command == 'msgclan'}
    		    <!--  *** Clan Member Input for Messaging their Entire Clan *** -->
    			<form id='msg_clan' action='clan.php' method='get' name='msg_clan'>
    	          <div>
    	          Message: <input id='message' type='text' size='50' maxlength='1000' name='message' class='textField'>
    	          <input type='submit' value='Send This Message' class='formButton'>
    	          </div>
    	          </form>
    		{/if}		
		
		    <!-- *** OPTIONS FOR ALL IN-CLAN PLAYERS -->
		    <!-- Note that these should not display after a clan "leave" option occurs.  -->
			<ul id='clan-options'>
	            <li><a href='clan.php?command=msgclan'>Message Clan Members</a></li>
	            <li><a href='clan.php?command=view&amp;clan_id={$own_clan_id|escape:'url'}'>View Your Clan</a></li>
	        </ul>
		{/if}
	{else}
	    <!-- // ****** VIEWER NOT YET PART OF ANY CLAN ******* -->
	
		{if $command == "join"}	
		    <!-- // *** Clan Joining Action *** -->
			{if $process == 1}
            <div id='clan-join-request-sent' class='ninja-notice'>
              Your request to join {$viewed_clan.clan_name|escape} has been sent to {$leader.uname|escape}
            </div>
			{else}
            <h2>Clans Available to Join</h2>
            <ul>
				{foreach from=$leaders item="leader_vo"}
              <li>
                <a target='main' class='clan-join' href="clan.php?command=join&amp;clan_id={$leader_vo.clan_id|escape:'url'|escape}&amp;process=1">Join{$leader_vo.clan_name|escape}</a>.
                Its leader is <a href="player.php?player_id={$leader_vo.player_id|escape:'url'|escape}">{$leader_vo.uname|escape}</a>, level {$leader_vo.level|escape}.
                <a target='main' href="clan.php?command=view&amp;clan_id={$leader_vo.clan_id|escape:'url'|escape}">View This Clan</a>
              </li>
				{/foreach}
            </ul>
			{/if}
		{/if}

		<div>You are a lone ninja, not a member of any clan.</div>
		<div><a href='clan.php?command=join'>View clans available to join</a></div>
		{if $clan_id_viewed}
    		<div><a href='clan.php?command=join&amp;clan_id={$clan_id_viewed|escape}&process=1'>
    		        Send a request to join the Clan {$viewed_clan_name|escape}
    		        </a></div>
    	{/if}
    	
		{if $can_create_a_clan}
			<div><a href='clan.php?command=new'>Start a New Clan</a></div>
		{else}
			<div>You can start your own clan when you reach level {$clan_creator_min_level}.</div>
		{/if}


		<!-- End of viewer not part of any clan section -->
	{/if}


	<!-- End of logged-in-only display section -->
{/if}


{if $command == "view"}
	<!-- // *** A view of the member list of any clan *** -->
	{$clan_view}

    {if $leader_of_viewed_clan}
    	<div class='ninja-notice'>You are the leader of this clan.</div>
    {/if}
	
{/if}


<!-- *** Display all the clans in their tag list. *** -->

{include file="clan.list.tpl" clans=$clans}
