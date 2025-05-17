function initMapWhenReady() {
    if (typeof google === 'object' && typeof google.maps === 'object') {
        const lat = parseFloat(ehxdMapData.latitude);
        const lng = parseFloat(ehxdMapData.longitude);
        console.log(lat, lng, 'lat, lng');
        const map = new google.maps.Map(document.getElementById("ehxd-map"), {
            center: { lat, lng },
            zoom: 15,
        });
        new google.maps.Marker({
            position: { lat, lng },
            map: map,
            title: ehxdMapData.title,
        });
    } else {
        // Retry in 200ms
        setTimeout(initMapWhenReady, 200);
    }
}

document.addEventListener("DOMContentLoaded", initMapWhenReady);

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

