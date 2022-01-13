<?php

/**
 * This file is part of the Colissimo Delivery Integration plugin.
 * (c) Halyra
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/****************************************************************************************/
/* Cdi Référence aux livraisons - js for Open Map                                                */
/****************************************************************************************/
     
       $jsmap = ' <div id="' . $parammap['id'] . '" style="width:' . $parammap['w'] . ';height:' . $parammap['h'] . ';' . $parammap['style'] . ' "><div id="mapom" style="height:100%; width:100%; position:absolute; top:0px; left:0px;"></div></div><br/>' ;
       $jsmap .=   "<script>  var sites = " . $listsites . " ;" ; 
       $jsmap .=  '
var listFeatures = [];
for (var i = 0; i < sites.length; i++) {
  var site = sites[i]; 
  var iconFeature = new ol.Feature({
    geometry: new ol.geom.Point(ol.proj.fromLonLat([site[1], site[0]]),),
    name: "<div style=\'display:inline-block; height:200px ; width:400px; overflow:scroll; z-index:1; \'>"+site[2]+"</div><button id=\'close\' style=\'float:right; padding:5px; \' onclick=\'suppopover(); return false;\'>X</button>", 
  });
  if(site[3]!=null) { 
    iconFeature.setStyle(new ol.style.Style({
      image: new ol.style.Icon({
        anchor: [0.5, 46],
        anchorXUnits: "fraction",
        anchorYUnits: "pixels",
        src: site[3],
      })
    }));
    listFeatures.push(iconFeature) ;
  }
} 

var iconLayerSource = new ol.source.Vector({
  features: listFeatures,
});
var iconLayer = new ol.layer.Vector({
  source: iconLayerSource,
});

var map = new ol.Map({
  target: "mapom",
  layers: [
    new ol.layer.Tile({
      source: new ol.source.OSM()
    }),
    iconLayer,
  ],
  view: new ol.View({
    center: ol.proj.fromLonLat([' . $parammap['lon'] . ', ' . $parammap['lat'] . ']),
    zoom: ' . $parammap['z'] . '
  })
});
jQuery( "#cdiompopup" ).each(function( index ) { 
  jQuery(this).remove();
});
var createcdiompopup = document.createElement("div");
createcdiompopup.id = "cdiompopup";
document.body.appendChild(createcdiompopup);
jQuery(document).ready(function ($) {
  var pagex ;
  var pagey ;
  $(document).on("mousemove", function(mulot) {
    pagex = mulot.pageX ;
    pagey = mulot.pageY ;
  });
  $(document).on("click", function(brosswagon) {
    function cleaning() {
      var isitmapom = document.getElementById("mapom");     
      if (!isitmapom) {
        jQuery( "#cdiompopup" ).each(function( index ) { 
          jQuery(this).remove();
        });
      }
    }
    setTimeout(cleaning, 2000) ;
  });
  map.on("click", function(evt) {
    var element = document.getElementById("cdiompopup");
    var cdiompopup = new ol.Overlay({
      element: element,
      positioning: "bottom-center",
      stopEvent: true,
      offset: [0, -50]
    });
    map.addOverlay(cdiompopup);
    var feature = map.forEachFeatureAtPixel(evt.pixel,
      function(feature) {
        return feature;
      });
    if (feature) {
      var coordinates = feature.getGeometry().getCoordinates();
      cdiompopup.setPosition(coordinates);
      var posittop = pagey - 250 ;
      var positleft = pagex - 218;
      document.body.appendChild(element);
      element.style ="top:"+posittop+"px; left:"+positleft+"px; height:230px; width:435px; position:absolute; background-color:white; padding:3px; border:1px solid black; border-radius: 5px;" ;
      var htmlcontent = feature.get("name");
      $(element).html("<div>"+htmlcontent+"<div style=\"margin : 0 auto; width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-top: 40px solid red;\" ></div></div>");
      $(element).show();
    }else{
      suppopover();
    }
    map.on("pointermove", function(e) {
      if (e.dragging) {
        return;
      }
      //suppopover();
    });
  });
});
function suppopover() {
  jQuery(document).ready(function ($){
    var element = document.getElementById("cdiompopup");
    $(element).hide();
  });
}
</script>' ;

?>

