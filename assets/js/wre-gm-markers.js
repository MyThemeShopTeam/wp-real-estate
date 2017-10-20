/*
 * WP Real Estate plugin by MyThemeShop
 * https://wordpress.com/plugins/wp-real-estate/
 */
(function() {

	if(jQuery('#wre-advanced-map').length <= 0) {
		return false;
	}

	function ClusterIcon(a,b){a.getMarkerClusterer().extend(ClusterIcon,google.maps.OverlayView),this.cluster_=a,this.className_=a.getMarkerClusterer().getClusterClass(),this.styles_=b,this.center_=null,this.div_=null,this.sums_=null,this.visible_=!1,this.setMap(a.getMap())}function Cluster(a){this.markerClusterer_=a,this.map_=a.getMap(),this.gridSize_=a.getGridSize(),this.minClusterSize_=a.getMinimumClusterSize(),this.averageCenter_=a.getAverageCenter(),this.markers_=[],this.center_=null,this.bounds_=null,this.clusterIcon_=new ClusterIcon(this,a.getStyles())}function MarkerClusterer(a,b,c){this.extend(MarkerClusterer,google.maps.OverlayView),b=b||[],c=c||{},this.markers_=[],this.clusters_=[],this.listeners_=[],this.activeMap_=null,this.ready_=!1,this.gridSize_=c.gridSize||60,this.minClusterSize_=c.minimumClusterSize||2,this.maxZoom_=c.maxZoom||null,this.styles_=c.styles||[],this.title_=c.title||"",this.zoomOnClick_=!0,void 0!==c.zoomOnClick&&(this.zoomOnClick_=c.zoomOnClick),this.averageCenter_=!1,void 0!==c.averageCenter&&(this.averageCenter_=c.averageCenter),this.ignoreHidden_=!1,void 0!==c.ignoreHidden&&(this.ignoreHidden_=c.ignoreHidden),this.enableRetinaIcons_=!1,void 0!==c.enableRetinaIcons&&(this.enableRetinaIcons_=c.enableRetinaIcons),this.imagePath_=c.imagePath||MarkerClusterer.IMAGE_PATH,this.imageExtension_=c.imageExtension||MarkerClusterer.IMAGE_EXTENSION,this.imageSizes_=c.imageSizes||MarkerClusterer.IMAGE_SIZES,this.calculator_=c.calculator||MarkerClusterer.CALCULATOR,this.batchSize_=c.batchSize||MarkerClusterer.BATCH_SIZE,this.batchSizeIE_=c.batchSizeIE||MarkerClusterer.BATCH_SIZE_IE,this.clusterClass_=c.clusterClass||"cluster",-1!==navigator.userAgent.toLowerCase().indexOf("msie")&&(this.batchSize_=this.batchSizeIE_),this.setupStyles_(),this.addMarkers(b,!0),this.setMap(a)}function InfoBox(a){a=a||{},google.maps.OverlayView.apply(this,arguments),this.content_=a.content||"",this.disableAutoPan_=a.disableAutoPan||!1,this.maxWidth_=a.maxWidth||0,this.pixelOffset_=a.pixelOffset||new google.maps.Size(0,0),this.position_=a.position||new google.maps.LatLng(0,0),this.zIndex_=a.zIndex||null,this.boxClass_=a.boxClass||"infoBox",this.boxStyle_=a.boxStyle||{},this.closeBoxMargin_=a.closeBoxMargin||"2px",this.closeBoxURL_=a.closeBoxURL||"http://www.google.com/intl/en_us/mapfiles/close.gif",""===a.closeBoxURL&&(this.closeBoxURL_=""),this.infoBoxClearance_=a.infoBoxClearance||new google.maps.Size(1,1),"undefined"==typeof a.visible&&(a.visible="undefined"==typeof a.isHidden?!0:!a.isHidden),this.isHidden_=!a.visible,this.alignBottom_=a.alignBottom||!1,this.pane_=a.pane||"floatPane",this.enableEventPropagation_=a.enableEventPropagation||!1,this.div_=null,this.closeListener_=null,this.moveListener_=null,this.contextListener_=null,this.eventListeners_=null,this.fixedWidthSet_=null}ClusterIcon.prototype.onAdd=function(){var a,b,c=this;this.div_=document.createElement("div"),this.div_.className=this.className_,this.visible_&&this.show(),this.getPanes().overlayMouseTarget.appendChild(this.div_),this.boundsChangedListener_=google.maps.event.addListener(this.getMap(),"bounds_changed",function(){b=a}),google.maps.event.addDomListener(this.div_,"mousedown",function(){a=!0,b=!1}),google.maps.event.addDomListener(this.div_,"click",function(d){if(a=!1,!b){var e,f,g=c.cluster_.getMarkerClusterer();google.maps.event.trigger(g,"click",c.cluster_),google.maps.event.trigger(g,"clusterclick",c.cluster_),g.getZoomOnClick()&&(f=g.getMaxZoom(),e=c.cluster_.getBounds(),g.getMap().fitBounds(e),setTimeout(function(){g.getMap().fitBounds(e),null!==f&&g.getMap().getZoom()>f&&g.getMap().setZoom(f+1)},100)),d.cancelBubble=!0,d.stopPropagation&&d.stopPropagation()}}),google.maps.event.addDomListener(this.div_,"mouseover",function(){var a=c.cluster_.getMarkerClusterer();google.maps.event.trigger(a,"mouseover",c.cluster_)}),google.maps.event.addDomListener(this.div_,"mouseout",function(){var a=c.cluster_.getMarkerClusterer();google.maps.event.trigger(a,"mouseout",c.cluster_)})},ClusterIcon.prototype.onRemove=function(){this.div_&&this.div_.parentNode&&(this.hide(),google.maps.event.removeListener(this.boundsChangedListener_),google.maps.event.clearInstanceListeners(this.div_),this.div_.parentNode.removeChild(this.div_),this.div_=null)},ClusterIcon.prototype.draw=function(){if(this.visible_){var a=this.getPosFromLatLng_(this.center_);this.div_.style.top=a.y+"px",this.div_.style.left=a.x+"px"}},ClusterIcon.prototype.hide=function(){this.div_&&(this.div_.style.display="none"),this.visible_=!1},ClusterIcon.prototype.show=function(){if(this.div_){var a="",b=this.backgroundPosition_.split(" "),c=parseInt(b[0].replace(/^\s+|\s+$/g,""),10),d=parseInt(b[1].replace(/^\s+|\s+$/g,""),10),e=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(e),a="<img src='"+this.url_+"' style='position: absolute; top: "+d+"px; left: "+c+"px; ",this.cluster_.getMarkerClusterer().enableRetinaIcons_||(a+="clip: rect("+-1*d+"px, "+(-1*c+this.width_)+"px, "+(-1*d+this.height_)+"px, "+-1*c+"px);"),a+="'>",this.div_.innerHTML=a+"<div style='position: absolute;top: "+this.anchorText_[0]+"px;left: "+this.anchorText_[1]+"px;color: "+this.textColor_+";font-size: "+this.textSize_+"px;font-family: "+this.fontFamily_+";font-weight: "+this.fontWeight_+";font-style: "+this.fontStyle_+";text-decoration: "+this.textDecoration_+";text-align: center;width: "+this.width_+"px;line-height:"+this.height_+"px;'>"+this.sums_.text+"</div>",this.div_.title="undefined"==typeof this.sums_.title||""===this.sums_.title?this.cluster_.getMarkerClusterer().getTitle():this.sums_.title,this.div_.style.display=""}this.visible_=!0},ClusterIcon.prototype.useStyle=function(a){this.sums_=a;var b=Math.max(0,a.index-1);b=Math.min(this.styles_.length-1,b);var c=this.styles_[b];this.url_=c.url,this.height_=c.height,this.width_=c.width,this.anchorText_=c.anchorText||[0,0],this.anchorIcon_=c.anchorIcon||[parseInt(this.height_/2,10),parseInt(this.width_/2,10)],this.textColor_=c.textColor||"black",this.textSize_=c.textSize||11,this.textDecoration_=c.textDecoration||"none",this.fontWeight_=c.fontWeight||"bold",this.fontStyle_=c.fontStyle||"normal",this.fontFamily_=c.fontFamily||"Arial,sans-serif",this.backgroundPosition_=c.backgroundPosition||"0 0"},ClusterIcon.prototype.setCenter=function(a){this.center_=a},ClusterIcon.prototype.createCss=function(a){var b=[];return b.push("cursor: pointer;"),b.push("position: absolute; top: "+a.y+"px; left: "+a.x+"px;"),b.push("width: "+this.width_+"px; height: "+this.height_+"px;"),b.join("")},ClusterIcon.prototype.getPosFromLatLng_=function(a){var b=this.getProjection().fromLatLngToDivPixel(a);return b.x-=this.anchorIcon_[1],b.y-=this.anchorIcon_[0],b.x=parseInt(b.x,10),b.y=parseInt(b.y,10),b},Cluster.prototype.getSize=function(){return this.markers_.length},Cluster.prototype.getMarkers=function(){return this.markers_},Cluster.prototype.getCenter=function(){return this.center_},Cluster.prototype.getMap=function(){return this.map_},Cluster.prototype.getMarkerClusterer=function(){return this.markerClusterer_},Cluster.prototype.getBounds=function(){var a,b=new google.maps.LatLngBounds(this.center_,this.center_),c=this.getMarkers();for(a=0;a<c.length;a++)b.extend(c[a].getPosition());return b},Cluster.prototype.remove=function(){this.clusterIcon_.setMap(null),this.markers_=[],delete this.markers_},Cluster.prototype.addMarker=function(a){var b,c,d;if(this.isMarkerAlreadyAdded_(a))return!1;if(this.center_){if(this.averageCenter_){var e=this.markers_.length+1,f=(this.center_.lat()*(e-1)+a.getPosition().lat())/e,g=(this.center_.lng()*(e-1)+a.getPosition().lng())/e;this.center_=new google.maps.LatLng(f,g),this.calculateBounds_()}}else this.center_=a.getPosition(),this.calculateBounds_();if(a.isAdded=!0,this.markers_.push(a),c=this.markers_.length,d=this.markerClusterer_.getMaxZoom(),null!==d&&this.map_.getZoom()>d)a.getMap()!==this.map_&&a.setMap(this.map_);else if(c<this.minClusterSize_)a.getMap()!==this.map_&&a.setMap(this.map_);else if(c===this.minClusterSize_)for(b=0;c>b;b++)this.markers_[b].setMap(null);else a.setMap(null);return this.updateIcon_(),!0},Cluster.prototype.isMarkerInClusterBounds=function(a){return this.bounds_.contains(a.getPosition())},Cluster.prototype.calculateBounds_=function(){var a=new google.maps.LatLngBounds(this.center_,this.center_);this.bounds_=this.markerClusterer_.getExtendedBounds(a)},Cluster.prototype.updateIcon_=function(){var a=this.markers_.length,b=this.markerClusterer_.getMaxZoom();if(null!==b&&this.map_.getZoom()>b)return void this.clusterIcon_.hide();if(a<this.minClusterSize_)return void this.clusterIcon_.hide();var c=this.markerClusterer_.getStyles().length,d=this.markerClusterer_.getCalculator()(this.markers_,c);this.clusterIcon_.setCenter(this.center_),this.clusterIcon_.useStyle(d),this.clusterIcon_.show()},Cluster.prototype.isMarkerAlreadyAdded_=function(a){var b;if(this.markers_.indexOf)return-1!==this.markers_.indexOf(a);for(b=0;b<this.markers_.length;b++)if(a===this.markers_[b])return!0;return!1},MarkerClusterer.prototype.onAdd=function(){var a=this;this.activeMap_=this.getMap(),this.ready_=!0,this.repaint(),this.listeners_=[google.maps.event.addListener(this.getMap(),"zoom_changed",function(){a.resetViewport_(!1),(this.getZoom()===(this.get("minZoom")||0)||this.getZoom()===this.get("maxZoom"))&&google.maps.event.trigger(this,"idle")}),google.maps.event.addListener(this.getMap(),"idle",function(){a.redraw_()})]},MarkerClusterer.prototype.onRemove=function(){var a;for(a=0;a<this.markers_.length;a++)this.markers_[a].getMap()!==this.activeMap_&&this.markers_[a].setMap(this.activeMap_);for(a=0;a<this.clusters_.length;a++)this.clusters_[a].remove();for(this.clusters_=[],a=0;a<this.listeners_.length;a++)google.maps.event.removeListener(this.listeners_[a]);this.listeners_=[],this.activeMap_=null,this.ready_=!1},MarkerClusterer.prototype.draw=function(){},MarkerClusterer.prototype.setupStyles_=function(){var a,b;if(!(this.styles_.length>0))for(a=0;a<this.imageSizes_.length;a++)b=this.imageSizes_[a],this.styles_.push({url:this.imagePath_+(a+1)+"."+this.imageExtension_,height:b,width:b})},MarkerClusterer.prototype.fitMapToMarkers=function(){var a,b=this.getMarkers(),c=new google.maps.LatLngBounds;for(a=0;a<b.length;a++)c.extend(b[a].getPosition());this.getMap().fitBounds(c)},MarkerClusterer.prototype.getGridSize=function(){return this.gridSize_},MarkerClusterer.prototype.setGridSize=function(a){this.gridSize_=a},MarkerClusterer.prototype.getMinimumClusterSize=function(){return this.minClusterSize_},MarkerClusterer.prototype.setMinimumClusterSize=function(a){this.minClusterSize_=a},MarkerClusterer.prototype.getMaxZoom=function(){return this.maxZoom_},MarkerClusterer.prototype.setMaxZoom=function(a){this.maxZoom_=a},MarkerClusterer.prototype.getStyles=function(){return this.styles_},MarkerClusterer.prototype.setStyles=function(a){this.styles_=a},MarkerClusterer.prototype.getTitle=function(){return this.title_},MarkerClusterer.prototype.setTitle=function(a){this.title_=a},MarkerClusterer.prototype.getZoomOnClick=function(){return this.zoomOnClick_},MarkerClusterer.prototype.setZoomOnClick=function(a){this.zoomOnClick_=a},MarkerClusterer.prototype.getAverageCenter=function(){return this.averageCenter_},MarkerClusterer.prototype.setAverageCenter=function(a){this.averageCenter_=a},MarkerClusterer.prototype.getIgnoreHidden=function(){return this.ignoreHidden_},MarkerClusterer.prototype.setIgnoreHidden=function(a){this.ignoreHidden_=a},MarkerClusterer.prototype.getEnableRetinaIcons=function(){return this.enableRetinaIcons_},MarkerClusterer.prototype.setEnableRetinaIcons=function(a){this.enableRetinaIcons_=a},MarkerClusterer.prototype.getImageExtension=function(){return this.imageExtension_},MarkerClusterer.prototype.setImageExtension=function(a){this.imageExtension_=a},MarkerClusterer.prototype.getImagePath=function(){return this.imagePath_},MarkerClusterer.prototype.setImagePath=function(a){this.imagePath_=a},MarkerClusterer.prototype.getImageSizes=function(){return this.imageSizes_},MarkerClusterer.prototype.setImageSizes=function(a){this.imageSizes_=a},MarkerClusterer.prototype.getCalculator=function(){return this.calculator_},MarkerClusterer.prototype.setCalculator=function(a){this.calculator_=a},MarkerClusterer.prototype.getBatchSizeIE=function(){return this.batchSizeIE_},MarkerClusterer.prototype.setBatchSizeIE=function(a){this.batchSizeIE_=a},MarkerClusterer.prototype.getClusterClass=function(){return this.clusterClass_},MarkerClusterer.prototype.setClusterClass=function(a){this.clusterClass_=a},MarkerClusterer.prototype.getMarkers=function(){return this.markers_},MarkerClusterer.prototype.getTotalMarkers=function(){return this.markers_.length},MarkerClusterer.prototype.getClusters=function(){return this.clusters_},MarkerClusterer.prototype.getTotalClusters=function(){return this.clusters_.length},MarkerClusterer.prototype.addMarker=function(a,b){this.pushMarkerTo_(a),b||this.redraw_()},MarkerClusterer.prototype.addMarkers=function(a,b){var c;for(c in a)a.hasOwnProperty(c)&&this.pushMarkerTo_(a[c]);b||this.redraw_()},MarkerClusterer.prototype.pushMarkerTo_=function(a){if(a.getDraggable()){var b=this;google.maps.event.addListener(a,"dragend",function(){b.ready_&&(this.isAdded=!1,b.repaint())})}a.isAdded=!1,this.markers_.push(a)},MarkerClusterer.prototype.removeMarker=function(a,b){var c=this.removeMarker_(a);return!b&&c&&this.repaint(),c},MarkerClusterer.prototype.removeMarkers=function(a,b){var c,d,e=!1;for(c=0;c<a.length;c++)d=this.removeMarker_(a[c]),e=e||d;return!b&&e&&this.repaint(),e},MarkerClusterer.prototype.removeMarker_=function(a){var b,c=-1;if(this.markers_.indexOf)c=this.markers_.indexOf(a);else for(b=0;b<this.markers_.length;b++)if(a===this.markers_[b]){c=b;break}return-1===c?!1:(a.setMap(null),this.markers_.splice(c,1),!0)},MarkerClusterer.prototype.clearMarkers=function(){this.resetViewport_(!0),this.markers_=[]},MarkerClusterer.prototype.repaint=function(){var a=this.clusters_.slice();this.clusters_=[],this.resetViewport_(!1),this.redraw_(),setTimeout(function(){var b;for(b=0;b<a.length;b++)a[b].remove()},0)},MarkerClusterer.prototype.getExtendedBounds=function(a){var b=this.getProjection(),c=new google.maps.LatLng(a.getNorthEast().lat(),a.getNorthEast().lng()),d=new google.maps.LatLng(a.getSouthWest().lat(),a.getSouthWest().lng()),e=b.fromLatLngToDivPixel(c);e.x+=this.gridSize_,e.y-=this.gridSize_;var f=b.fromLatLngToDivPixel(d);f.x-=this.gridSize_,f.y+=this.gridSize_;var g=b.fromDivPixelToLatLng(e),h=b.fromDivPixelToLatLng(f);return a.extend(g),a.extend(h),a},MarkerClusterer.prototype.redraw_=function(){this.createClusters_(0)},MarkerClusterer.prototype.resetViewport_=function(a){var b,c;for(b=0;b<this.clusters_.length;b++)this.clusters_[b].remove();for(this.clusters_=[],b=0;b<this.markers_.length;b++)c=this.markers_[b],c.isAdded=!1,a&&c.setMap(null)},MarkerClusterer.prototype.distanceBetweenPoints_=function(a,b){var c=6371,d=(b.lat()-a.lat())*Math.PI/180,e=(b.lng()-a.lng())*Math.PI/180,f=Math.sin(d/2)*Math.sin(d/2)+Math.cos(a.lat()*Math.PI/180)*Math.cos(b.lat()*Math.PI/180)*Math.sin(e/2)*Math.sin(e/2),g=2*Math.atan2(Math.sqrt(f),Math.sqrt(1-f)),h=c*g;return h},MarkerClusterer.prototype.isMarkerInBounds_=function(a,b){return b.contains(a.getPosition())},MarkerClusterer.prototype.addToClosestCluster_=function(a){var b,c,d,e,f=4e4,g=null;for(b=0;b<this.clusters_.length;b++)d=this.clusters_[b],e=d.getCenter(),e&&(c=this.distanceBetweenPoints_(e,a.getPosition()),f>c&&(f=c,g=d));g&&g.isMarkerInClusterBounds(a)?g.addMarker(a):(d=new Cluster(this),d.addMarker(a),this.clusters_.push(d))},MarkerClusterer.prototype.createClusters_=function(a){var b,c,d,e=this;if(this.ready_){0===a&&(google.maps.event.trigger(this,"clusteringbegin",this),"undefined"!=typeof this.timerRefStatic&&(clearTimeout(this.timerRefStatic),delete this.timerRefStatic)),d=this.getMap().getZoom()>3?new google.maps.LatLngBounds(this.getMap().getBounds().getSouthWest(),this.getMap().getBounds().getNorthEast()):new google.maps.LatLngBounds(new google.maps.LatLng(85.02070771743472,-178.48388434375),new google.maps.LatLng(-85.08136444384544,178.00048865625));var f=this.getExtendedBounds(d),g=Math.min(a+this.batchSize_,this.markers_.length);for(b=a;g>b;b++)c=this.markers_[b],!c.isAdded&&this.isMarkerInBounds_(c,f)&&(!this.ignoreHidden_||this.ignoreHidden_&&c.getVisible())&&this.addToClosestCluster_(c);g<this.markers_.length?this.timerRefStatic=setTimeout(function(){e.createClusters_(g)},0):(delete this.timerRefStatic,google.maps.event.trigger(this,"clusteringend",this))}},MarkerClusterer.prototype.extend=function(a,b){return function(a){var b;for(b in a.prototype)this.prototype[b]=a.prototype[b];return this}.apply(a,[b])},MarkerClusterer.CALCULATOR=function(a,b){for(var c=0,d="",e=a.length.toString(),f=e;0!==f;)f=parseInt(f/10,10),c++;return c=Math.min(c,b),{text:e,index:c,title:d}},MarkerClusterer.BATCH_SIZE=2e3,MarkerClusterer.BATCH_SIZE_IE=500,MarkerClusterer.IMAGE_PATH="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclustererplus/images/m",MarkerClusterer.IMAGE_EXTENSION="png",MarkerClusterer.IMAGE_SIZES=[53,56,66,78,90],InfoBox.prototype=new google.maps.OverlayView,InfoBox.prototype.createInfoBoxDiv_=function(){var a,b,c,d=this,e=function(a){a.cancelBubble=!0,a.stopPropagation&&a.stopPropagation()},f=function(a){a.returnValue=!1,a.preventDefault&&a.preventDefault(),d.enableEventPropagation_||e(a)};if(!this.div_){if(this.div_=document.createElement("div"),this.setBoxStyle_(),"undefined"==typeof this.content_.nodeType?this.div_.innerHTML=this.getCloseBoxImg_()+this.content_:(this.div_.innerHTML=this.getCloseBoxImg_(),this.div_.appendChild(this.content_)),this.getPanes()[this.pane_].appendChild(this.div_),this.addClickHandler_(),this.div_.style.width?this.fixedWidthSet_=!0:0!==this.maxWidth_&&this.div_.offsetWidth>this.maxWidth_?(this.div_.style.width=this.maxWidth_,this.div_.style.overflow="auto",this.fixedWidthSet_=!0):(c=this.getBoxWidths_(),this.div_.style.width=this.div_.offsetWidth-c.left-c.right+"px",this.fixedWidthSet_=!1),this.panBox_(this.disableAutoPan_),!this.enableEventPropagation_){for(this.eventListeners_=[],b=["mousedown","mouseover","mouseout","mouseup","click","dblclick","touchstart","touchend","touchmove"],a=0;a<b.length;a++)this.eventListeners_.push(google.maps.event.addDomListener(this.div_,b[a],e));this.eventListeners_.push(google.maps.event.addDomListener(this.div_,"mouseover",function(){this.style.cursor="default"}))}this.contextListener_=google.maps.event.addDomListener(this.div_,"contextmenu",f),google.maps.event.trigger(this,"domready")}},InfoBox.prototype.getCloseBoxImg_=function(){var a="";return""!==this.closeBoxURL_&&(a="<img",a+=" src='"+this.closeBoxURL_+"'",a+=" align=right",a+=" style='",a+=" position: relative;",a+=" cursor: pointer;",a+=" margin: "+this.closeBoxMargin_+";",a+="'>"),a},InfoBox.prototype.addClickHandler_=function(){var a;""!==this.closeBoxURL_?(a=this.div_.firstChild,this.closeListener_=google.maps.event.addDomListener(a,"click",this.getCloseClickHandler_())):this.closeListener_=null},InfoBox.prototype.getCloseClickHandler_=function(){var a=this;return function(b){b.cancelBubble=!0,b.stopPropagation&&b.stopPropagation(),google.maps.event.trigger(a,"closeclick"),a.close()}},InfoBox.prototype.panBox_=function(a){var b,c,d=0,e=0;if(!a&&(b=this.getMap(),b instanceof google.maps.Map)){b.getBounds().contains(this.position_)||b.setCenter(this.position_),c=b.getBounds();var f=b.getDiv(),g=f.offsetWidth,h=f.offsetHeight,i=this.pixelOffset_.width,j=this.pixelOffset_.height,k=this.div_.offsetWidth,l=this.div_.offsetHeight,m=this.infoBoxClearance_.width,n=this.infoBoxClearance_.height,o=this.getProjection().fromLatLngToContainerPixel(this.position_);if(o.x<-i+m?d=o.x+i-m:o.x+k+i+m>g&&(d=o.x+k+i+m-g),this.alignBottom_?o.y<-j+n+l?e=o.y+j-n-l:o.y+j+n>h&&(e=o.y+j+n-h):o.y<-j+n?e=o.y+j-n:o.y+l+j+n>h&&(e=o.y+l+j+n-h),0!==d||0!==e){{b.getCenter()}b.panBy(d,e)}}},InfoBox.prototype.setBoxStyle_=function(){var a,b;if(this.div_){this.div_.className=this.boxClass_,this.div_.style.cssText="",b=this.boxStyle_;for(a in b)b.hasOwnProperty(a)&&(this.div_.style[a]=b[a]);this.div_.style.WebkitTransform="translateZ(0)","undefined"!=typeof this.div_.style.opacity&&""!==this.div_.style.opacity&&(this.div_.style.MsFilter='"progid:DXImageTransform.Microsoft.Alpha(Opacity='+100*this.div_.style.opacity+')"',this.div_.style.filter="alpha(opacity="+100*this.div_.style.opacity+")"),this.div_.style.position="absolute",this.div_.style.visibility="hidden",null!==this.zIndex_&&(this.div_.style.zIndex=this.zIndex_)}},InfoBox.prototype.getBoxWidths_=function(){var a,b={top:0,bottom:0,left:0,right:0},c=this.div_;return document.defaultView&&document.defaultView.getComputedStyle?(a=c.ownerDocument.defaultView.getComputedStyle(c,""),a&&(b.top=parseInt(a.borderTopWidth,10)||0,b.bottom=parseInt(a.borderBottomWidth,10)||0,b.left=parseInt(a.borderLeftWidth,10)||0,b.right=parseInt(a.borderRightWidth,10)||0)):document.documentElement.currentStyle&&c.currentStyle&&(b.top=parseInt(c.currentStyle.borderTopWidth,10)||0,b.bottom=parseInt(c.currentStyle.borderBottomWidth,10)||0,b.left=parseInt(c.currentStyle.borderLeftWidth,10)||0,b.right=parseInt(c.currentStyle.borderRightWidth,10)||0),b},InfoBox.prototype.onRemove=function(){this.div_&&(this.div_.parentNode.removeChild(this.div_),this.div_=null)},InfoBox.prototype.draw=function(){this.createInfoBoxDiv_();var a=this.getProjection().fromLatLngToDivPixel(this.position_);this.div_.style.left=a.x+this.pixelOffset_.width+"px",this.alignBottom_?this.div_.style.bottom=-(a.y+this.pixelOffset_.height)+"px":this.div_.style.top=a.y+this.pixelOffset_.height+"px",this.div_.style.visibility=this.isHidden_?"hidden":"visible"},InfoBox.prototype.setOptions=function(a){"undefined"!=typeof a.boxClass&&(this.boxClass_=a.boxClass,this.setBoxStyle_()),"undefined"!=typeof a.boxStyle&&(this.boxStyle_=a.boxStyle,this.setBoxStyle_()),"undefined"!=typeof a.content&&this.setContent(a.content),"undefined"!=typeof a.disableAutoPan&&(this.disableAutoPan_=a.disableAutoPan),"undefined"!=typeof a.maxWidth&&(this.maxWidth_=a.maxWidth),"undefined"!=typeof a.pixelOffset&&(this.pixelOffset_=a.pixelOffset),"undefined"!=typeof a.alignBottom&&(this.alignBottom_=a.alignBottom),"undefined"!=typeof a.position&&this.setPosition(a.position),"undefined"!=typeof a.zIndex&&this.setZIndex(a.zIndex),"undefined"!=typeof a.closeBoxMargin&&(this.closeBoxMargin_=a.closeBoxMargin),"undefined"!=typeof a.closeBoxURL&&(this.closeBoxURL_=a.closeBoxURL),"undefined"!=typeof a.infoBoxClearance&&(this.infoBoxClearance_=a.infoBoxClearance),"undefined"!=typeof a.isHidden&&(this.isHidden_=a.isHidden),"undefined"!=typeof a.visible&&(this.isHidden_=!a.visible),"undefined"!=typeof a.enableEventPropagation&&(this.enableEventPropagation_=a.enableEventPropagation),this.div_&&this.draw()},InfoBox.prototype.setContent=function(a){this.content_=a,this.div_&&(this.closeListener_&&(google.maps.event.removeListener(this.closeListener_),this.closeListener_=null),this.fixedWidthSet_||(this.div_.style.width=""),"undefined"==typeof a.nodeType?this.div_.innerHTML=this.getCloseBoxImg_()+a:(this.div_.innerHTML=this.getCloseBoxImg_(),this.div_.appendChild(a)),this.fixedWidthSet_||(this.div_.style.width=this.div_.offsetWidth+"px","undefined"==typeof a.nodeType?this.div_.innerHTML=this.getCloseBoxImg_()+a:(this.div_.innerHTML=this.getCloseBoxImg_(),this.div_.appendChild(a))),this.addClickHandler_()),google.maps.event.trigger(this,"content_changed")},InfoBox.prototype.setPosition=function(a){this.position_=a,this.div_&&this.draw(),google.maps.event.trigger(this,"position_changed")},InfoBox.prototype.setZIndex=function(a){this.zIndex_=a,this.div_&&(this.div_.style.zIndex=a),google.maps.event.trigger(this,"zindex_changed")},InfoBox.prototype.setVisible=function(a){this.isHidden_=!a,this.div_&&(this.div_.style.visibility=this.isHidden_?"hidden":"visible")},InfoBox.prototype.getContent=function(){return this.content_},InfoBox.prototype.getPosition=function(){return this.position_},InfoBox.prototype.getZIndex=function(){return this.zIndex_},InfoBox.prototype.getVisible=function(){var a;return a="undefined"==typeof this.getMap()||null===this.getMap()?!1:!this.isHidden_},InfoBox.prototype.show=function(){this.isHidden_=!1,this.div_&&(this.div_.style.visibility="visible")},InfoBox.prototype.hide=function(){this.isHidden_=!0,this.div_&&(this.div_.style.visibility="hidden")},InfoBox.prototype.open=function(a,b){var c=this;b&&(this.position_=b.getPosition(),this.moveListener_=google.maps.event.addListener(b,"position_changed",function(){c.setPosition(this.getPosition())})),this.setMap(a),this.div_&&this.panBox_()},InfoBox.prototype.close=function(){var a;if(this.closeListener_&&(google.maps.event.removeListener(this.closeListener_),this.closeListener_=null),this.eventListeners_){for(a=0;a<this.eventListeners_.length;a++)google.maps.event.removeListener(this.eventListeners_[a]);this.eventListeners_=null}this.moveListener_&&(google.maps.event.removeListener(this.moveListener_),this.moveListener_=null),this.contextListener_&&(google.maps.event.removeListener(this.contextListener_),this.contextListener_=null),this.setMap(null)},function(){var a=!0,b=null,c=!1;(function(){var d,e={}.hasOwnProperty,f=[].slice;((d=this.google)!=b?d.maps:void 0)!=b&&(this.OverlappingMarkerSpiderfier=function(){function d(a,c){var d,f,h,i,j=this;this.map=a,c==b&&(c={});for(d in c)e.call(c,d)&&(f=c[d],this[d]=f);for(this.e=new this.constructor.g(this.map),this.n(),this.b={},i=["click","zoom_changed","maptypeid_changed"],f=0,h=i.length;h>f;f++)d=i[f],g.addListener(this.map,d,function(){return j.unspiderfy()})}var g,h,i,j,k,l,m,n;for(l=d.prototype,n=[d,l],j=0,k=n.length;k>j;j++)i=n[j],i.VERSION="0.3.3";return h=google.maps,g=h.event,k=h.MapTypeId,m=2*Math.PI,l.keepSpiderfied=c,l.markersWontHide=c,l.markersWontMove=c,l.nearbyDistance=20,l.circleSpiralSwitchover=9,l.circleFootSeparation=23,l.circleStartAngle=m/12,l.spiralFootSeparation=26,l.spiralLengthStart=11,l.spiralLengthFactor=4,l.spiderfiedZIndex=1e3,l.usualLegZIndex=10,l.highlightedLegZIndex=20,l.legWeight=1.5,l.legColors={usual:{},highlighted:{}},j=l.legColors.usual,i=l.legColors.highlighted,j[k.HYBRID]=j[k.SATELLITE]="#fff",i[k.HYBRID]=i[k.SATELLITE]="#f00",j[k.TERRAIN]=j[k.ROADMAP]="#444",i[k.TERRAIN]=i[k.ROADMAP]="#f00",l.n=function(){this.a=[],this.j=[]},l.addMarker=function(d){var e,f=this;return d._oms!=b?this:(d._oms=a,e=[g.addListener(d,"click",function(a){return f.F(d,a)})],this.markersWontHide||e.push(g.addListener(d,"visible_changed",function(){return f.o(d,c)})),this.markersWontMove||e.push(g.addListener(d,"position_changed",function(){return f.o(d,a)})),this.j.push(e),this.a.push(d),this)},l.o=function(a,c){return a._omsData==b||!c&&a.getVisible()||this.s!=b||this.t!=b?void 0:this.unspiderfy(c?a:b)},l.getMarkers=function(){return this.a.slice(0)},l.removeMarker=function(a){var c,d,e,f,h;if(a._omsData!=b&&this.unspiderfy(),c=this.m(this.a,a),0>c)return this;for(e=this.j.splice(c,1)[0],f=0,h=e.length;h>f;f++)d=e[f],g.removeListener(d);return delete a._oms,this.a.splice(c,1),this},l.clearMarkers=function(){var a,b,c,d,e,f,h,i;for(this.unspiderfy(),i=this.a,a=d=0,f=i.length;f>d;a=++d){for(c=i[a],b=this.j[a],e=0,h=b.length;h>e;e++)a=b[e],g.removeListener(a);delete c._oms}return this.n(),this},l.addListener=function(a,c){var d,e;return((e=(d=this.b)[a])!=b?e:d[a]=[]).push(c),this},l.removeListener=function(a,b){var c;return c=this.m(this.b[a],b),0>c||this.b[a].splice(c,1),this},l.clearListeners=function(a){return this.b[a]=[],this},l.trigger=function(){var a,c,d,e,g,h;for(c=arguments[0],a=2<=arguments.length?f.call(arguments,1):[],c=(d=this.b[c])!=b?d:[],h=[],e=0,g=c.length;g>e;e++)d=c[e],h.push(d.apply(b,a));return h},l.u=function(a,b){var c,d,e,f,g;for(e=this.circleFootSeparation*(2+a)/m,d=m/a,g=[],c=f=0;a>=0?a>f:f>a;c=a>=0?++f:--f)c=this.circleStartAngle+c*d,g.push(new h.Point(b.x+e*Math.cos(c),b.y+e*Math.sin(c)));return g},l.v=function(a,b){var c,d,e,f,g;for(e=this.spiralLengthStart,c=0,g=[],d=f=0;a>=0?a>f:f>a;d=a>=0?++f:--f)c+=this.spiralFootSeparation/e+5e-4*d,d=new h.Point(b.x+e*Math.cos(c),b.y+e*Math.sin(c)),e+=m*this.spiralLengthFactor/c,g.push(d);return g},l.F=function(a,c){var d,e,f,g,h,i,j,k,l;if(g=a._omsData!=b,(!g||!this.keepSpiderfied)&&this.unspiderfy(),g||this.map.getStreetView().getVisible()||"GoogleEarthAPI"===this.map.getMapTypeId())return this.trigger("click",a,c);for(g=[],h=[],d=this.nearbyDistance,i=d*d,f=this.c(a.position),l=this.a,j=0,k=l.length;k>j;j++)d=l[j],d.map!=b&&d.getVisible()&&(e=this.c(d.position),this.f(e,f)<i?g.push({A:d,p:e}):h.push(d));return 1===g.length?this.trigger("click",a,c):this.G(g,h)},l.markersNearMarker=function(a,d){var e,f,g,h,i,j,k,l,m,n;if(d==b&&(d=c),this.e.getProjection()==b)throw"Must wait for 'idle' event on map before calling markersNearMarker";for(e=this.nearbyDistance,i=e*e,g=this.c(a.position),h=[],l=this.a,j=0,k=l.length;k>j&&(e=l[j],e===a||e.map==b||!e.getVisible()||(f=this.c((m=(n=e._omsData)!=b?n.l:void 0)!=b?m:e.position),!(this.f(f,g)<i&&(h.push(e),d))));j++);return h},l.markersNearAnyOtherMarker=function(){var d,e,f,g,h,i,j,k,l,m,n,o;if(this.e.getProjection()==b)throw"Must wait for 'idle' event on map before calling markersNearAnyOtherMarker";for(i=this.nearbyDistance,d=i*i,g=this.a,i=[],n=0,f=g.length;f>n;n++)e=g[n],i.push({q:this.c((j=(l=e._omsData)!=b?l.l:void 0)!=b?j:e.position),d:c});for(n=this.a,e=j=0,l=n.length;l>j;e=++j)if(f=n[e],f.map!=b&&f.getVisible()&&(g=i[e],!g.d))for(o=this.a,f=k=0,m=o.length;m>k;f=++k)if(h=o[f],f!==e&&h.map!=b&&h.getVisible()&&(h=i[f],(!(e>f)||h.d)&&this.f(g.q,h.q)<d)){g.d=h.d=a;break}for(n=this.a,f=[],d=j=0,l=n.length;l>j;d=++j)e=n[d],i[d].d&&f.push(e);return f},l.z=function(a){var b=this;return{h:function(){return a._omsData.i.setOptions({strokeColor:b.legColors.highlighted[b.map.mapTypeId],zIndex:b.highlightedLegZIndex})},k:function(){return a._omsData.i.setOptions({strokeColor:b.legColors.usual[b.map.mapTypeId],zIndex:b.usualLegZIndex})}}},l.G=function(b,c){var d,e,f,i,j,k,l,m,n,o;return this.s=a,o=b.length,d=this.C(function(){var a,c,d;for(d=[],a=0,c=b.length;c>a;a++)m=b[a],d.push(m.p);return d}()),i=o>=this.circleSpiralSwitchover?this.v(o,d).reverse():this.u(o,d),d=function(){var a,c,d,m=this;for(d=[],a=0,c=i.length;c>a;a++)f=i[a],e=this.D(f),n=this.B(b,function(a){return m.f(a.p,f)}),l=n.A,k=new h.Polyline({map:this.map,path:[l.position,e],strokeColor:this.legColors.usual[this.map.mapTypeId],strokeWeight:this.legWeight,zIndex:this.usualLegZIndex}),l._omsData={l:l.position,i:k},this.legColors.highlighted[this.map.mapTypeId]!==this.legColors.usual[this.map.mapTypeId]&&(j=this.z(l),l._omsData.w={h:g.addListener(l,"mouseover",j.h),k:g.addListener(l,"mouseout",j.k)}),l.setPosition(e),l.setZIndex(Math.round(this.spiderfiedZIndex+f.y)),d.push(l);return d}.call(this),delete this.s,this.r=a,this.trigger("spiderfy",d,c)},l.unspiderfy=function(c){var d,e,f,h,i,j,k;if(c==b&&(c=b),this.r==b)return this;for(this.t=a,h=[],f=[],k=this.a,i=0,j=k.length;j>i;i++)e=k[i],e._omsData!=b?(e._omsData.i.setMap(b),e!==c&&e.setPosition(e._omsData.l),e.setZIndex(b),d=e._omsData.w,d!=b&&(g.removeListener(d.h),g.removeListener(d.k)),delete e._omsData,h.push(e)):f.push(e);return delete this.t,delete this.r,this.trigger("unspiderfy",h,f),this},l.f=function(a,b){var c,d;return c=a.x-b.x,d=a.y-b.y,c*c+d*d},l.C=function(a){var b,c,d,e,f;for(e=c=d=0,f=a.length;f>e;e++)b=a[e],c+=b.x,d+=b.y;return a=a.length,new h.Point(c/a,d/a)},l.c=function(a){return this.e.getProjection().fromLatLngToDivPixel(a)},l.D=function(a){return this.e.getProjection().fromDivPixelToLatLng(a)},l.B=function(a,c){var d,e,f,g,h,i;for(f=h=0,i=a.length;i>h;f=++h)g=a[f],g=c(g),("undefined"==typeof d||d===b||e>g)&&(e=g,d=f);return a.splice(d,1)[0]},l.m=function(a,c){var d,e,f,g;if(a.indexOf!=b)return a.indexOf(c);
	for(d=f=0,g=a.length;g>f;d=++f)if(e=a[d],e===c)return d;return-1},d.g=function(a){return this.setMap(a)},d.g.prototype=new h.OverlayView,d.g.prototype.draw=function(){},d}())}).call(this)}.call(this);


	var map_options = {"map_style": "[{\"featureType\":\"landscape\",\"stylers\":[{\"hue\":\"#FFBB00\"},{\"saturation\":43.4},{\"lightness\":37.6},{\"gamma\":1}]},{\"featureType\":\"road.highway\",\"stylers\":[{\"hue\":\"#FFC200\"},{\"saturation\":-61.8},{\"lightness\":45.6},{\"gamma\":1}]},{\"featureType\":\"road.arterial\",\"stylers\":[{\"hue\":\"#FF0300\"},{\"saturation\":-100},{\"lightness\":51.2},{\"gamma\":1}]},{\"featureType\":\"road.local\",\"stylers\":[{\"hue\":\"#FF0300\"},{\"saturation\":-100},{\"lightness\":52},{\"gamma\":1}]},{\"featureType\":\"water\",\"stylers\":[{\"hue\":\"#0078FF\"},{\"saturation\":-13.2},{\"lightness\":2.4},{\"gamma\":1}]},{\"featureType\":\"poi\",\"stylers\":[{\"hue\":\"#00FF6A\"},{\"saturation\":-1.0989010989},{\"lightness\":11.2},{\"gamma\":1}]}]"};

	var map = null, markers = [], newMarkers = [], markerCluster = null, infobox = [], address = null;

	var markerClusterOptions = {
		gridSize: 60, // Default: 60
		maxZoom: 14,
		styles: [{
				width: 50,
				height: 40,
				url: '#'
			}]
	};

	var listingsData = '';
	var geocoder = new google.maps.Geocoder();
	/**
	 * initMap
	 *
	 */
	function initMap() {
		listingsData = jQuery.parseJSON( jQuery('.wre-google-map').attr('data-listings-data') );
		jQuery('.wre-google-map').removeAttr('data-listings-data');
		var map_settings = listingsData.map_settings;
		var lat = parseInt(map_settings.center[0]);
		var lng = parseInt(map_settings.center[1]);
		var mapOptions = {
			center: new google.maps.LatLng(lat, lng),
			zoom: parseInt(map_settings.zoom),
			scrollwheel: false,
			streetViewControl: false,
			draggable: true,
			disableDefaultUI: true,
		};

		map = new google.maps.Map(document.getElementById("wre-advanced-map"), mapOptions);

		if (map_options.map_style !== '') {
			var styles = JSON.parse(map_options.map_style);
			map.setOptions({styles: styles});
		}

		address = null;
		var MapData = [];
		
		
		var listings = listingsData.listings;
		jQuery.each(listings, function( key, value ){
			MapData.push({
				permalink: value.permalink,
				title: value.title,
				price: value.price,
				content: value.content,
				latLng: new google.maps.LatLng(value.lat, value.lng),
				thumbnail: value.thumbnail['sml'],
				icon: value.icon
			});
		});
		markers = initMarkers(map, MapData);

		// Spiderfier
		var oms = new OverlappingMarkerSpiderfier(map, {markersWontMove: true, markersWontHide: true, keepSpiderfied: true, legWeight: 5});

		function omsMarkers(markers) {
			for (var i = 0; i < markers.length; i++) {
				oms.addMarker(markers[i]);
			}
		}

		omsMarkers(markers);

	}
	google.maps.event.addDomListener(window, 'load', initMap);

	/**
	 * Get latLng from property address and grab it with callback, as geocode calls asynchonous
	 *
	 */
	function getLatLng(callback) {

		if (geocoder && address) {
			geocoder.geocode({'address': address}, function (results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					latLng = results[0].geometry.location;
					callback(latLng);
				} else {
					console.log("Geocoder failed due to: " + status);
				}
			});
		}
	}

	/**
	 * initMarkers
	 *
	 */
	function initMarkers(map, markerData) {

		getLatLng(function (latLng) {
			map.setCenter(latLng);
		});

		var bounds = new google.maps.LatLngBounds();

		for (var i = 0; i < markerData.length; i++) {
			var labelData = '';
			if(markerData[i].icon) {
				icon = new google.maps.MarkerImage(
					markerData[i].icon,
					null, // size is determined at runtime
					null, // origin is 0,0
					null, // anchor is bottom center of the scaled image
					new google.maps.Size(50, 70)
				);
			} else {
				icon = ' ';
				labelData = {
					fontFamily: 'wrewp',
					text: "\140",
					fontSize: '60px',
					color: '#44a3d3'
				}
			}
			marker = new google.maps.Marker({
				map: map,
				position: markerData[i].latLng,
				icon: icon,
				label: labelData,
				animation: google.maps.Animation.DROP
			}),
			infoboxOptions = {
				content: '<div class="map-marker-wrapper">' +
							'<div class="map-marker-container">' +
								'<div class="close-infobox"><i class="wre-icon-close"></i></div>' +
								'<div class="arrow-down"></div>' +
								'<a href="' + markerData[i].permalink + '">' +
									'<img width="80" src="' + markerData[i].thumbnail + '" />' +
									'<h5 class="title">' + markerData[i].title + '</h5>' +
								'</a>' +
								'<div class="content">' +
									markerData[i].content +
								'</div>' +
								'<p class="price">' +
									markerData[i].price +
								'</p>' +
							'</div>' +
						'</div>',
				disableAutoPan: false,
				pixelOffset: new google.maps.Size(-33, -70),
				zIndex: null,
				isHidden: true,
				alignBottom: true,
				closeBoxURL: '',
				infoBoxClearance: new google.maps.Size(25, 25)
			};

			newMarkers.push(marker);

			newMarkers[i].infobox = new InfoBox(infoboxOptions);

			google.maps.event.addListener(newMarkers[i].infobox, 'domready', function(e) {
				var current_infobox = jQuery(this)[0];
				jQuery('.close-infobox').on('click', function(){
					current_infobox.hide();
					map.setCenter(current_infobox.getPosition());
					map.panBy(0, -175);
				});
			});

			google.maps.event.addListener(marker, 'click', (function (marker, i) {
				return function () {
					if (newMarkers[i].infobox.getVisible()) {
						newMarkers[i].infobox.hide();
					} else {
						jQuery('.infoBox').hide();
						newMarkers[i].infobox.show();
					}

					newMarkers[i].infobox.open(map, this);
					map.setCenter(marker.getPosition());
					map.panBy(0, -175);
				}
			})(marker, i));

			google.maps.event.addListener(map, 'click', function () {
				jQuery('.infoBox').hide();
			});
			// Extend map bounds for this marker
			bounds.extend(markerData[i].latLng);

		} // for (each marker)
		if(listingsData.map_settings.fit == 'true') {
			// Create new map bounds to have all marker on the map
			// If not on single-property.php, as only required for multiple markers
			map.fitBounds(bounds);
		}
		markerCluster = new MarkerClusterer(map, newMarkers, markerClusterOptions);

		return newMarkers;

	} // initMarkers()
	function map_controls() {

		// Zoom In
		google.maps.event.addDomListener(document.getElementById('wre-zoom-in'), 'click', function (e) {
			e.preventDefault();
			var currentZoom = map.getZoom();
			currentZoom++;
			if (currentZoom > 19) {
				currentZoom = 19;
			}
			map.setZoom(currentZoom);
			return false;
		});

		// Zoom Out
		google.maps.event.addDomListener(document.getElementById('wre-zoom-out'), 'click', function (e) {
			e.preventDefault();
			var currentZoom = map.getZoom();
			currentZoom--;
			if (currentZoom > 19) {
				currentZoom = 19;
			}
			map.setZoom(currentZoom);
			return false;
		});

		// Map Type: Roadmap
		google.maps.event.addDomListener(document.getElementById('wre-map-type-roadmap'), 'click', function (e) {
			e.preventDefault();
			map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
			return false;
		});

		// Map Type: Satellite
		google.maps.event.addDomListener(document.getElementById('wre-map-type-satellite'), 'click', function (e) {
			e.preventDefault();
			map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
			return false;
		});

		// Map Type: Hybrid
		google.maps.event.addDomListener(document.getElementById('wre-map-type-hybrid'), 'click', function (e) {
			e.preventDefault();
			map.setMapTypeId(google.maps.MapTypeId.HYBRID);
			return false;
		});

		// Map Type: Terrain
		google.maps.event.addDomListener(document.getElementById('wre-map-type-terrain'), 'click', function (e) {
			e.preventDefault();
			map.setMapTypeId(google.maps.MapTypeId.TERRAIN);
			return false;
		});

		if( listingsData.map_settings.search == 'true' ) {
			jQuery("#wre-map-address").geocomplete();
			document.getElementById('wre-map-submit').addEventListener('click', function() {
				geocodeAddress( geocoder, map );
			});

			function geocodeAddress(geocoder, resultsMap) {
				var search_zoom = parseInt(listingsData.map_settings.search_zoom);
				var address = document.getElementById('wre-map-address').value;
				geocoder.geocode({'address': address}, function(results, status) {
				if (status === 'OK') {
					resultsMap.setCenter(results[0].geometry.location);

					resultsMap.setZoom(search_zoom);
					} else {
						alert( 'Location could not be found' );
					}
				});
			}
		}

		jQuery('.map-type li').click(function () {
			jQuery('.map-type li').removeClass('active');
			jQuery(this).addClass('active');
		});

		// Geolocation - Current Location
		jQuery('#wre-current-location').click(function (e) {
			e.preventDefault();

			// Current Location Marker
			var markerCurrent = new google.maps.Marker({
				//clickable: false,
				icon: new google.maps.MarkerImage('//maps.gstatic.com/mapfiles/mobile/mobileimgs2.png',
							new google.maps.Size(22, 22),
							new google.maps.Point(0, 18),
							new google.maps.Point(11, 11)
						),
				shadow: null,
				zIndex: null,
				map: map});

			jQuery(this).toggleClass('active');

			if (!jQuery('#wre-current-location').hasClass('draw')) {
				// Create Loading Element
				var loading = document.createElement('div');
				loading.setAttribute('class', 'wre-loader-container');
				loading.innerHTML = '<div class="svg-loader"></div>';
				document.getElementById('wre-advanced-map').appendChild(loading);
			}

			// Current Position
			if (navigator.geolocation) {

				navigator.geolocation.getCurrentPosition(function (current) {

					var me = new google.maps.LatLng(current.coords.latitude, current.coords.longitude);
					markerCurrent.setPosition(me);
					map.panTo(me);

					// Remove Loader
					if( loading != undefined )
						loading.remove();

					// https://developers.google.com/maps/documentation/javascript/examples/circle-simple
					var currentRadiusCircleOptions = {
						strokeColor: '#00CFF0',
						strokeOpacity: 0.6,
						strokeWeight: 2,
						fillColor: '#00CFF0',
						fillOpacity: 0.2,
						map: map,
						center: me,
						visible: true,
						radius: 1000 // Unit: meter
					};

					// When Initializing
					if (!jQuery('#wre-current-location').hasClass('draw')) {

						// Create Circle
						currentRadiusCircle = new google.maps.Circle(currentRadiusCircleOptions);

					}

					jQuery('#wre-current-location').addClass('draw');

					// Toggle Crrent Location Icon & Circle
					if (jQuery('#wre-current-location').hasClass('active')) {
						markerCurrent.setMap(map);
						currentRadiusCircle.setMap(map);
					}
					else {
						markerCurrent.setMap(null);
						currentRadiusCircle.setMap(null);
					}

				});

			} else {
				console.log("Current Position Not Found");
			}

			// Toggle Current Location Circle Visibility
			google.maps.event.addListener(markerCurrent, 'click', (function () {
				if (currentRadiusCircle.getVisible()) {
					currentRadiusCircle.set('visible', false);
				} else {
					currentRadiusCircle.set('visible', true);
				}
			}));

			return false;

		});

	}

	google.maps.event.addDomListener(window, 'load', map_controls);

}());