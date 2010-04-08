<h1>Shop</h1>

<div class='description'>
{$description}
</div>
<form id="shop_form" action="shop.php" method="post" name="shop_form" {if !$is_logged_in}onsubmit="return false;"{/if}>
<input id="purchase" type="hidden" value="1" name="purchase">
<table>
<tr>
  <td colspan="4">&nbsp; </td>
</tr>
<!--
<tr>
  <td>
  Item
  </td>

  <td>
  Description
  </td>

  <td>
  Cost
  </td>

  <td>
  Picture
  </td>
</tr>
-->
<tr>
  <td>
  <input name="item" type="submit" value="Fire Scroll" class="shopButton">
  </td>

  <td>
  Reduces HP
  </td>

  <td>
  $175
  </td>

  <td>
  <img style='width:55px;height:20px' src="images/scroll.png" alt="Scroll">
</td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Ice Scroll" class="shopButton">
  </td>

  <td>
  Reduces Turns
  </td>

  <td>
  $125
  </td>

  <td>
  <img style='width:55px;height:20px' src="images/scroll.png" alt="Scroll">
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Speed Scroll" class="shopButton">
  </td>

  <td>
  Increases Turns
  </td>

  <td>
  $225
  </td>

  <td>
  <img style='width:55px;height:20px' src="images/scroll.png" alt="Scroll">
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Stealth Scroll" class="shopButton">
  </td>

  <td>
  Stealths a Ninja(<a href="about.php#magic">*</a>)
  </td>

  <td>
  $150
  </td>

  <td>
  <img style='width:55px;height:20px' src="images/scroll.png" alt="Scroll">
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Shuriken" class="shopButton">
  </td>

  <td>
  Reduces HP
  </td>

  <td>
  $50
  </td>

  <td>
  <img src="images/mini_star.png" alt="Shuriken" style='width:25px;height:23px'>
  </td>
</tr>
<tr>
  <td colspan="4" style="text-align: center;padding: 1em;">
{if $is_logged_in}
  How many would you like? <input id="quantity" type="text" size="3" maxlength="5" name="quantity" class="textField" value="1">
{else}
  To purchase items you must <a href="signup.php?referrer=">become a ninja</a>.
{/if}
  </td>
</tr>
<tr>
  <td colspan="4">&nbsp; </td>
</tr>
</table>
</form>
