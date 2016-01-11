$(function() {
    $('#kick_form').submit(function(){return confirm('Are you sure you want to kick this player?');});


    /*
       because some browsers store all values as strings, we need to store
       booleans as string representations of 1 and 0. We then need to get
       the int value upon retrieval
    */
    if(typeof(attacking_possible) !== null && attacking_possible){
        $("#duel").prop('checked', parseInt(NW.storage.appState.get("duel_checked", false)));

        for (i = 0; i < combat_skills.length; i++) {
            $("#"+combat_skills[i].skill_internal_name).prop('checked',
                parseInt(NW.storage.appState.get(combat_skills[i].skill_internal_name+"_checked", false))
            );
        }

        $("#attack_player").submit(function() {
            // the unary + operator converts the boolean to an int
            NW.storage.appState.set("duel_checked", +$("#duel").prop('checked'));

            for (i = 0; i < combat_skills.length; i++) {
                NW.storage.appState.set(
                    combat_skills[i].skill_internal_name+"_checked",
                    +$("#"+combat_skills[i].skill_internal_name).prop('checked')
                );
            }

            return true;
        });
    }

    var lastItemUsed = NW.storage.appState.get("last_item_used");

    if ($("#item option[value='"+lastItemUsed+"']").length) {
        $("#item").val(lastItemUsed);
    } else {
        $("#item").val($("#item option:first-child").val());
    }

    $("#inventory_form").submit(function() {
        NW.storage.appState.set("last_item_used", $("#item").val());
    });


});