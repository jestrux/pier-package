<template>
    <c-form-control mb="6">
        <c-switch :id="option.label" mr="2"
            color="orange" size="md"
            v-model="val"
        />

        <c-form-label :html-for="option.label">
            {{ option.label }}
        </c-form-label>
    </c-form-control>
</template>

<script>

import { CFormControl, CFormLabel, CSwitch } from '@chakra-ui/vue';

export default {
  name: "PierBooleanField",
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
        CFormControl, CFormLabel, CSwitch
    }
}
</script>