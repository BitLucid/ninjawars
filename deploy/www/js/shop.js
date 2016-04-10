$(function() {

  var auth = typeof loggedIn !== 'undefined'? loggedIn : false;

  var quantity = NW.storage.appState.get("quantity", 1);
  
  $("#quantity").val(quantity);

  $("#shop_form").submit(function() {
      if(!auth){
        return false;
      } else {
        NW.storage.appState.set("quantity", $("#quantity").val());
        return true;
      }
  });
});