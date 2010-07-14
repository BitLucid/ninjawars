<h1>Messages</h1>

<script type='text/javascript' src='{$templatelite.const.JS_ROOT}messageDeleteConfirm.js'></script>

<div id='clan-and-search'>

{if $has_clan}
  <div id='clan-mail-section'>
    <form id='clan_msg' action='messages.php' method='get' name='clan_msg'>
      <div>
        <textarea id='message' name='message' class='textField'></textarea>
        <input type='hidden' value='toclan' name='toclan'>
        <input type='hidden' value='1' name='messenger'>
        <input type='submit' value='Mail Clan' class='formButton'>
      </div>
    </form>
  </div>
{/if}

  <div id='ninja-search'>
    Find a ninja to message:
    <form id='player_search' action='list_all_players.php' method='get' name='player_search'>
      <div>
        <input id='searched' type='text' maxlength='50' name='searched' class='textField'>
        <input type='submit' value='Find' class='formButton'>
      </div>
    </form>
  </div>

  <div id='delete-messages'>
    <a href="messages.php?delete=1">Delete Messages</a>
  </div>
</div>

{if $message_sent_to}
<div id='message-sent-to'>Message sent to {$message_sent_to|escape}.</div>
{/if}

{include file="messages.nav.tpl"}

<dl id='message-list'>
  {$message_list}
</dl>

{include file="messages.nav.tpl"}
