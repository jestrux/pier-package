<style scoped>
    .status-label{
        font-size: .875rem !important;
        flex: none;
        width: auto;
        position: relative;
    }

    .status-label input{
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
</style>
<template>
    <div class="flex -mr-2">
        <label v-for="(choice, index) in choices" :key="index" 
            class="status-label rounded px-4 py-2 uppercase text-sm tracking-wider cursor-pointer mr-2 mb-2 border-2 hover:bg-gray-100"
            :style="{
                'background': choice.selected ? choice.color : '',
                'color': choice.selected ? 'white' : '',
                'border-color': choice.selected ? choice.color : ''
            }"
            :for="`${label}Status${choice.name}`">
            {{ choice.name }}

            <input :id="`${label}Status${choice.name}`" type="radio" :name="label" v-model="val" :value="choice.name" :checked="choice.selected" :required="required" />
        </label>
    </div>
</template>

<script>
export default {
    name: "StatusField",
    props: {
        label: String,
        meta: Object,
        value: String,
        required: String | Boolean
    },
    mounted() {
        if(this.value)
            this.val = this.value;
    },
    data() {
        return {
            val: "",
            dontUpdate: false
        }
    },
    computed: {
        choices() {
            return this.meta.availableStatuses.map((choice) => ({
                ...choice,
                selected: this.val?.toString().toLowerCase() == choice.name?.toString().toLowerCase()
            }))
        }
    },
    watch: {
        value: function(newValue){
            this.dontUpdate = true;
            this.val = newValue;
        },
        val: function(newValue){
            this.$emit('input', newValue);
        }
    }
}
</script>