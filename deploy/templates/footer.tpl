

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
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-707264-2', 'ninjawars.net');
ga('send', 'pageview');
</script>
{/if}
    
    
  </body>
</html>
