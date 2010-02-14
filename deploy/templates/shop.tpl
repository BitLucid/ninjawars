<h1>Shop</h1>

<div class='description'>
{$description}
</div>
<form id="shop_form" action="shop.php" method="post" name="shop_form">
<input id="purchase" type="hidden" value="1" name="purchase">
<table>
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
  <img src="images/scroll.png" alt="Scroll">
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
  <img src="images/scroll.png" alt="Scroll">
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
  <img src="images/scroll.png" alt="Scroll">
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
  <img src="images/scroll.png" alt="Scroll">
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
  <img src="images/mini_star.png" alt="Shuriken">
  </td>
</tr>
<tr>
  <td colspan="3">
  How Many? <input id="quantity" type="text" size="3" maxlength="5" name="quantity" class="textField">
  </td>
</tr>
</table>
</form>
