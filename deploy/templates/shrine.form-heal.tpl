<style>
#max_heal_form input[type=submit]{
  min-width:50%;
}
</style>

<form id="max_heal_form" action="/shrine/heal" method="post" name="max_heal_form" class='thick centered'>
  <div>
    <div><em class='speech'>How much healing do you need?</em></div>
    <input type="hidden" value="max" name="heal_points">
    <input type="submit" value="Full Heal" class="btn btn-primary">
  </div>
</form>
<form id="heal_form" action="/shrine/heal" method="post" name="heal_form">
  <div class='thick'>
    <input type="submit" value="Heal" class="btn btn-default">
    <input type="text" size="3" maxlength="4" name="heal_points" class="textField" style='font-size:1.1em'> HP
  </div>
</form>
