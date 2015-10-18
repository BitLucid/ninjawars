<nav class='player-list-nav parent'>
  <div class='child'>

    {if $page == 1}
    &laquo;First&nbsp; <!-- &lsaquo; --> &#9664;&nbsp;
    {else}
    <a href="list.php?hide={$hide}&amp;page=1&amp;searched={$searched}" target='main'>&laquo;First</a>&nbsp;
    <a href="list.php?page={math equation="x-1" x=$page}&amp;searched={$searched}&amp;hide={$hide}" rel='previous' target='main'><!-- &lsaquo; -->&#9664; </a>&nbsp;
    {/if}

    <span class='current-page'>
      {$page}/{$numofpages}
    </span>

    {if !$last_page}
    &nbsp;&#9654;<!-- &rsaquo; -->
    &nbsp;Last&raquo;
    {else}
    &nbsp;<a href='list.php?page={math equation="x+1" x=$page}&amp;searched={$searched}&amp;hide={$hide}' rel='next' target='main'>&#9654;<!-- &rsaquo; --></a>
    &nbsp;<a href='list.php?page={$numofpages}&amp;hide={$hide}&amp;searched={$searched}' target='main'>Last&raquo;</a>
    {/if}
  </div>
</nav>
