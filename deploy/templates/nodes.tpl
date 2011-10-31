    <style>
    {literal}
    
    #map table {
    	margin: 0 auto;
    }
    
    #map td{
    	background-color:black;
    }
    
    #map td.doshin {
    	background-image:url(/images/nodes/node-tiles.png);
    	background-position:0px 0px;
    	background-repeat:no-repeat;
    }
    
    #map td.dojo {
    	background-image:url(/images/nodes/node-tiles.png);
    	background-position:0px -111px;
    	background-repeat:no-repeat;
    }
    
    #map td.shrine {
    	background-image:url(/images/nodes/node-tiles.png);
    	background-position:0px -227px;
    	background-repeat:no-repeat;
    }
    
    #map td.rice-field {
    	background-image:url(/images/nodes/node-tiles.png);
    	background-position:0px -342px;
    	background-repeat:no-repeat;
    }
    
    #map td.casino {
    	background-image:url(/images/nodes/node-tiles.png);
    	background-position:0px -444px;
    	background-repeat:no-repeat;
    }
    
    #map td.weapons-shop {
    	background-image:url(/images/nodes/node-tiles.png);
    	background-position:0px -558px;
    	background-repeat:no-repeat;
    }
    
    #map td.grass{
    	background-color:rgb(30,60,0);
    }
    
    #map td.rice-field{
    	background-color:rgb(12,44,44);
    }
    
    {/literal}
    </style>
    
    
    <div id='map'>
    <table style='width:510px;background-color:black'>
    	<thead>
    	<tr>
    		<td colspan='5' class='area-name' style='background-color:rgb(55, 10, 10);font-size:1.3em;text-align:center'>The Village</td>
    	</tr>
    	</thead>
    	<!-- <tfoot></tfoot> -->
    	<tbody>

{foreach name='foreach-row' from=$nodes item='row' key='ycoord'}
    	<tr>
{foreach name='foreach-over-row-of-nodes' from=$row item='node' key='xcoord'}
    		<td class='node-id-{$node.id} {$node.type}' style='width:100px;height:100px;margin:0;padding:0'>	
    			<form action='map.php' method='post'>
    				<input type='hidden' name='ycoord' value='{$ycoord}>
    				<input type='hidden' name='xcoord' value='{$xcoord}>
    				<input type='submit' name='move' value='{if isset($node.name)}{$node.name}{else}Go{/if}'>
    			</form>
    			
				<div class='details' style='width:66%;text-align:center;height:2.5em;font-size:1.1em;background-color:black;background-color:rgba(0,0,0,.8);'>
				
				  	{if $node.url}<a href='{$node.url|escape:'url'|escape}' target='main'>{/if}
				{if isset($node.image)}
				      <img src='/images/{$node.image|escape:'url'|escape}' alt='' style='width:8px;height:8px'>
				{/if}
				      {if $node.url}Enter {/if}
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
