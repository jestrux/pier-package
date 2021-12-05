<template>
    <c-form-control mb="6">
        <c-form-label :html-for="option.label" color="#777" fontSize="lg" for="fieldLabel">
            {{ option.label }}
        </c-form-label>
        
        <c-input
            :id="option.label"
            :placeholder="option.placeholder"
            type="text"
            size="md"
            v-model="val"
        />
    </c-form-control>
</template>

<script>

import { 
    CFormControl, 
    CFormLabel, 
    CInput 
} from '@chakra-ui/vue';

export default {
  name: "PierTextField",
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
        CInput
    }
}
</script>