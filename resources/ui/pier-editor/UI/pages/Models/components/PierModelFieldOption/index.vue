<template>
    <div>
        <PierBooleanField v-if="option.type === 'toggle'" 
            :option="option"
            v-model="option.value" />
        <PierRadioField v-else-if="option.type == 'radio'" 
            :option="option"
            v-model="option.value" />
        <PierChoicesField v-else-if="option.type == 'choice'" 
            :option="option"
            v-model="option.value" />
        <PierTextField v-else 
            :option="option"
            v-model="option.value" />

        <c-text v-if="option.hint && option.hint.length" color="#999" marginTop="-1rem" mb="6" fontSize="sm">
            Hint: <span class="opacity-75">{{ option.hint }}</span>
        </c-text>
    </div>
</template>

<script>

import PierBooleanField from './PierBooleanField';
import PierTextField from './PierTextField';
import PierChoicesField from './PierChoicesField';
import PierRadioField from './PierRadioField';

import {
  CText,
} from '@chakra-ui/vue';

export default {
  name: "PierModelFieldOption",
    props: {
        value: Object | String | Boolean
    },
    mounted() {
        const option = this.value;
        if(option){
            const {value, defaultValue} = option;  
            if(value === undefined && defaultValue !== undefined)
                option.value = defaultValue;
        }

        this.option = option;
    },
    data() {
        return {
            option: {},
        }
    },
    watch: {
        option: {
            deep: true,
            handler: function(newValue){
                this.$emit('input', newValue);
            }
        }
    },
    components: {
        PierTextField,
        PierBooleanField,
        PierChoicesField,
        PierRadioField,
        CText
    }
}
</script>