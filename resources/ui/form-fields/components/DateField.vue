<style>
    .mx-datepicker{
        width: 100%;
    }
</style>
<template>
    <date-picker 
        v-model="val" 
        :type="type" 
        :valueType="valueType" 
        :format="format"
    />
</template>

<script>
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    name: "DateField",
    props: {
        field: Object,
        value: String
    },
    mounted() {
        if(this.value)
            this.val = this.value;
    },
    data() {
        return {
            val: false,
            dontUpdate: false
        }
    },
    computed: {
        type(){
            if(!this.field)
                return "date";

            if(this.field.meta.includeTime)
                return "datetime";

            return "date";
        },
        valueType(){
            if(!this.field)
                return "YYYY-MM-DD";

            if(this.field.meta.includeTime)
                return "YYYY-MM-DD HH:mm:ss";

            return "YYYY-MM-DD";
        },
        format(){
            if(!this.field)
                return "MMMM DD, YYYY";

            if(this.field.meta.includeTime)
                return "MMMM DD, YYYY - hh:mm A";

            return "MMMM DD, YYYY";
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
            this.$emit('input', newValue);
        }
    },
    components: {
        DatePicker
    }
}
</script>