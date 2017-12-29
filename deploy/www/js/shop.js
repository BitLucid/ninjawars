/* Store the shop settings */
/*jshint browser: true, white: true, plusplus: true*/
/*global $, NW */
$(function() {
  'use strict';

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