<link rel="stylesheet" property="stylesheet" href="css/map.css">

<div id='map'>
    <section class='map-grid'>
        {foreach name='foreach_row' from=$nodes item='row' key='ycoord'}
            <!-- Row start -->
            {foreach name='foreach_over_row_of_nodes' from=$row item='node' key='xcoord'}
                <div class='node-id-{$node.id} {$node.type} node'>
                    <form action='/map' method='post'>
                        <input type='hidden' name='ycoord' value='{$ycoord}>
						<input type=' hidden' name='xcoord' value='{$xcoord}>
        				<input type=' submit' name='move' value='{if isset($node.name)}{$node.name}{else}Go{/if}'>
                    </form>
                    <div class='details'>
                        {if isset($node.url) && $node.url}<a href='{$node.url|escape:'url'|escape}' target='main'>{/if}
                            {if isset($node.image)}
                                <img src='/images/{$node.image|escape:'url'|escape}' alt='' style='width:8px;height:8px'>
                            {/if}
                            {if isset($node.icon)}
                                <i class='{$node.icon}'></i>
                            {/if}
                            {if isset($node.icon_raw)}
                                {$node.icon_raw}
                            {/if}
                            {$node.name|escape}
                        {if isset($node.url) && $node.url}</a>{/if}
                    </div>
                </div>
            {/foreach}
            <!-- Row end -->
        {/foreach}
    </section>
</div> <!-- End of map div -->