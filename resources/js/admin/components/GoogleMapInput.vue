<template>
  <div>
    <!-- <input ref="autocompleteInput" type="text" placeholder="Enter your address or postcode" class="address-input"
      :value="initialAddress" @input="onAddressInput" style="height: 30px;width: 86%;" /> -->
    <div class="ehxd-input-wrapper">
      <span class="dashicons dashicons-search ehxd_icon"></span>
      <input ref="autocompleteInput" type="text" placeholder="Enter address/postcode" class=" ehxd-location-input"
      :value="initialAddress" @input="onAddressInput"  />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    initialAddress: String,
    initialLatitude: [String, Number],
    initialLongitude: [String, Number],
    showMap: {
      type: Boolean,
      default: true,
    },
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
      this.initAutocomplete();
    } else {
      const check = setInterval(() => {
        if (window.google && window.google.maps) {
          clearInterval(check);
          this.initAutocomplete();
        }
      }, 200);
    }
  },
  methods: {
    onAddressInput(event) {
      this.address = event.target.value;
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

        this.emitAll();
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
.ehxd-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    padding-bottom: 25px;
    border-bottom: 1px solid rgb(224, 224, 224);
    .ehxd_icon {
        position: absolute;
        position: absolute;
        color: #666666;
        font-weight: 100;
        padding: 1px 0px 0px 8px;
    }
}
.ehxd-location-input {
    width: 100%;
    padding: 12px 10px 12px 35px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 14px;
    background: #FBFBFB;

    &:focus-visible {
        outline: none;
    }
}
</style>