{if $is_index}{* Only display footer on index page *}
    <footer class="footer footer-inverse">
      <div class="container">
        {include file='footerlinks.tpl'}
      </div>

      <div id='index-footer' class='navigation'>
        <!-- Stuff like links, and the author information -->
        {include file='footer.linkbar.tpl'}
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
    const { debug } = console ?? { debug: () => { /* no-op */ } };
    debug('Refreshing player stats if available stats');
		NW.refreshStats(pub_char_info); // Refresh the data piped in above.
	}
});
{/literal}
{/if}
</script>

{if !$smarty.const.LOCAL_JS}{* Skip for local js *}
<!-- Google Analytics, just add all the tracking info to an array at once -->
{literal}
<!-- Google tag (gtag.js) -- Updated 1/9/2024 -- RR -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-WWN26L7SKM"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-WWN26L7SKM');
</script>
{/literal}
{/if}

  </body>
</html>
