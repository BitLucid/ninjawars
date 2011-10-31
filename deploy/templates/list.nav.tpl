<div class='player-list-nav'>
  <form action='list.php' method='get'>
    <div>

{if $page == 1}
      &laquo;First | &lsaquo;Previous {$record_limit}&nbsp; |
{else}
      <a href="list.php?hide={$hide}&amp;page=1&amp;searched={$searched}">&laquo;First</a> |
      <a href="list.php?page={math equation="x-1" x=$page}&amp;searched={$searched}&amp;hide={$hide}" rel='previous'>&lsaquo;Previous {$record_limit}</a>&nbsp;|
{/if}

      <span class='current-page'>
        <input type='hidden' name='hide' value='{$hide}'>
        <button type='submit' class='formButton' value='Page'>Page</button>
        <input type='hidden' name='searched' value='{$searched}'>
        <input class='page-counter' type='text' name='page' value='{$page}' size='3'> /{$numofpages}
      </span>

{if !$last_page}
      | Next {$record_limit}&rsaquo;
      | Last&raquo;
{else}
      | <a href='list.php?page={math equation="x+1" x=$page}&amp;searched={$searched}&amp;hide={$hide}' rel='next'>Next {$record_limit}&rsaquo;</a>
      | <a href='list.php?page={$numofpages}&amp;hide={$hide}&amp;searched={$searched}'>Last&raquo;</a>
{/if}
    </div>
  </form>
</div>
