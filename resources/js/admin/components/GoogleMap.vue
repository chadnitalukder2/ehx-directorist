<template>
    <div>
        <input ref="autocompleteInput" type="text" placeholder="Enter your address" class="address-input" />
        <div v-if="address">
            <p><strong>Address:</strong> {{ address }}</p>
            <p><strong>Latitude:</strong> {{ lat }}</p>
            <p><strong>Longitude:</strong> {{ lng }}</p>
            <p><strong>Postal Code:</strong> {{ postalCode }}</p>
        </div>
        <div ref="map" class="map"></div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            address: '',
            lat: null,
            lng: null,
            postalCode: '',
            autocomplete: null,
            map: null,
            marker: null,
        };
    },
    mounted() {
        if (window.google && window.google.maps) {
            this.initAutocomplete();
            this.initMap();
        } else {
            const check = setInterval(() => {
                if (window.google && window.google.maps) {
                    clearInterval(check);
                    this.initAutocomplete();
                    this.initMap();
                }
            }, 200);
        }
    },
    methods: {
        initAutocomplete() {
            const input = this.$refs.autocompleteInput;
            this.autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode'], // This ensures that the address is a valid geocode
            });

            this.autocomplete.addListener('place_changed', () => {
                const place = this.autocomplete.getPlace();

                if (!place.geometry) return; // If no geometry is found, exit

                // Update the address, latitude, longitude, and map position
                this.address = place.formatted_address;
                this.lat = place.geometry.location.lat();
                this.lng = place.geometry.location.lng();

                // Loop through the address components to find the postal code
                const postalComp = place.address_components.find(comp =>
                    comp.types.includes('postal_code') // Search for the postal code component
                );

                // If postal code is found, set it, else set an empty string
                this.postalCode = postalComp ? postalComp.long_name : '';

                // Update the map and marker location based on the selected address
                const location = place.geometry.location;
                this.map.setCenter(location);
                this.marker.setPosition(location);
            });
        },
    
    initMap() {
        this.map = new google.maps.Map(this.$refs.map, {
            center: { lat: 23.8103, lng: 90.4125 }, // Default to Dhaka
            zoom: 13,
        });

        this.marker = new google.maps.Marker({
            map: this.map,
            position: { lat: 23.8103, lng: 90.4125 },
        });
    },
},
  };
</script>

<style scoped lang="scss">

.address-input {
    width: 100%;
    padding: 5px 25px;
    font-size: 14px;
    border-radius: 8px;
    margin-bottom: 18px;
    border: 1px solid #E8EAF1;
    color: #2c3338;
    &:focus {
        outline: none;
        border: none ;
        box-shadow: 0px 0px 0px 1px #E8EAF1;
    }
    &::placeholder {
        color: #A0A3B1;
        font-size: 14px;
        font-weight: 400;
    }
    &:focus-visible{
        outline: none;
    }
}


.map {
    width: 100%;
    height: 350px;
    margin-top: 10px;
    border-radius: 8px;
    overflow: hidden;
}

</style>