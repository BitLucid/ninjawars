

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
      {$health_section}
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
