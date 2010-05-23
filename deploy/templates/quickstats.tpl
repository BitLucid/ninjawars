
{if !$viewinv}

  <dl class='quickstats player-stats'>
    <dt>Health:</dt>
    <dd>
      <div style="width: 100%;border: 1px solid #ee2520;" title="HP: {$health}">
        <div style="width: {$health_pct}%;background-color: #ee2520;">&nbsp;</div>
      </div>
    </dd>
    <dt>Exp:</dt>
    <dd>
      {if $progress == 100}<a target='main' href='dojo.php' style='text-decoration:none'>{/if}
      <div style="width: 100%;border: 1px solid #6612ee;" title="Exp: {$progress}%">
        <div style="width: {$progress}%;background-color: #6612ee;">&nbsp;</div>
      </div>
      {if $progress == 100}</a>{/if}
    </dd>
    <dt>Status:</dt>
    <dd>
     {$status_output_list}
    </dd>
    <dt>Turns:</dt>
    <dd>
        {$turns}
    </dd>
    <dt>Gold:</dt>
    <dd>
        {$gold}
    </dd>
    <dt>Bounty:</dt>
    <dd>
        {$bounty}
    </dd>
  </dl>


{else}

	    <dl class='quickstats inventory'>
	    {foreach from=$items item=item}
	          <dt>{$item.item}: </dt>
	          <dd> {$item.amount}</dd>
	    {/foreach}
	      <dt style='color:gold'>Gold:</dt>
	      <dd style='color:gold'>{$gold}</dd>

	    </dl>
{/if}


<!-- Update the login-bar's health display. -->
<script language='javascript' type='text/javascript'>
    updateHealthBar({$health});
</script>
