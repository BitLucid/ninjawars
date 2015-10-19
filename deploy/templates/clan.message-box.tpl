{* VIEW PART FOR ALL PLAYERS THAT ARE IN A CLAN *}
<form id='msg_clan' action='clan.php' method='get' name='msg_clan'>
  <div>
	<textarea id='message' maxlength='{$smarty.const.MAX_CLAN_MSG_LENGTH|escape}' name='message' class='textField' placeholder='Message to clan' autocomplete='off'></textarea><br>
	<input type="hidden" name="command" value="message">
	<input type='submit' value='Send' class='formButton'>
  </div>
</form>
