<template>
    <c-box>
        <PierModelFieldOption v-model="model"
            :key="selectedModel.name"
        />
        <PierModelFieldOption 
            :key="selectedModel.name + `${selectedField ? selectedField.label : 'field'}`" 
            v-model="field" 
        />
    </c-box>
</template>

<script>

import {
  CBox
} from "@chakra-ui/vue";
import { mapState } from 'vuex';
import PierModelFieldOption from "./PierModelFieldOption";

export default {
    name: "PierModelReferenceFieldOption",
    props: {
        value: Object
    },
    mounted() {
        const options = {...this.value};
        if(options){
            const {model, field} = options;
            if(model && model.value !== undefined){
                const selectedModel = this.models.find(({name}) => name === model.value);
                this.selectedModel = selectedModel;

                this.$nextTick(() => {
                    if(field && field.value !== undefined && selectedModel){
                        const fields = selectedModel.fields;
                        this.selectedField = fields.find(({label}) => label === field.value);
                    }
                });
            }
        }

        // this.option = option;
    },
    data: function(){
        return {
            selectedModel: {},
            selectedField: {},
            options: {}
        }
    },
    computed: {
        ...mapState(['models']),
        model: {
            get: function() {
                const model = {
                    label: "Reference Model",
                    type: "choice",
                    choices: []
                };

                if(this.models && this.models.length){
                    model.choices = this.models.map(({name}) => name);
                    if(this.selectedModel)
                        model.value = this.selectedModel.name;
                }

                return model;
            },
            set: function(newModel) {
                if(!newModel.value || !newModel.value.length)
                    return;

                const selectedModel = this.models.find(({name}) => name === newModel.value);
                this.selectedModel = selectedModel;
                this.selectedField = {};
            }
        },
        field: {
            get: function(){
                const field = {
                    label: "Preview Field",
                    type: "choice",
                    choices: []
                };

                if(this.models && this.models.length && this.selectedModel.fields){
                    const fields = this.selectedModel.fields;
                    field.choices = fields.map(({label}) => label);

                    if(this.selectedField)
                        field.value = this.selectedField.label;
                }

                return field;
            },
            set: function(newField) {
                if(this.selectedModel.fields){
                    const fields = this.selectedModel.fields;
                    this.selectedField = fields.find(({label}) => label === newField.value);
                }
            }
        }
    },
    watch: {
        selectedModel: function(newValue){
            const options = {...this.options};
            if(!newValue || !newValue.name || !newValue.name.length){
                delete options.model;
                this.options = options;
                return;
            }

            this.options = {
                ...options, 
                model: { value: newValue.name },
                mainField: { value: newValue.display_field },
            };
        },
        selectedField: function(newValue){
            const options = {...this.options};
            if(!newValue || !newValue.label){
                delete options.field;
                this.options = options;
                return;
            };

            let meta = {type: newValue.type}

            if(newValue.meta){
                meta = {
                    ...meta,
                    ...newValue.meta
                }
            };

            this.options = {
                ...options, 
                field: {
                    value: newValue.label,
                    meta
                }
            };
        },
        options: function(newValue){
            this.$emit('input', this.options);
        }
    },
    components: {
        CBox,
        PierModelFieldOption
    }
}
</script>