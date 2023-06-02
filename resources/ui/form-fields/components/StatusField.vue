<style scoped>
    .status-label{
        font-size: .875rem !important;
        flex: none;
        width: auto;
    }

    .status-label input{
        display: none;
    }
</style>
<template>
    <div class="flex -mr-2">
        <label v-for="(choice, index) in meta.availableStatuses" :key="index" 
            class="status-label rounded px-4 py-2 uppercase text-sm tracking-wider cursor-pointer mr-2 mb-2 border-2 hover:bg-gray-100"
            :style="{
                'background': val === choice.name ? choice.color : '',
                'color': val === choice.name ? 'white' : '',
                'border-color': val === choice.name ? choice.color : ''
            }"
            :for="`${label}Status${choice.name}`">
            {{ choice.name }}

            <input :id="`${label}Status${choice.name}`" type="radio" :name="label" v-model="val" :value="choice.name" />
        </label>
    </div>
</template>

<script>
export default {
    name: "StatusField",
    props: {
        label: String,
        meta: Object,
        value: String
    },
    mounted() {
        if(this.value)
            this.val = this.value;
    },
    data() {
        return {
            val: false
        }
    },
    watch: {
        val: function(newValue){
            this.$emit('input', newValue);
        }
    }
}
</script>