jQuery(document).ready(function ($) {
    if (typeof ehxdMapData !== 'undefined') {
        var lat = parseFloat(ehxdMapData.latitude);
        var lng = parseFloat(ehxdMapData.longitude);
        var address = ehxdMapData.address;
        var title = ehxdMapData.title;

        // Check if latitude and longitude are valid
        if (!isNaN(lat) && !isNaN(lng)) {
            var map = new google.maps.Map(document.getElementById('ehxd-map'), {
                zoom: 15,
                center: { lat: lat, lng: lng },
            });

            new google.maps.Marker({
                map: map,
                position: { lat: lat, lng: lng },
                title: title,
            });
        } else if (address) {
            var map = new google.maps.Map(document.getElementById('ehxd-map'), {
                zoom: 15,
                center: { lat: 0, lng: 0 },
            });

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: address }, function (results, status) {
                if (status === 'OK') {
                    map.setCenter(results[0].geometry.location);
                    new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                        title: title,
                    });
                } else {
                    console.error('Geocode failed: ' + status);
                }
            });
        } else {
            console.warn('No location data available');
        }
    } else {
        console.warn('ehxdMapData is not defined');
    }
});