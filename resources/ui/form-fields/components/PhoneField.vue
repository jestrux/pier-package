<style>
    form .input-group .PierTelInput input {
        margin-top: 0;
        box-shadow: none;
        border: none !important;
        border-radius: 0 !important;
        border-left: 1px solid #C8C8C8 !important;
    }

    .PierTelInput{
        border: 1px solid #C8C8C8 !important;
        border-radius: 0.35rem !important;
        overflow: hidden;
        /* border: none !important; */
        /* box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.3) !important; */
    }
</style>

<template>
    <vue-tel-input 
        class="PierTelInput"
        v-model="val" 
        :placeholder="`Enter ${label}`"
    />
</template>

<script>
import { VueTelInput } from 'vue-tel-input';

export default {
    name: "PhoneField",
    props: {
        label: String,
        value: String
    },
    mounted() {
        if(this.value)
            this.val = this.value;
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
        VueTelInput,
    }
}
</script>