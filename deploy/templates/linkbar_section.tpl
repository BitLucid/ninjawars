              
      	<div id='footer-top-bar'>
        <span id='nw-catchphrases'>
{literal}
          <script type="text/javascript">
            $().ready(function (){
                var catchphrases = $('#nw-catchphrases span');
                var rand = Math.floor(Math.random()*catchphrases.size());
                // Choose random index.
                catchphrases.hide().eq(rand).show();
                // Hide all, show one at random.
                
                var footer = $('#index-footer');
                //Hide the second two sections.
                var footerBottoms = footer.find('#footer-middle-bar, #footer-bottom-bar').hide();
                // When any of the three sections are hovered, show the bottom two.
        // Only change the display of the bottom sections if another event doesn't over-ride.
                footer.hover(
                	function(){footerBottoms.stop(true, true).slideDown()}, 
                	function(){footerBottoms.stop(true, true).delay(2000).slideUp()}
                );
                
            });
          </script>
{/literal}
        <!-- These catchphrases will be displayed randomly. -->
          <span style="display:none">There was going to be a NinjaWars2, but NinjaWars1 stabbed it.</span>
          <span style="display:none">Join a clan, promote multiple stab wounds.</span>
          <span style="display:none">Annoy the Emperor, kill Samurai.</span>
          <span style="display:none">Some theorize that poison is actually liquified ninja.</span>
          <span style="display:none">Helping ninja stab people since 2003.</span>
          <span style="display:none">Fact: Ninja can just click faster.</span>
          <span>Oni are actually quite friendly, if you get to know them.</span>
        </span>
         |
        <a href="tutorial.php" target="main">Help</a> |
        <a href="rules.php" target="main">Rules</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?board=ann" target="_blank" class="extLink">News</a> |
        <a href="http://ninjawars.pbwiki.com/" target="_blank" class="extLink">Wiki</a> |
        <a href="http://ninjawars.proboards.com" target="_blank" class="extLink">Forum</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?action=display&board=suggcomp&thread=1174" target="_blank" class="extLink">Feedback</a>
        </div>
        <style>
        {literal}
        	#footer-authors{
        		display:block;
        		min-height:3.5em;
        	}
        	#footer-authors .author{
        		display:inline-block;
        		width:200px;
        		min-height:80px;
        		margin-left:10%;
        		position:relative;
        	}
        	#footer-authors avatar{
        		display:block;
        	}
        	#footer-authors .author a{
        		display:inline-block;
        	}
        	#html5-integration{
        		display:block;
        	}
        {/literal}
        </style>
        
        <div id='footer-middle-bar'>
		    <span id='created-by'>
		    	<a href='staff.php' target='main'>CREATED BY BitLucid, Inc.</a>
		    </span>
		    <ul id='footer-authors'>
		    	<li class='author'>
		    		<img class='avatar' alt='' src="http://www.gravatar.com/avatar/68dd1255208cbf50f2c42615bbbd8f46?d=monsterid&amp;80&amp;r=x">
					<a href='//royronalds.com' class='extLink'>Roy Ronalds</a>
					<a href='player.php?target=tchalvak'>Ninja: Tchalvak</a>
					<a href='//twitter.com/tchalvak' class='extLink'>@tchalvak</a>
		    	</li>
		    	<li class='author'>
		    		<img class='avatar' alt="" src="http://www.gravatar.com/avatar/01b8df4923c0559d3ff56e6922e35011?d=monsterid&amp;80&amp;r=x">
		    		<a style='cursor:pointer;text-decoration:none'>Al Vazquez</a>
		    		<a href='player.php?target=beagle'>Ninja: Beagle</a>
		    	</li>
		    </ul>
        </div>
        <div id='footer-bottom-bar'>
        	<span id='html5-integration'>
		    <a href="http://www.w3.org/html/logo/">
			<img src="http://www.w3.org/html/logo/badge/html5-badge-h-css3-multimedia-performance-semantics.png" width="229" height="64" alt="HTML5 Powered with CSS3 / Styling, Multimedia, Performance &amp; Integration, and Semantics" title="HTML5 Powered with CSS3 / Styling, Multimedia, Performance &amp; Integration, and Semantics">
			</a>
			</span>
        
        	
        	<!-- Script to display commits -->
        	<script type='text/javascript' src="js/staffPage.js"></script>
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
