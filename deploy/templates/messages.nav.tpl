{if $pages gt 1}
<nav class="message-nav">
	{if $current_page > 1}
  		<a class='prev' href="/messages/{$current_tab}?page={math equation="x-1" x=$current_page}&amp;type={$type}"><i class="fas fa-step-backward"></i></a>
	{else}
  		<span class='prev inactive'><i class="fas fa-step-backward"></i></span>
	{/if}
  		<span class='current-page-location'>{$current_page}/{$pages}</span>
	{if $current_page < $pages}
  		<a class='next' href="/messages/{$current_tab}?page={math equation="x+1" x=$current_page}&amp;type={$type}"><i class="fas fa-step-forward"></i></a>
	{else}
  		<span class='next inactive'><i class="fas fa-step-forward"></i></span>
	{/if}
</nav>
{/if}
