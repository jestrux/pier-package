<style>
    .PierMultiReferenceField{
        padding: 0.2rem 0.3rem;
        box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.3);
        border-radius: 2px;
    }
    
    .PierMultiReferenceField > div:not(.reference-item){
        flex: 1;
        /* background: #ddd; */
        margin: 0.25rem !important;
    }

    .PierMultiReferenceField .autocomplete-input{
        margin: 0 !important;
        padding: 0.25rem !important;
        height: auto !important;
        border: none !important;
        background: transparent;
        box-shadow: none;
        /* box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.3) !important; */
        /* min-width: 0; */
        min-width: 170px !important;
    }

    .PierMultiReferenceField .autocomplete-input + ul{
        min-width: 220px;
    }

    .PierMultiReferenceField .autocomplete-input + ul li{
        padding-left: 1rem;
        padding-right: 1rem;
        background-image: none;
    }
</style>
<template>
    <div class="PierMultiReferenceField relative flex items-center flex-wrap">
        <div v-for="(reference, index) in references" :key="index" 
            class="reference-item bg-gray-100 inline-flex items-center rounded-full pl-3 py-1 text-base mx-1 my-1 border-2 border-gray-300">
            {{ referenceModelKey ? reference[referenceModelKey] : "" }}

            <button type="button" class="inline-flex items-center justify-center w-6 h-6 bg-gray-400 border rounded-full verflow-hidden ml-2 mr-1"
                @click="removeReference(index)">
                <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </div>

        <autocomplete
            ref="autocomplete"
            placeholder="Type here to search"
            :search="search" 
            :get-result-value="getResultValue"
            :debounceTime="300"
            @submit="handleSubmit"
        />

        <!-- <multiselect v-model="selectedCountries" id="ajax" label="name" track-by="code" placeholder="Type to search" open-direction="bottom" :options="countries" :multiple="true" :searchable="true" :loading="isLoading" :internal-search="false" :clear-on-select="false" :close-on-select="false" :options-limit="300" :limit="3" :limit-text="limitText" :max-height="600" :show-no-results="false" :hide-selected="true" @search-change="asyncFind">
            <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>{{ option.name }}</span><span class="custom__remove" @click="remove(option)">❌</span></span></template>
            <template slot="clear" slot-scope="props">
            <div class="multiselect__clear" v-if="selectedCountries.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
            </template><span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
        </multiselect> -->
    </div>
</template>

<script>
import Autocomplete from '@trevoreyre/autocomplete-vue'
import '@trevoreyre/autocomplete-vue/dist/style.css'
import { mapState } from 'vuex';

export default {
    name: "MultiReferenceField",
    props: {
        referenceModel: String,
        referenceModelMainField: String,
        label: String,
        value: Array | String
    },
    inject: ['API'],
    mounted() {
        if(this.value && !this.references.length)
            this.references = this.value;

        this.$refs.autocomplete.setValue(" ");
    },
    data() {
        return {
            val: false,
            dontUpdate: false,
            references: []
        }
    },
    methods: {
        search(input) {
            // if (input.length < 1) { return [] }

            return new Promise(async (resolve, reject) => {
                try {
                    let results = await this.API.searchModel(this.referenceModel, input);
                    const selectedItems = this.references.map(({_id}) => _id);
                    results = results.filter(({_id}) => !selectedItems.includes(_id));
                    resolve(results);
                } catch (error) {
                    reject("Failed to search for field.");
                    console.log("Failed to search for field.", error);
                }
            });
        },
        getResultValue(result) {
            return result.label;
        },
        removeReference(index) {
            this.references.splice(index, 1);
        },
        handleSubmit(result) {
            this.references.push(result);
            this.$refs.autocomplete.setValue(" ");

            const input = this.$refs.autocomplete.$el.querySelector(".autocomplete-input");
            input.blur();
            setTimeout(() => input.focus());
        }
    },
    computed: {
        ...mapState(['models']),
        referenceModelKey(){
            if(this.referenceModelMainField)
                return this.referenceModelMainField;

            return null;
        }
    },
    watch: {
        value: function(newValue){
            this.dontUpdate = true;
            if(newValue && !this.references.length)
                this.references = newValue;
        },
        references: {
            immediate: true,
            handler: function(){
                if(this.dontUpdate){
                    this.dontUpdate = false;
                    return;
                }
                
                this.$emit('input', this.references);
            }
        }
    },
    components: {
        Autocomplete
    }
}
</script>