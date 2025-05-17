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

jQuery(document).ready(function ($) {
    console.log(window.EhxDirectoristData.rest_api, 'window');
    $('#ehxd-contact-form').on('submit', function (e) {
        e.preventDefault();

        const data = {
            admin_email: $('input[name="addmin_email"').val(),
            name: $('input[name="name"]').val(),
            email: $('input[name="email"]').val(),
            message: $('textarea[name="message"]').val()
        };

        $.ajax({
            url: `${window.EhxDirectoristData.rest_api}/submitFrom`,
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (response) {
                if (response.success === true) {
                    $('.ehxd-response-message').text(response.message).css('color', 'green');

                    $('#ehxd-contact-form').find('input[type="text"], input[type="email"], textarea').val('');
                } else {
                    $('.ehxd-response-message').text(response.message).css('color', '#e96a6a');
                    $('#ehxd-contact-form').find('input[type="text"], input[type="email"], textarea').val('');
                }
            },
            
            error: function (xhr) {
                let errorMsg = 'Failed to send message.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('.ehxd-response-message').text(errorMsg).css('color', '#e96a6a');
            }
        });
    });
});

