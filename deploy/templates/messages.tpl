<h2>Messages</h2>

<!-- Clan send box here -->

{if $clan_send}



 <div id='clan-mail-section'>
  <form id='clan_msg' action='mail_send.php' method='get' name='clan_msg'>
    <textarea id='message' name='message' class='textField'></textarea>
    <input id='to' type='hidden' value='clansend' name='to'>
    <input id='messenger' type='hidden' value='1' name='messenger'>
    <input type='submit' value='Mail Clan' class='formButton'>
  </form>
  </div>
  
  
{/if}

<div id='ninja-search'>
    Find a ninja to message:
    <form id='player_search' action='list_all_players.php' method='get' name='player_search'>
        <input id='searched' type='text' maxlength='50' name='searched' class='textField'>
        <input type='submit' value='Find' class='formButton'>
    </form>
</div>

<!-- Individual ninja search here -->

<ul id='message-list'>
{$message_list}
</ul>
