<template>
  <div>
    <div
      ref="mapContainer"
      class="w-full h-64 rounded-lg border"
      :class="{ 'border-red-500': error }"
    ></div>
    <p class="text-xs text-gray-500 mt-1">
      {{ $t('Click on map to set location') }} • {{ $t('Drag marker to adjust location') }}
    </p>
    <div class="relative mt-2">
      <input
        type="text"
        ref="searchInput"
        v-model="localAddress"
        @input="handleManualAddress($event.target.value)"
        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        :class="{ 'border-red-500': error }"
        :placeholder="$t('Search location or enter address')"
      />
      <div v-if="predictions.length > 0" class="absolute z-50 w-full mt-1 bg-white border rounded-lg shadow-lg">
        <div class="max-h-60 overflow-y-auto">
          <div
            v-for="prediction in predictions"
            :key="prediction.place_id"
            class="px-4 py-2 cursor-pointer hover:bg-gray-100"
            @click="selectPrediction(prediction)"
          >
            {{ prediction.description }}
          </div>
        </div>
      </div>
    </div>
    
    <!-- Manual coordinate input as fallback -->
    <div class="mt-4 p-3 bg-gray-50 rounded-lg border">
      <p class="text-xs text-gray-600 mb-2">{{ $t('Or enter coordinates manually') }}:</p>
      <div class="grid grid-cols-2 gap-2">
        <div>
          <label class="text-xs text-gray-500">{{ $t('Latitude') }}</label>
          <input
            type="number"
            step="any"
            :value="latitude"
            @input="handleManualLatitude($event.target.value)"
            class="w-full px-2 py-1 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
            placeholder="24.7136"
          />
        </div>
        <div>
          <label class="text-xs text-gray-500">{{ $t('Longitude') }}</label>
          <input
            type="number"
            step="any"
            :value="longitude"
            @input="handleManualLongitude($event.target.value)"
            class="w-full px-2 py-1 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
            placeholder="46.6753"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import debounce from 'lodash/debounce'

const props = defineProps({
  latitude: {
    type: [Number, String],
    default: null,
  },
  longitude: {
    type: [Number, String],
    default: null,
  },
  address: {
    type: String,
    default: '',
  },
  error: {
    type: [String, Boolean],
    default: false,
  },
})

const emit = defineEmits(['update:latitude', 'update:longitude', 'update:address'])

const mapContainer = ref(null)
const searchInput = ref(null)
const map = ref(null)
const marker = ref(null)
const localAddress = ref(props.address)
const predictions = ref([])
const autocompleteService = ref(null)
const placesService = ref(null)

const initMap = () => {
  console.log('🗺️ Attempting to initialize Google Maps...');
  
  if (!window.google) {
    console.error('❌ Google Maps API not loaded');
    return;
  }
  
  if (!mapContainer.value) {
    console.error('❌ Map container element not found');
    return;
  }
  
  console.log('✅ Google Maps API is available, initializing map...');

  // Initialize the map with default center or provided coordinates
  const center = props.latitude && props.longitude
    ? { lat: Number(props.latitude), lng: Number(props.longitude) }
    : { lat: 24.7136, lng: 46.6753 } // Default to Riyadh, Saudi Arabia

  console.log('📍 Map center:', center);

  map.value = new google.maps.Map(mapContainer.value, {
    center,
    zoom: 13,
    streetViewControl: false,
    mapTypeControl: false,
  })

  console.log('🗺️ Map created successfully');

  // Initialize the marker
  marker.value = new google.maps.Marker({
    position: center,
    map: map.value,
    draggable: true,
  })

  console.log('📌 Marker created successfully');

  // Initialize Places services
  try {
    autocompleteService.value = new google.maps.places.AutocompleteService()
    placesService.value = new google.maps.places.PlacesService(map.value)
    console.log('🔍 Places services initialized');
  } catch (error) {
    console.error('❌ Failed to initialize Places services:', error);
  }

  // Add click event to map
  map.value.addListener('click', (e) => {
    console.log('🖱️ Map clicked at:', e.latLng.lat(), e.latLng.lng());
    updateLocation(e.latLng)
  })

  // Add dragend event to marker
  marker.value.addListener('dragend', () => {
    console.log('🎯 Marker dragged to:', marker.value.getPosition().lat(), marker.value.getPosition().lng());
    updateLocation(marker.value.getPosition())
  })

  console.log('✅ Google Maps initialization complete');
}

const updateLocation = async (latLng) => {
  const lat = latLng.lat()
  const lng = latLng.lng()

  console.log('🎯 Updating location to:', lat, lng);

  // Update marker position
  marker.value.setPosition(latLng)
  map.value.panTo(latLng)

  // Get address from coordinates
  const geocoder = new google.maps.Geocoder()
  try {
    console.log('🔍 Starting reverse geocoding...');
    const response = await geocoder.geocode({ location: latLng })
    if (response.results[0]) {
      localAddress.value = response.results[0].formatted_address
      console.log('📍 Address found:', response.results[0].formatted_address);
      emit('update:address', response.results[0].formatted_address)
    } else {
      console.warn('⚠️ No address found for coordinates');
    }
  } catch (error) {
    console.error('❌ Geocoding failed:', error)
  }

  // Emit updates
  console.log('📡 Emitting coordinate updates:', lat, lng);
  emit('update:latitude', lat)
  emit('update:longitude', lng)
}

const handleAddressInput = debounce(async () => {
  if (!localAddress.value || !autocompleteService.value) {
    predictions.value = []
    return
  }

  try {
    const response = await autocompleteService.value.getPlacePredictions({
      input: localAddress.value,
      componentRestrictions: { country: 'SA' }, // Restrict to Saudi Arabia
    })
    predictions.value = response.predictions || []
  } catch (error) {
    console.error('Places API error:', error)
    predictions.value = []
  }
}, 300)

const selectPrediction = (prediction) => {
  if (!placesService.value) return

  placesService.value.getDetails(
    { placeId: prediction.place_id },
    (place, status) => {
      if (status === google.maps.places.PlacesServiceStatus.OK && place.geometry) {
        localAddress.value = place.formatted_address
        predictions.value = []
        updateLocation(place.geometry.location)
      }
    }
  )
}

const handleManualLatitude = (value) => {
  console.log('📍 Manual latitude input:', value);
  emit('update:latitude', value)
}

const handleManualLongitude = (value) => {
  console.log('📍 Manual longitude input:', value);
  emit('update:longitude', value)
}

const handleManualAddress = (value) => {
  console.log('📍 Manual address input:', value);
  emit('update:address', value)
}

// Watch for prop changes
watch(() => props.address, (newValue) => {
  if (newValue !== localAddress.value) {
    localAddress.value = newValue
  }
})

watch([() => props.latitude, () => props.longitude], ([newLat, newLng]) => {
  if (newLat && newLng && map.value && marker.value) {
    const latLng = new google.maps.LatLng(Number(newLat), Number(newLng))
    marker.value.setPosition(latLng)
    map.value.panTo(latLng)
  }
})

watch(localAddress, handleAddressInput)

onMounted(() => {
  console.log('🚀 MapPicker component mounted');
  
  // Load Google Maps script if not already loaded
  if (!window.google) {
    console.log('📦 Google Maps API not loaded, loading script...');
    const script = document.createElement('script')
    script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyDILA7Oj1pjycVqnFH21aUiBQVwJYVGYQs&libraries=places`
    script.async = true
    script.defer = true
    script.onload = () => {
      console.log('✅ Google Maps script loaded successfully');
      initMap();
    }
    script.onerror = (error) => {
      console.error('❌ Failed to load Google Maps script:', error);
    }
    document.head.appendChild(script)
  } else {
    console.log('✅ Google Maps API already available');
    initMap()
  }
})
</script> 