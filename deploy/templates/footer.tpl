

    <script type="text/javascript">
	NW.loggedIn = {if $logged_in}true{else}false{/if};
{if !$is_index && $quickstat}
// Only execute on non-index pages.
{literal}
$(function() {
{/literal}

	// Has to use php so can't be literal.
	NW.refreshStats({$json_public_char_info});

{literal}
});
{/literal}
{/if}
    </script>
    


{if !$smarty.const.LOCAL_JS}
    <!-- Google Analytics, just add all the tracking info to an array at once -->
	<script>var _gaq=[['_setAccount','UA-707264-2'],['_trackPageview']];</script>
	<script async src='http://www.google-analytics.com/ga.js'></script>
{/if}
    
    
  </body>
</html>
