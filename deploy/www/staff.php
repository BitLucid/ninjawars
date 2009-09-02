<?php
$private    = false;
$alive      = false;
$page_title = "Staff";
$quickstat  = false;

include SERVER_ROOT."interface/header.php";
// TODO: Add a switching system so that it displays further information in an expandable way.
?>
<script type='text/javascript'>
$(document).ready(function() {
	$('.developer-info').hide();
	$('.expand-link').click(function () {
		$('.developer-info').slideDown();
		$('.expand-link').hide();
	});
});
</script>

<p class="title">Staff</p>

<div>
  <p>
    The preferred method of contacting us with problems or bugs with the game is via the <a href="http://ninjawars.proboards19.com">Ninjawars Forum</a>.
    For other issues, including confirmation problems, email us at: <a href="mailto:<?php echo SUPPORT_EMAIL;?>"><?php echo SUPPORT_EMAIL;?></a>
  </p>
</div>

<div class='developers'>
  <div class='subtitle'>Developers</div>
	<!-- TODO: Make expandable sections for this staff stuff. -->
  <div class='developer'>
    <a class='expand-link'>[Expand] - </a><a href="mailto:ninjawarsTchalvak@gmail.com">Tchalvak / Roy Flynn</a> - Programmer and Maintainer
    <span id='social-networks'>
      - <a target='_blank' href="http://www.facebook.com/tchalvak">on Facebook</a>
      - <a target='_blank' href="http://www.myspace.com/toastersquid">on Myspace</a>
      - on AIM: Tchalvak
    </span>
    <div class='developer-info'>

    <p>
      Hailing from the icy steppes of upstate New York, Roy got his first computer at the ripe old age of 14, and has been addicted to computers ever since.  He's worked in IT fixing computers, and eventually decided that the web is the future, got in on the action, and hasn't looked back.  Since 2003, he's been working in php and webdesign, which he got started on from working on NinjaWars.
    </p>

    <p>
      Roy is almost done with a BS degree in Biochemistry from SUNY Geneseo.  He says it was an accident.  Luckily he also took programming classes along the way, and liked them.
    </p>

    <p>
      When he's able to overcome his attachment to instantaneous communication and exchange of ideas (i.e. the internet), he truly enjoys swimming and walking dogs, even if they are someone else's dogs.
    </p>

    <img src="images/tchalvak.jpg">
    <div id='facebook-badge'><!-- Facebook Badge START --><a href="http://www.facebook.com/tchalvak" title="Roy Flynn" target="_TOP" style="font-family: &quot;lucida grande&quot;,tahoma,verdana,arial,sans-serif; font-size: 11px; font-variant: normal; font-style: normal; font-weight: normal; color: #3B5998; text-decoration: none;">Roy Flynn</a><span style="font-family: &quot;lucida grande&quot;,tahoma,verdana,arial,sans-serif; font-size: 11px; line-height: 16px; font-variant: normal; font-style: normal; font-weight: normal; color: #555555; text-decoration: none;">&nbsp;|&nbsp;</span><a href="http://www.facebook.com/badges.php" title="Make your own badge!" target="_TOP" style="font-family: &quot;lucida grande&quot;,tahoma,verdana,arial,sans-serif; font-size: 11px; font-variant: normal; font-style: normal; font-weight: normal; color: #3B5998; text-decoration: none;">Make your own badge</a><br><a href="http://www.facebook.com/tchalvak" title="Roy Flynn" target="_TOP"><img src="http://badge.facebook.com/badge/16501613.459.488706671.png" alt="Roy Flynn" style="border: 0px;"></a><!-- Facebook Badge END --></div>
    </div><!-- End of .developer-info -->
  </div>
  <div class='developer'>
    <span style="color:white">Beagle / Al Vazquez</span> - Previously Programming Lead and Current Server Administrator
    <!-- No developer info here. -->
  </div>

</div><!-- End of the Developers section -->

<hr>

<div class='other-credits'>
  <div class='subtitle'>Other Credits</div>
  <p> NinjaLord / John Facey, II - Founder &amp; Original Developer of NinjaWars - <a href="interview.php">Interview with John by www.bbgdev.com</a> </p>
  <p>
    Other developers &amp; planners at various points:
    chrismonster, kultcher, sparky, &amp; suavisimo
  </p>
  <p> Evolym Fragile/Davinel - the Flash Banner </p>
  <p> Magatsu - the Shop Graphics </p>
  <p> Alegion - the Koi photograph Background - <a href='http://alegion.deviantart.com/'>alegion.deviantart.com/</a> </p>
</div>

<hr>
<div>
  <div class='subtitle'>Contributing to ninjawars</div>

  <p> Ninjawars is open source, with the source code downloadable and able to be openly contributed back to at: </p>

  <p>
    <a target='_blank' href="http://github.com/tchalvak/ninjawars/tree/master">http://github.com/tchalvak/ninjawars/tree/master</a>
  </p>

  <p>Want to get involved?  You can hack away at the code on github and suggest changes or ask any questions on the forum.</p>
</div>
<div class='license'>
  <div class='subtitle'>License</div>
  <p>
    <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/us/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/us/88x31.png"></a><br>
    <span xmlns:dc="http://purl.org/dc/elements/1.1/" href="http://purl.org/dc/dcmitype/InteractiveResource" property="dc:title" rel="dc:type">ninjawars</span>
    by <a xmlns:cc="http://creativecommons.org/ns#" href="<?php echo WEB_ROOT;?>" property="cc:attributionName" rel="cc:attributionURL">ninjawars.net</a>
    is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/us/">
    Creative Commons Attribution-Share Alike 3.0 United States License</a>.<br>
    Permissions beyond the scope of this license may be available at
    <a xmlns:cc="http://creativecommons.org/ns#" href="<?php echo WEB_ROOT;?>staff.php" rel="cc:morePermissions"><?php echo WEB_ROOT;?>staff.php</a>.
  </p>
</div>
<?php
include SERVER_ROOT."interface/footer.php";
?>
