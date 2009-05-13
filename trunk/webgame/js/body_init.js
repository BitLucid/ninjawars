$(document).ready(function() {
   
   /* Collapse the following parts of the index */
    $("#links-menu").toggle();
    
    /* Expand the chat when it's clicked. */
    $("#expand-chat").click(function () {
        $("#mini-chat-frame-container").removeClass('chat-collapsed').addClass('chat-expanded');/*.height(780);*/
        $(this).hide();  // Hide the clicked section after expansion.
    });
    
    
    //$("a[target]").hide(); /* Hide all links using target */
    
    /*
    function addClickHandlers() {
        $("a.remote", this).click(function() {
            $("#target").load(this.href, addClickHandlers);
        });
    }
    $(document).ready(addClickHandlers);
    */
   
 });
