<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Rules";

include SERVER_ROOT."interface/header.php";
?>
<style type="text/css">
	dl.rules dt {
		color: red;
		font-weight: bold;
	}

	dl.rules dd {
		margin-bottom: 10px;
	}

	dl.penalties {
		margin-top: 3px;
	}

	dl.penalties dt {
	    color: red;
		font-weight: normal;
		float: left;
		margin-right: 4px;
	}

	dl.penalties dd {
		margin-bottom: 0px;
		font-style: italic;
	}
</style>

<h1>Fair Play Rules</h1>
<dl class="rules">
  <dt>Do not multiplay.</dt>
  <dd>
    Multiplaying is actively using two or more characters.<br>
    Players using multiple characters will have their accounts suspended indefinately.
    <dl class="penalties">
      <dt>Penalty:</dt>
      <dd>Suspension of all accounts involved in the multiplaying.</dd>
    </dl>
  </dd>
  <dt>Do not abuse bugs, help us by reporting them.</dt>
  <dd>
    Bugs are aspects of the game that are obviously broken and unbalanced, or that provide an unfair advantage over other players.<br>
    Players finding and being the first to report bugs descriptively enough to allow them to be fixed will be rewarded.  Silently abusing a bug to gain unfair advantage over other players, on the other hand, will result in suspension of your account.  Clans with leaders who abuse bugs will necessarily be disbanded when the clanleader's account is suspended.<br>
    <dl class="penalties">
      <dt>Penalty:</dt>
      <dd>Suspension of account abusing the bug.</dd>
    </dl>
  </dd>
  <dt>Do not spam.</dt>
  <dd>
    Spamming is excessive use of any of the communication method; chat, mail, forums, etc without specifically directing the posts that you make at any player.  While conversing with another player, it is acceptable to post frequently.  If no other players are involved in a conversation with you, on the other hand, what you are doing is probably just spamming.<br>
    Spamming, whether in the chat room or on the forum, is annoying and unnecessary.  Newbies may not know enough not to spam, tell them not to.  Spammers will either become unable to access the place that they spam on, or their accounts will be suspended, whichever proves necessary.
    <dl class="penalties">
      <dt>First Offense:</dt>
      <dd>Warning.</dd>
      <dt>Second Offense:</dt>
      <dd>Loss of messaging capabilities or suspension of account that is spamming.</dd>
    </dl>
  </dd>
</dl>
<p>Other than that, kill each-other and have fun.</p>

<?php
include SERVER_ROOT."interface/footer.php";
?>
