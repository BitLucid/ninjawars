<h2>Messages</h2>

<div id='clan-and-search'>

{if $has_clan}
 <div id='clan-mail-section'>
  <form id='clan_msg' action='messages.php' method='get' name='clan_msg'>
    <textarea id='message' name='message' class='textField'></textarea>
    <input type='hidden' value='toclan' name='toclan'>
    <input type='hidden' value='1' name='messenger'>
    <input type='submit' value='Mail Clan' class='formButton'>
  </form>
  </div>
  
  
{/if}

{if $message_sent_to}
    <div id='message-sent-to'>Message sent to {$message_sent_to}.</div>
{/if}

</div>

<div id='ninja-search'>
    Find a ninja to message:
    <form id='player_search' action='list_all_players.php' method='get' name='player_search'>
        <input id='searched' type='text' maxlength='50' name='searched' class='textField'>
        <input type='submit' value='Find' class='formButton'>
    </form>
</div>

{if $nav}
        {$nav}
{/if}

<ul id='message-list'>
    {$message_list}
</ul>

{if $nav}
        {$nav}
{/if}
