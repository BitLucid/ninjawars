{if $submitted}
	{include file="signup-submit-intro.tpl"}
{/if}

{if !$submit_successful}
	{include file="signup-form.tpl"}
{/if}
