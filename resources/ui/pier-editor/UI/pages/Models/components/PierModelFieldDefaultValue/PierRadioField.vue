<template>
    <c-form-control mb="6">
        <c-form-label :html-for="option.label" color="#777" fontSize="lg" for="fieldLabel">
            {{ option.label }}
        </c-form-label>

        <c-stack is-inline v-model="val">
            <custom-radio v-for="(choice, index) in option.choices" 
                :key="index"
                :is-checked="choice.value && val === choice.value || val === choice"
                @clicked="val = choice.value || choice"
            >
                {{ choice.label || choice }}
            </custom-radio>
        </c-stack>
    </c-form-control>
</template>

<script>

import { 
    CFormControl, 
    CFormLabel, 
    CButton, CStack
} from '@chakra-ui/vue';

const CustomRadio = {
    name: 'CustomRadio',
    props: {
        isChecked: Boolean,
        isDisabled: Boolean,
        value: [String, Number],
        mx: [String, Number]
    },
    template: `
    <c-button
        style="box-shadow: none"
        v-bind="$props"
        height="30px"
        mt="1"
        mb="1"
        mr="2"
        px="6"
        :variant-color="isChecked ? 'orange.300' : 'gray'"
        :backgroundColor="isChecked ? '#4b4b4b' : '#2c2c2c'"
        :color="isChecked ? '#bfbdbd' : '#999'"
        borderRadius="50px"
        role="radio"
        :aria-checked="isChecked"
        @click="$emit('clicked')"
        >
        <slot />
    </c-button>
    `,
    components: {
        CButton
    }
}

export default {
  name: "PierRadioField",
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
        CStack,
        CustomRadio
    }
}
</script>