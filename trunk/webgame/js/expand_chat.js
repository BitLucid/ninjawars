function expand_collapse_mini_chat() {
    var e = document.getElementById('mini-chat-frame-container');
    /*alert(print_r(e.style.height, 1));*/
    /*var display = e.style.display;
    document.writeln(' '+display+' ');*/
    if(e.style.height == '' || e.style.height == '880px')
        e.style.height = '400px';
    else
        e.style.height = '880px';
        
    /*simple_var_dump(e);*/
    return true;
}
