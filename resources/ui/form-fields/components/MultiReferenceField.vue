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

    .PierMultiReferenceField.add-inline .autocomplete-input {
        display: none;
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
    <div>
        <div v-if="!addReferenceInline" class="PierMultiReferenceField relative flex items-center flex-wrap">
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
        </div>

        <div v-else>
            <div :class="{
                'grid grid-cols-2 gap-2': referencePreviewField && referencePreviewField.type == 'image',
                'flex flex-col gap-1': !referencePreviewField || !referencePreviewField.type != 'image',
            }">
                <template v-for="(reference, index) in references">
                    <div :key="index" v-if="referencePreviewField && referencePreviewField.type == 'image'"
                        class="flex-1 min-w-0 truncate relative"
                    >
                        <FieldPreview
                            type="image" 
                            :url="reference[referencePreviewField.label]"
                            :isDp="field && field.meta && field.meta.face"
                        />

                        <button type="button" class="absolute right-0.5 top-1 flex-shrink-0 inline-flex items-center justify-center w-6 h-6 bg-neutral-100 hover:bg-neutral-200 border rounded-full verflow-hidden ml-2 mr-1"
                            @click="removeReference(index, {permanentDelete: true})">
                            <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                        </button>
                    </div>

                    <div :key="index" v-else class="flex items-center justify-between py-2 text-lg font-bold border-b">
                        <div class="truncate">{{ referenceModelKey ? reference[referenceModelKey] : "" }}</div>

                        <button type="button" class="flex-shrink-0 inline-flex items-center justify-center w-6 h-6 bg-neutral-100 hover:bg-neutral-200 border rounded-full verflow-hidden ml-2 mr-1"
                            @click="removeReference(index, {permanentDelete: true})">
                            <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <div style="position: relative; pointer-events: none">
                <input
                    style="position: absolute; top: -3rem; opacity: 0"
                    type="text"
                    :value="displayValue"
                    :required="field.required"
                />
            </div>

            <button type="button" @click="addNewReference" class="mt-3 self-start border border-current flex font-semibold gap-1 items-center rounded-full text-primary text-sm leading-none hover:bg-neutral-200/50" style="padding: 0.4rem 1rem;">
                <svg class="-ml-1" height="18px" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                <span class="lowercase first-letter:uppercase inline-block">New entry</span>
            </button>
        </div>
    </div>
</template>

<script>
import Autocomplete from '@trevoreyre/autocomplete-vue'
import '@trevoreyre/autocomplete-vue/dist/style.css'
import { mapState } from 'vuex';
import FieldPreview from '../components/FieldPreview';

export default {
    name: "MultiReferenceField",
    props: {
        field: Object,
        label: String,
        value: Array | String
    },
    inject: ['API', 'PierCMSConfig'],
    mounted() {
        if(this.value && !this.references.length)
            this.references = this.value;

        if(!this.addReferenceInline) this.$refs.autocomplete.setValue(" ");
    },
    data() {
        return {
            val: false,
            dontUpdate: false,
            references: []
        }
    },
    methods: {
        async addNewReference() {
            const newReference = await window.showPierReferenceModalForm(this.referenceModel);
            if(newReference) {
                this.$set(this.references, this.references.length, newReference);
                
                const PierFormWrapper = this.$el.closest(".PierFormWrapper");
                if(PierFormWrapper) {
                    PierFormWrapper.dispatchEvent(
                        new CustomEvent("autosave-reference-field", {
                            detail: {
                                [this.field.label]: this.references.map(({_id}) => _id)
                            },
                        })
                    );
                }
            }
        },
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
        removeReference(index, {permanentDelete = false} = {}) {
            if(this.references[index] && this.references[index]._id && permanentDelete) {
                this.API.deleteRecord(this.referenceModel, this.references[index]._id);
            }

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
        displayValue() {
            if(!this.references || this.references.length) return "";

            return this.references.map(({ _id }) => _id).join("");
        },
        referenceModel() {
            if(!this.field || !this.field.meta) return null; 

            return this.field.meta.model;
        },
        referenceModelMainField() {
            if(!this.field || !this.field.meta) return null; 

            return this.field.meta.mainField;
        },
        addReferenceInline() {
            if(!this.field || !this.field.meta) return null; 

            return this.field.meta.addInline;
        },
        referencePreviewField() {
            if(!this.field || !this.field.meta || !this.field.meta.type) return null; 

            return {
                type: this.field.meta.type,
                label: this.field.meta.field,
            };
        },
        referenceModelKey(){
            if(this.referenceModelMainField)
                return this.referenceModelMainField;

            return null;
        }
    },
    watch: {
        referencePreviewField(newValue) {
            console.log("referencePreviewField: ", newValue)
        },
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
        Autocomplete,
        FieldPreview
    }
}
</script>