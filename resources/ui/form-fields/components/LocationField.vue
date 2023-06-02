<style>
    .autocomplete-input{
        padding-left: 48px !important;
    }
</style>
<template>
    <div v-if="val" class="flex items-center px-2 rounded bg-gray-200 bg-opacity-25 border border-gray-300" style="height: 40px">
        <span class="ml-2 text-sm">{{val.name}}</span>

        <button type="button" class="ml-auto h-full flex items-center text-blue-900 text-xs uppercase px-1 border-none bg-transparent" 
            @click="val = null">
            Change
        </button>
    </div>
    <div v-else id="locationField">
        <autocomplete 
            :placeholder="`Type to search for ${label}`"
            :search="search" 
            :get-result-value="({name}) => name"
            :debounceTime="300"
            @submit="handleSubmit"
        />
        <!-- <div v-if="mapLocation && mapLocation.length" class="mt-3">
            <FieldPreview type="location" :url="mapLocation" />
        </div> -->
    </div>
</template>

<script>
import Autocomplete from '@trevoreyre/autocomplete-vue'
import '@trevoreyre/autocomplete-vue/dist/style.css'
import FieldPreview from "../components/FieldPreview";
import { getMapLocation } from '../../Utils';

async function getLocation(location, countries = ""){
    let countryFilter = "";
    if(countries && countries.length)
        countryFilter = "&countrycodes="+countries;

    const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${location}&format=json&limit=10&addressdetails=1${countryFilter}`);
    const results = await response.json();

    return results.map(({display_name, lon, lat}) => ({
        name: display_name,
        coords: [lon, lat]
    }));
}


export default {
    name: "LocationField",
    props: {
        referenceModel: String,
        label: String,
        meta: Object|String,
        value: Object|String
    },
    mounted() {
        try {
            if(this.value)
                this.val = JSON.parse(this.value);
        } catch (error) {
            
        }

    },
    data() {
        return {
            val: false
        }
    },
    methods: {
        search(input) {
            if (input.length < 1) { return [] }

            return new Promise(async (resolve, reject) => {
                try {
                    const results = await getLocation(input, this.meta.countries);
                    resolve(results);
                } catch (error) {
                    reject("Failed to search for field.");
                    console.log("Failed to search for field.", error);
                }
            });
        },
        handleSubmit(result) {
            this.val = JSON.stringify(result)
        }
    },
    computed: {
        mapLocation(){
            return getMapLocation(this.val, 170, 170);
        }
    },
    watch: {
        value: function(newValue){
            try {
                if(newValue)
                    this.val = JSON.parse(newValue);
            } catch (error) {
                
            }
        },
        val: function(newValue){
            this.$emit('input', newValue);
        }
    },
    components: {
        Autocomplete,
        FieldPreview
    }
}
</script>