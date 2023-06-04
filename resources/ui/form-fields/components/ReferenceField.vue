<style>
    .autocomplete-input{
        padding-left: 48px !important;
    }
</style>
<template>
    <div>
        <div :class="{'hidden': !val, 'flex' : val}" class="flex items-center px-2 rounded bg-gray-200 bg-opacity-25 border border-gray-300" style="height: 40px">
            <span class="ml-1">{{displayValue}}</span>

            <button type="button" class="ml-auto h-full flex items-center text-primary text-xs uppercase px-1 border-none bg-transparent" 
                @click="changeValue">
                Change
            </button>
        </div>

        <autocomplete :class="{'hidden': val}"
            ref="autocomplete"
            :placeholder="`Type to search for ${label}`"
            :search="search" 
            :get-result-value="getResultValue"
            :debounceTime="300"
            @submit="handleSubmit"
        />
    </div>
</template>

<script>
import Autocomplete from '@trevoreyre/autocomplete-vue'
import '@trevoreyre/autocomplete-vue/dist/style.css'

export default {
    name: "ReferenceField",
    props: {
        referenceModel: String,
        referenceModelMainField: String,
        label: String,
        value: Object|String
    },
    inject: ['API'],
    mounted() {
        if(this.value) this.val = this.value;
        this.$refs.autocomplete.setValue(" ");
    },
    data() {
        return {
            val: false,
            dontUpdate: false
        }
    },
    methods: {
        search(input) {
            // if (input.length < 1) { return [] }

            return new Promise(async (resolve, reject) => {
                try {
                    const results = await this.API.searchModel(this.referenceModel, input);
                    resolve(results);
                } catch (error) {
                    reject("Failed to search for field.");
                    console.log("Failed to search for field.", error);
                }
            });
        },
        changeValue() {
            this.val = null;
            this.$nextTick(() => {
                this.$refs.autocomplete.$el.querySelector(".autocomplete-input").focus()
            });
        },
        getResultValue(result) {
            return result.label;
        },
        handleSubmit(result) {
            this.val = result;
            this.$refs.autocomplete.setValue(" ");
        }
    },
    computed: {
        displayValue(){
            if(this.val && this.referenceModelMainField)
                return this.val[this.referenceModelMainField];

            return null;
        }
    },
    watch: {
        value: function(newValue){
            this.dontUpdate = true;
            this.val = newValue;
        },
        val: function(newValue){
            if(this.dontUpdate){
                this.dontUpdate = false;
                return;
            }
            
            if(newValue != null)
                this.$emit('input', newValue);
        }
    },
    components: {
        Autocomplete
    }
}
</script>