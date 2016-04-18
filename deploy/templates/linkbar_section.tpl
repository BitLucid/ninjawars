
      {include file='footerlinks.tpl'}


        <div id='footer-middle-bar'>
		    <h3 id='created-by'>
		    	<a href='http://bitlucid.com' target='_blank'>CREATED BY BitLucid, Inc.</a>
		    </h3>
		    <ul id='footer-authors'>
		    	<li class='author'>
		    		<img class='avatar' alt='' src="//www.gravatar.com/avatar/68dd1255208cbf50f2c42615bbbd8f46?d=monsterid&amp;80&amp;r=x">
					<a href='//royronalds.com' class='extLink'>Roy Ronalds</a>
					<a href='/player?player=tchalvak'>Ninja: Tchalvak</a>
					<a href='//twitter.com/tchalvak' rel='nofollow' class='extLink'>@tchalvak</a>
          <a rel="author external me" href="https://plus.google.com/104798509386141979631/">On Google Plus</a>
		    	</li>
		    	<li class='author'>
		    		<img class='avatar' alt="" src="//www.gravatar.com/avatar/01b8df4923c0559d3ff56e6922e35011?d=monsterid&amp;80&amp;r=x">
		    		<a style='cursor:pointer;text-decoration:none'>Al Vazquez</a>
		    		<a href='/player?player=beagle'>Ninja: Beagle</a>
		    	</li>
		    </ul>
        </div>
        <div id='footer-bottom-bar'>
        	<span id='html5-integration'>
		    <a href="http://www.w3.org/html/logo/" rel='nofollow'>
			<img src="//www.w3.org/html/logo/badge/html5-badge-h-css3-multimedia-performance-semantics.png" width="229" height="64" alt="HTML5 Powered with CSS3 / Styling, Multimedia, Performance &amp; Integration, and Semantics" title="HTML5 Powered with CSS3 / Styling, Multimedia, Performance &amp; Integration, and Semantics">
			</a>
			</span>


        	<!-- Script to display commits -->
        	<script type='text/javascript' src="{cachebust file="/js/repo.js"}"></script>
        	<script>
        	{literal}
			$(document).ready(function() {
				loadLastCommitMessage(); // To display commits on the main page.
			});
        	{/literal}
        	</script>
			<div id='latest-commit-section'>
				<p id='latest-commit-title' style='display:none'>Most recent upcoming change to ninjawars:</p>
				<span id='latest-commit' style='display:none'>
				</span>
			</div>
        </div>
