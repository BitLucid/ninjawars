$(function() {
  
  $("#quantity").val(NW.storage.appState.get("quantity", 1));

  $("#shop_form").submit(function() {
      if(typeOf(loggedIn) === 'undefined' || !loggedIn){
        return false;
      }
      NW.storage.appState.set("quantity", $("#quantity").val());
      return true;
  });
});