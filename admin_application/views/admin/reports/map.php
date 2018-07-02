<div id="map" style="height: 600px; width: 100%; background-color: #DDDDDD;"></div>

<script>

function initMap() {
  
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 5,
    center: {lat: <?= $center_lat; ?>,lng: <?= $center_lng; ?>}
  });
  
  var heatmap = new google.maps.visualization.HeatmapLayer({
    data: getPoints(),
    map: map,
    radius: 20
  });
}

function getPoints() {
    return [
        <?php foreach($points as $index => $point) : ?>
        new google.maps.LatLng(<?= $point->latitude; ?>, <?= $point->longitude; ?>)<?php if($index != count($points) - 1) echo ","; ?>
        <?php endforeach; ?>
        ]
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKwk53Jy9zIhiVzIhiPr45imGvsOEOzk8&signed_in=true&libraries=visualization&callback=initMap"></script>