    <style>
    {literal}
    #map td{
    	background-color:black;
    }
    #map td.grass{
    	background-color:green;
    }
    
    #map td.rice-field{
    	background-color:aqua;
    }
    
    #map table {
    	margin: 0 auto;
    }
    
    {/literal}
    </style>
    
    
    
    <div id='map'>
    <table style='width:310px;background-color:black'>
    	<thead>
    	<tr>
    		<td colspan='3' class='area-name' style='background-color:rgb(55, 10, 10);font-size:1.3em'>The Village</td>
    	</tr>
    	</thead>
    	<!-- <tfoot></tfoot> -->
    	<tbody>

{foreach name='foreach-row' from=$nodes item='row' key='ycoord'}
    	<tr>
{foreach name='foreach-over-row-of-nodes' from=$row item='node' key='xcoord'}
    		<td class='node-id-{$node.id} {$node.type} some-node-type-like-building' style='width:100px;height:120px'>	
    			<form action='map.php' method='post'>
    				<input type='hidden' name='ycoord' value='{$ycoord}>
    				<input type='hidden' name='xcoord' value='{$xcoord}>
    				<input type='submit' name='move' value='{if isset($node.name)}{$node.name}{else}Move{/if}'>
	{if isset($node.tile_image)}
	    <img src='/images/{$node.tile_image}' alt='' style='max-width:100px;max-height:100px'>
	{else}
		<div style='display:inline-block;width:100px;height:100px'>
		</div>
	{/if}
    			</form>
    			
    		<div class='details' style='height:20px;font-size:1.3em'>
    		
		      	{if $node.url}<a href='{$node.url|escape:'url'|escape}'>{/if}
			{if isset($node.image)}
		          <img src='/images/{$node.image|escape:'url'|escape}' alt='' style='width:8px;height:8px'>
			{/if}
		          {$node.name|escape}

		      	{if $node.url}</a>{/if}
    		
    		</div>

    		
    		</td>
{/foreach}
    	</tr>
{/foreach}
	</tbody>
	</table>
    

{*    
    <ul style='margin: .5em auto;text-align:center;font-size:1.3em;'>
{foreach name="looploc" from=$locations item="loc" key="idx"}
      <li style='padding-left:8px'>
      	<a href='{$loc.url|escape}'>
	{if isset($loc.tile_image)}
	    <img src='/images/{$loc.tile_image}' alt='' style='max-width:100px;max-height:100px'>
	{/if}
	{if isset($loc.image)}
          <img src='/images/{$loc.image|escape:'url'|escape}' alt='' style='width:8px;height:8px'>
	{/if}
          {$loc.name|escape}
      	</a>
      </li>
{/foreach}
    </ul>
  
*}

	</div> <!-- End of map div -->
