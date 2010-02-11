

<!-- Update the login-bar's health display. -->
<script language='javascript' type='text/javascript'>
    updateHealthBar({$players_health});
</script>



{if !$viewinv}

  <table class='quickstats player-stats'>
  <tr>
    <td>
    Health: 
    </td>

    <td>
      <div style="width: 80%;border: 1px solid #ee2520;" title="HP: {$health}">
        <div style="width: {$health_pct}%;background-color: #ee2520;">&nbsp;</div>
      </div>
    </td>
  </tr>
  <tr>
    <td>
    Exp:
    </td>

    <td>
      <div style="width: 80%;border: 1px solid #6612ee;" title="Exp: {$progress}%">
        <div style="width: {$progress}%;background-color: #6612ee;">&nbsp;</div>
      </div>
    </td>
  </tr>
  <tr>
    <td>
    Status:
    </td>

    <td>

	{$status_output_list}

    </td>
  </tr>
  <tr>
    <td>
    Turns:
    </td>
    <td>
        {$turns}
    </td>
  </tr>
  <tr>
    <td>
    Gold: 
    </td>
    <td>
        {$gold}
    </td>
  </tr>
  <tr>
    <td>
    Bounty: 
    </td>
    <td>
        {$bounty}
    </td>
  </tr>

  </table>
{else}

	    <table class='quickstats inventory'>
        {$items_section}
	      <tr>
	        <td>
	          Gold: 
	        </td>
	        <td>
	          {$players_gold}<br>
	        </td>
	      </tr>
	    </table>
{/if}
