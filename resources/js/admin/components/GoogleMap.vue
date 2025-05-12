<template>
  <div>
    <input
      ref="autocompleteInput"
      type="text"
      placeholder="Enter your address"
      class="address-input"
      :value="address"
      @input="onAddressInput"
    />
    <div ref="map" class="map"></div>
  </div>
</template>

<script>
export default {
  props: {
    initialAddress: String,
    initialLatitude: [String, Number],
    initialLongitude: [String, Number],
  },
  data() {
    return {
      address: this.initialAddress || '',
      lat: parseFloat(this.initialLatitude) || 23.8103,
      lng: parseFloat(this.initialLongitude) || 90.4125,
      postalCode: '',
      city: '',
      autocomplete: null,
      map: null,
      marker: null,
    };
  },
  mounted() {
    if (window.google && window.google.maps) {
      this.initMap();
      this.initAutocomplete();
    } else {
      const check = setInterval(() => {
        if (window.google && window.google.maps) {
          clearInterval(check);
          this.initMap();
          this.initAutocomplete();
        }
      }, 200);
    }
  },
  methods: {
    onAddressInput(event) {
      this.address = event.target.value;
    },

    initMap() {
      const center = { lat: this.lat, lng: this.lng };

      this.map = new google.maps.Map(this.$refs.map, {
        center,
        zoom: 13,
      });

      this.marker = new google.maps.Marker({
        position: center,
        map: this.map,
        draggable: true,
      });

      this.marker.addListener('dragend', this.handleMarkerDrag);
    },

    initAutocomplete() {
      const input = this.$refs.autocompleteInput;
      this.autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['geocode'],
      });

      this.autocomplete.addListener('place_changed', () => {
        const place = this.autocomplete.getPlace();
        if (!place.geometry) return;

        const location = place.geometry.location;

        this.lat = location.lat();
        this.lng = location.lng();
        this.address = place.formatted_address;

        const postalComp = place.address_components.find(c =>
          c.types.includes('postal_code')
        );
        this.postalCode = postalComp?.long_name || '';

        const cityComp = place.address_components.find(c =>
          c.types.includes('locality')
        );
        this.city = cityComp?.long_name || '';

        this.map.setCenter(location);
        this.marker.setPosition(location);

        this.emitAll();
      });
    },

    handleMarkerDrag(event) {
      const lat = event.latLng.lat();
      const lng = event.latLng.lng();

      this.lat = lat;
      this.lng = lng;

      const geocoder = new google.maps.Geocoder();
      const latlng = { lat, lng };

      geocoder.geocode({ location: latlng }, (results, status) => {
        if (status === 'OK' && results[0]) {
          this.address = results[0].formatted_address;

          const postalComp = results[0].address_components.find(c =>
            c.types.includes('postal_code')
          );
          this.postalCode = postalComp?.long_name || '';

          const cityComp = results[0].address_components.find(c =>
            c.types.includes('locality')
          );
          this.city = cityComp?.long_name || '';

          this.emitAll();
        }
      });
    },

    emitAll() {
      this.$emit('update:address', this.address);
      this.$emit('update:latitude', this.lat);
      this.$emit('update:longitude', this.lng);
      this.$emit('update:postalCode', this.postalCode);
      this.$emit('update:city', this.city);
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
    border: none;
    box-shadow: 0px 0px 0px 1px #E8EAF1;
  }

  &::placeholder {
    color: #A0A3B1;
    font-size: 14px;
    font-weight: 400;
  }

  &:focus-visible {
    outline: none;
  }
}

.map {
  width: 100%;
  height: 300px;
  margin-top: 10px;
  border-radius: 8px;
  overflow: hidden;
}
</style>
