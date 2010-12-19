<span class='generic-indicator' style='position:relative;display:inline-block;width:100%' title='{$title|default:''}'>
  <a href='{$action}' target='main' style='font-weight:normal'>
    <span class='bar-border' style='width:100%;border: 1px solid {$bar_color|default:'#003399'};display:inline-block;text-align:left'>
        <span class='bar' style="width:{$bar_percent}%;background-color: {$bar_color|default:'#003399'};display:inline-block;">
          &nbsp;
        </span>
    </span>
    <span class='bar-text' style='position:absolute;top:0;left:1em;color:whitesmoke;display:inline-block;text-shadow: 2px 2px 2px #000;'>
        <span class='bar-number'>{$number}</span>
        <span class='bar-number-of-word'>{$number_of|default:''}</span>
        
        
		<span style='color:crimson;font-weight:bolder;display:none'>{$zero_word|default:'0'}</span>
    </span>
  </a>
</span>
