<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class LocationPickerField extends BaseFieldType
{
    public function getName(): string
    {
        return 'location';
    }

    public function getLabel(): string
    {
        return 'Location Picker';
    }

    public function getIcon(): string
    {
        return 'bi bi-geo-alt';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $attributes = $this->getCommonAttributes($field);
        $mapId = 'location_map_'.$field->id;
        $searchId = 'location_search_'.$field->id;
        $wireModel = 'formData.field_'.$field->id;

        // Get configuration options
        $defaultLat = $field->options['default_lat'] ?? 37.7749;
        $defaultLng = $field->options['default_lng'] ?? -122.4194;
        $defaultZoom = $field->options['default_zoom'] ?? 13;
        $mapHeight = $field->options['map_height'] ?? 400;
        $enableSearch = $field->options['enable_search'] ?? true;
        $showCoordinates = $field->options['show_coordinates'] ?? true;

        // Parse existing value
        $locationData = is_string($value) ? json_decode($value, true) : $value;
        $lat = $locationData['lat'] ?? $defaultLat;
        $lng = $locationData['lng'] ?? $defaultLng;
        $address = $locationData['address'] ?? '';

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Render label
        $html .= $this->renderLabel($field, $attributes['id']);

        // Load Leaflet CSS and JS (only once per page)
        static $leafletLoaded = false;
        if (! $leafletLoaded) {
            $html .= '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>';
            $html .= '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>';
            $leafletLoaded = true;
        }

        // Location picker wrapper with Alpine.js
        $html .= '<div x-data="{';
        $html .= '  map: null,';
        $html .= '  marker: null,';
        $html .= '  lat: '.$lat.',';
        $html .= '  lng: '.$lng.',';
        $html .= '  address: \''.addslashes($address).'\',';
        $html .= '  locationData: $wire.entangle(\''.$wireModel.'\'),';
        $html .= '  updateLocation() {';
        $html .= '    this.locationData = JSON.stringify({lat: this.lat, lng: this.lng, address: this.address});';
        $html .= '  },';
        $html .= '  searchLocation() {';
        $html .= '    const query = document.getElementById(\''.$searchId.'\').value;';
        $html .= '    if (query) {';
        $html .= '      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)';
        $html .= '        .then(r => r.json())';
        $html .= '        .then(data => {';
        $html .= '          if (data && data[0]) {';
        $html .= '            this.lat = parseFloat(data[0].lat);';
        $html .= '            this.lng = parseFloat(data[0].lon);';
        $html .= '            this.address = data[0].display_name;';
        $html .= '            this.map.setView([this.lat, this.lng], '.$defaultZoom.');';
        $html .= '            this.marker.setLatLng([this.lat, this.lng]);';
        $html .= '            this.updateLocation();';
        $html .= '          }';
        $html .= '        });';
        $html .= '    }';
        $html .= '  }';
        $html .= '}" x-init="';
        $html .= '$nextTick(() => {';
        $html .= '  setTimeout(() => {';
        $html .= '    if (typeof L !== \'undefined\') {';
        $html .= '      const mapEl = document.getElementById(\''.$mapId.'\');';
        $html .= '      if (mapEl && !map) {';
        $html .= '        map = L.map(\''.$mapId.'\').setView([lat, lng], '.$defaultZoom.');';
        $html .= '        L.tileLayer(\'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png\', {';
        $html .= '          attribution: \'© OpenStreetMap contributors\'';
        $html .= '        }).addTo(map);';
        $html .= '        marker = L.marker([lat, lng], {draggable: true}).addTo(map);';
        $html .= '        const self = this;';
        $html .= '        marker.on(\'dragend\', function(e) {';
        $html .= '          const pos = e.target.getLatLng();';
        $html .= '          self.lat = pos.lat;';
        $html .= '          self.lng = pos.lng;';
        $html .= '          self.updateLocation();';
        $html .= '        });';
        $html .= '        map.on(\'click\', function(e) {';
        $html .= '          self.lat = e.latlng.lat;';
        $html .= '          self.lng = e.latlng.lng;';
        $html .= '          marker.setLatLng([self.lat, self.lng]);';
        $html .= '          self.updateLocation();';
        $html .= '        });';
        $html .= '        if (locationData) {';
        $html .= '          const data = JSON.parse(locationData);';
        $html .= '          if (data.lat && data.lng) {';
        $html .= '            lat = data.lat;';
        $html .= '            lng = data.lng;';
        $html .= '            address = data.address || \'\';';
        $html .= '            map.setView([lat, lng], '.$defaultZoom.');';
        $html .= '            marker.setLatLng([lat, lng]);';
        $html .= '          }';
        $html .= '        }';
        $html .= '      }';
        $html .= '    }';
        $html .= '  }, 100);';
        $html .= '});';
        $html .= '">';

        // Search input
        if ($enableSearch) {
            $html .= '<div class="input-group mb-2">';
            $html .= '<input type="text" id="'.$searchId.'" class="form-control" placeholder="Search for a location..." @keyup.enter="searchLocation()">';
            $html .= '<button type="button" class="btn btn-outline-secondary" @click="searchLocation()"><i class="bi bi-search"></i> Search</button>';
            $html .= '</div>';
        }

        // Map container (wire:ignore prevents Livewire from morphing this element)
        $html .= '<div wire:ignore id="'.$mapId.'" class="location-map border rounded" style="height: '.$mapHeight.'px;"></div>';

        // Coordinates display
        if ($showCoordinates) {
            $html .= '<div class="small text-muted mt-2">';
            $html .= '<i class="bi bi-geo-alt-fill me-1"></i>';
            $html .= '<span x-text="\'Latitude: \' + lat.toFixed(6) + \', Longitude: \' + lng.toFixed(6)"></span>';
            $html .= '</div>';
        }

        $html .= '</div>'; // End Alpine wrapper

        // Hidden input for validation
        $html .= '<input type="hidden" '.$this->getWireModelAttribute($field).'="'.$wireModel.'" '.$this->getValidationAttributes($field).'>';

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $mapId = 'location_map_builder_'.$field->id;
        $defaultLat = $field->options['default_lat'] ?? 37.7749;
        $defaultLng = $field->options['default_lng'] ?? -122.4194;
        $defaultZoom = $field->options['default_zoom'] ?? 13;
        $mapHeight = $field->options['map_height'] ?? 400;
        $enableSearch = $field->options['enable_search'] ?? true;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, '');

        // Load Leaflet CSS and JS (only once per page)
        static $leafletLoadedBuilder = false;
        if (! $leafletLoadedBuilder) {
            $html .= '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>';
            $html .= '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>';
            $leafletLoadedBuilder = true;
        }

        // Search input preview
        if ($enableSearch) {
            $html .= '<div class="input-group mb-2">';
            $html .= '<input type="text" class="form-control" placeholder="Search for a location..." disabled>';
            $html .= '<button type="button" class="btn btn-outline-secondary" disabled><i class="bi bi-search"></i> Search</button>';
            $html .= '</div>';
        }

        // Interactive map with Alpine.js
        $html .= '<div x-data="{ map: null }" x-init="';
        $html .= '$nextTick(() => {';
        $html .= '  setTimeout(() => {';
        $html .= '    if (typeof L !== \'undefined\') {';
        $html .= '      const mapEl = document.getElementById(\''.$mapId.'\');';
        $html .= '      if (mapEl && !map) {';
        $html .= '        map = L.map(\''.$mapId.'\').setView(['.$defaultLat.', '.$defaultLng.'], '.$defaultZoom.');';
        $html .= '        L.tileLayer(\'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png\', {';
        $html .= '          attribution: \'© OpenStreetMap contributors\'';
        $html .= '        }).addTo(map);';
        $html .= '        L.marker(['.$defaultLat.', '.$defaultLng.']).addTo(map);';
        $html .= '      }';
        $html .= '    }';
        $html .= '  }, 100);';
        $html .= '});';
        $html .= '">';

        // Map container (wire:ignore prevents Livewire from morphing this element)
        $html .= '<div wire:ignore id="'.$mapId.'" class="location-map border rounded" style="height: '.$mapHeight.'px;"></div>';

        $html .= '</div>'; // End Alpine wrapper

        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'json';
        $rules[] = 'nullable';

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        // Store as JSON with lat, lng, and address
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $value;
            }
        }

        return json_encode($value);
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'default_lat' => [
                'type' => 'number',
                'label' => 'Default Latitude',
                'tab' => 'options',
                'target' => 'options',
                'default' => 37.7749,
                'step' => 0.000001,
                'help' => 'Default map center latitude (e.g., 37.7749 for San Francisco)',
            ],
            'default_lng' => [
                'type' => 'number',
                'label' => 'Default Longitude',
                'tab' => 'options',
                'target' => 'options',
                'default' => -122.4194,
                'step' => 0.000001,
                'help' => 'Default map center longitude (e.g., -122.4194 for San Francisco)',
            ],
            'default_zoom' => [
                'type' => 'number',
                'label' => 'Default Zoom Level',
                'tab' => 'options',
                'target' => 'options',
                'default' => 13,
                'min' => 1,
                'max' => 18,
                'help' => 'Map zoom level (1 = world view, 18 = street level)',
            ],
            'map_height' => [
                'type' => 'number',
                'label' => 'Map Height',
                'tab' => 'options',
                'target' => 'options',
                'default' => 400,
                'min' => 200,
                'max' => 800,
                'help' => 'Height of the map in pixels',
            ],
            'enable_search' => [
                'type' => 'switch',
                'label' => 'Enable Location Search',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'help' => 'Show search box to find locations by address',
            ],
            'show_coordinates' => [
                'type' => 'switch',
                'label' => 'Show Coordinates',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'help' => 'Display latitude and longitude below the map',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            // No additional validation options beyond required
            // Location is either selected (has lat/lng) or not
        ];
    }
}
