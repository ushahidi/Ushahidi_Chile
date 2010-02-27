// MAPBOX LAYERS
jQuery(function() {
	map.addLayers(
	  [
	    new OpenLayers.Layer.MapBox('Haiti MMI Jan 20',
	      {
	        layername: 'haiti-mmi-20100120',
	        type: 'png',
	        visibility: false,
	        isBaseLayer: false,
	resolutions: [
	      1222.99245234375,
	      611.496226171875,
	      305.7481130859375,
	      152.87405654296876,
	      76.43702827148438,
	      38.21851413574219]
	      }
	    ),
	    new OpenLayers.Layer.MapBox('Haiti MMI Jan 13',
	      {
	        layername: 'haiti-mmi-20100113',
	        type: 'png',
	        visibility: false,
	        isBaseLayer: false,
	resolutions: [
	      1222.99245234375,
	      611.496226171875,
	      305.7481130859375,
	      152.87405654296876,
	      76.43702827148438,
	      38.21851413574219]
	      }
	    ),
	    new OpenLayers.Layer.MapBox('Haiti Obstacles',
	      {
	        layername: 'haiti-obstacles',
	        type: 'png',
	        visibility: false,
	        isBaseLayer: false
	      }
	    ),
	    new OpenLayers.Layer.MapBox('Haiti Fault Lines',
	      {
	        layername: 'haiti-fault-lines',
	        type: 'png',
	        visibility: false,
	        isBaseLayer: false,
	resolutions: [
	      1222.99245234375,
	      611.496226171875,
	      305.7481130859375,
	      152.87405654296876,
	      76.43702827148438,
	      38.21851413574219]
	      }
	    ),
	    new OpenLayers.Layer.MapBox('Haiti Roads',
	      {
	        layername: 'haiti-roads',
	        type: 'png',
	        isBaseLayer: false
	      }
	    ),
	    new OpenLayers.Layer.MapBox('Haiti Terrain',
	      {
	        layername: 'haiti-terrain',
	        type: 'jpg',
	resolutions: [
	      1222.99245234375,
	      611.496226171875,
	      305.7481130859375,
	      152.87405654296876,
	      76.43702827148438,
	      38.21851413574219,
	      19.109257067871095,
	      9.554628533935547,
	      4.777314266967774]
	      }
	    ),
	    new OpenLayers.Layer.MapBox('Haiti Terrain Grey',
	      {
	        layername: 'haiti-terrain-grey',
	        type: 'jpg',
	resolutions: [
	      1222.99245234375,
	      611.496226171875,
	      305.7481130859375,
	      152.87405654296876,
	      76.43702827148438,
	      38.21851413574219,
	      19.109257067871095,
	      9.554628533935547,
	      4.777314266967774]
	      }
	    )
	  ]
	);
});