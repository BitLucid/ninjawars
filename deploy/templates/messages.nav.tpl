{if $pages gt 1}
<nav class="message-nav">
	{if $current_page > 1}
  		<a class='prev' href="messages.php?command={$current_tab}&amp;page={math equation="x-1" x=$current_page}&amp;type={$type}">&#9664;</a>
	{else}
  		<span class='prev inactive'>&#9664;</span>
	{/if}
  		<span class='current-page-location'>{$current_page}/{$pages}</span>
	{if $current_page < $pages}
  		<a class='next' href="messages.php?command={$current_tab}&amp;page={math equation="x+1" x=$current_page}&amp;type={$type}">&#9654;</a>
	{else}
  		<span class='next inactive'>&#9654;</span>
	{/if}
</nav>
{/if}
