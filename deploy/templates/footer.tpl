{if $is_index}{* Only display footer on index page *}
    <footer class="footer">
      <div class="container">
        <p class="text-muted">Place sticky footer content here.</p>
      </div>

      <div id='index-footer' class='navigation'>
        <!-- Stuff like catchphrases, links, and the author information -->
        {include file='linkbar_section.tpl'}
      </div>

    </footer>

{/if}

<script type="text/javascript">
NW.loggedIn = {if $logged_in}true{else}false{/if};
var pub_char_info = {if $json_public_char_info}{$json_public_char_info}{else}''{/if};
{if !$is_index && $quickstat}
{literal} // Only refresh the stats when they're not initially loading and when requested.
$(function() {
	if(pub_char_info){
		NW.refreshStats(pub_char_info); // Refresh the data piped in above.
	}
});
{/literal}
{/if}
</script>

{if !$smarty.const.LOCAL_JS}<!-- Skip for local js -->
<!-- Google Analytics, just add all the tracking info to an array at once -->
{literal}
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-707264-2', 'ninjawars.net');
ga('send', 'pageview');
</script>
{/literal}
{/if}

  </body>
</html>
