<template>
    <c-form-control mb="6">
        <c-form-label :html-for="option.label" color="#777" fontSize="lg" for="fieldLabel">
            {{ option.label }}
        </c-form-label>

        <c-select v-model="val" placeholder="Choose one">
            <option v-for="(choice, index) in option.choices" 
                :key="index"
                :value="choice"
            >
                {{ choice }}
            </option>
        </c-select>
    </c-form-control>
</template>

<script>

import { 
    CFormControl, 
    CFormLabel, 
    CSelect 
} from '@chakra-ui/vue';

export default {
  name: "PierChoicesField",
    props: {
        option: Object,
        value: Object | String | Boolean
    },
    mounted() {
        if(this.option){
            const {value} = this.option;  
            if(value !== undefined)
                this.val = value;
        }
    },
    data() {
        return {
            val: ""
        }
    },
    watch: {
        val: function(newValue){
            this.$emit('input', newValue);
        }
    },
    components: {
        CFormControl, 
        CFormLabel, 
        CSelect
    }
}
</script>