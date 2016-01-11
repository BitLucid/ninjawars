$(function(){
    $("#bet").val(NW.storage.appState.get("bet", 1));

    $("#coin_flip").submit(function() {
        NW.storage.appState.set("bet", $("#bet").val());
        return true;
    });
});