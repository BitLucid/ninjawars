<h1>Messages</h1>

{include file='message-tabs.tpl' current=$current_tab}

<!-- Message an individual ninja-->
<div class='glassbox' id='message-ninja'>
  <form action='messages.php' method='post' name='ninja_message'>
    <div>
      <em class='char-name'>@<input class='char-name textField' type='text' size='26' name='to' value='{$to}'></em>
      <em>say</em>
      &apos;<input id='message-to-ninja' type='text' size='30' name='message' class='textField' autocomplete='off' maxlength="{$smarty.const.MAX_MSG_LENGTH|escape}">&apos;
      <input type='hidden' name='messenger' value='1'>
      <input type='submit' value='Send' name='ninja_message' class='formButton'>
    </div>
  </form>
</div>

<div id='clan-and-search'>

{if $has_clan}
  <div id='clan-mail-section' class='glassbox'>
    <form id='clan_msg' action='messages.php' method='get' name='clan_msg'>
      <div>
        <input type="text" id='message-clan' name='message' class='textField' maxlength="{$smarty.const.MAX_CLAN_MSG_LENGTH|escape}">
        <input type='hidden' value='toclan' name='toclan'>
        <input type='hidden' value='1' name='messenger'>
        <input type='submit' value='Mail Clan' class='formButton'>
      </div>
    </form>
  </div>
{/if}


  <div class='glassbox' id='delete-messages'>
    <a href="messages.php?delete=1&amp;type={$type_filter}">Delete {$viewed_type} Messages</a>
  </div>
</div> <!-- End of clan and search div -->

{if $message_sent_to}
  <div id='message-sent-to' class='ninja-notice'>Message sent to <em class='char-name'>{$message_sent_to|escape}</em>.</div>
{/if}


<div class='glassbox'>
  {include file="messages.nav.tpl"}

  <dl id='message-list' class='message-list'>
  {foreach from=$messages item="loop_message"}
  	{include file='single_message.tpl' message=$loop_message}
  {/foreach}
  </dl>

  {include file="messages.nav.tpl"}
</div>

<script type='text/javascript' src='{$smarty.const.JS_ROOT}messageDeleteConfirm.js'></script>

{if $message_to eq 'individual' or $message_to eq 'clan'}
<script type='text/javascript'>
{literal}
  $().ready(function(){
    // Cache the focus point.
    var focus = '{/literal}{$message_to}{literal}';
    if(focus == 'clan'){
      $('input#message-clan').focus();
    } else {
      $('input#message-to-ninja').focus();
    }
  });
{/literal}
</script>
{/if}