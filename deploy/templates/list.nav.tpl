<nav class='player-list-nav parent'>
  <div class='child'>

    {if $page == 1}
    <i class="fas fa-fast-backward" title="First"></i>&nbsp;
    <i class="fas fa-step-backward" title="First"></i>&nbsp;
    {else}
    <a href="/list?hide={$hide}&amp;page=1&amp;searched={$searched}" target='main'><i class="fas fa-fast-backward" title="First"></i></a>&nbsp;
    <a href="/list?page={math equation="x-1" x=$page}&amp;searched={$searched}&amp;hide={$hide}" rel='previous' target='main'><i class="fas fa-step-backward"></i></a>&nbsp;
    {/if}

    <span class='current-page'>
      {$page}/{$numofpages}
    </span>

    {if !$last_page}
    &nbsp;<i class='fas fa-step-forward'></i>
    &nbsp;<i class='fas fa-fast-forward'></i>
    {else}
    &nbsp;<a href='/list?page={math equation="x+1" x=$page}&amp;searched={$searched}&amp;hide={$hide}' rel='next' target='main'><i class='fas fa-step-forward'></i></a>
    &nbsp;<a href='/list?page={$numofpages}&amp;hide={$hide}&amp;searched={$searched}' target='main'><i class='fas fa-fast-forward'></i></a>
    {/if}
  </div>
</nav>
