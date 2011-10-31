
{if !$smarty.const.LOCAL_JS}
    <!-- Google Analytics -->
    <script type="text/javascript" src="http://www.google-analytics.com/ga.js"></script>
    <!-- The google-analytics code that gets run is in nw.js -->
    <script type="text/javascript">
    // GOOGLE ANALYTICS
    /* There's a script include that goes with this, but I just put it in the head directly.*/
{literal}
    try {
        var pageTracker = _gat._getTracker("UA-707264-2");
        pageTracker._trackPageview();
    } catch(err) {}
{/literal}
    </script>
{/if}
    
    
  </body>
</html>
