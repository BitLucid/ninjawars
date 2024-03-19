<h1>{if $current_tab === 'clan'}Clan {/if}Messages</h1>

{include file='message-tabs.tpl' current=$current_tab}

{if $informational}<div class='ninja-notice'>{$informational}</div>{/if}

{if $error}<div class='ninja-error'>{$error}</div>{/if}

<section>

  <div id='email-messages'>
    <button title='Email {$messages_type} Messages' class='btn btn-info' type='submit' name='submit'><i class='fa fa-envelope'></i></button>
  </div>

  <div id='delete-messages'>
    <form method='post' action='/messages/delete_{$current_tab}'>
      <input type='hidden' name='delete' value='1'>
      <input type='hidden' name='type' value='{$type}'>
      <button title='Delete {$messages_type} Messages' class='btn btn-warning' type='submit' name='submit'><i class='fa fa-trash'></i></button>
    </form>
  </div>

  {if $current_tab != 'clan'}
  <!-- Message an individual ninja-->
  <div class='glassbox' id='message-ninja'>
    <form action='/messages/send_personal' method='post' name='ninja_message' id='message-form'>
      <div>
        <em class='char-name'>@ <input class='char-name textField js-hook' style='max-width:15rem' type='text' size='26'
id='send-to' name='to' value='{$to|escape}' required='required'></em>
        <em>say</em>
        &apos;<input id='message-to-ninja' type='text' size='30' name='message' class='textField' autocomplete='off' maxlength='{$smarty.const.MAX_MSG_LENGTH|escape}' required='required' autofocus='autofocus'>&apos;
        <input type='hidden' name='messenger' value='1'>
<input type='submit' value='Send' name='ninja_message' class='btn formButton btn-primary'>
      </div>
    </form>
  </div>
  {/if}

{if $has_clan && $current_tab == 'clan'}
  <div id='clan-mail-section' class='glassbox'>
    <form id='clan_msg' action='/messages/send_clan' method='post' name='clan_msg'>
      <div class='input-group'>
        <input autofocus type="text" id='message-clan' name='message' class='textField' maxlength="{$smarty.const.MAX_CLAN_MSG_LENGTH|escape}" autocomplete='off' required=required autofocus=autofocus>
        <input type='hidden' name='toclan' value='1'>
        <input type='hidden' value='1' name='messenger'>
        <input type='submit' value='Mail Clan' class='formButton'>
      </div>
    </form>
  </div>
{/if}

</section>

{if $message_sent_to}
  <div id='message-sent-to' class='ninja-notice'>Message sent to <em class='char-name'>{$message_sent_to|escape}</em>.</div>
{/if}

<section class='glassbox'><!-- Message list area -->
  {include file="messages.nav.tpl"}

  <dl id='message-list' class='message-list'>
  {foreach from=$messages item="loop_message"}
  	{include file='message.single.tpl' message=$loop_message}
  {/foreach}
  </dl>

  {include file="messages.nav.tpl"}
</section>

<script>
// Set the need to refocus on the messaging areas if necessary.
var refocus = {if $individual_or_clan}true{else}false{/if};
var focusArea = '{$message_to}';
</script>
<!-- Confirmation requirements and refocus setup -->
<script type='module' src='{cachebust file="/js/talk.js"}'></script>
