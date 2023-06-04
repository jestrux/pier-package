<style scoped>
    .status-label{
        height: 38px;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: .875rem !important;
        flex: none;
        width: auto;
        position: relative;
        border: 1px solid #C8C8C8;
        font-weight: 600;
    }

    .status-label.selected{
        background: #E8E8E8;
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
            class="status-label rounded-md px-3 py-2.5 text-sm leading-none cursor-pointer mr-2 mb-2 hover:bg-gray-100"
            :class="{'selected': choice.selected}"
            :for="`${label}Status${choice.name}`"
        >
            <svg
				class="w-5 h-5 -ml-0.5"
                :class="{'text-primary' : choice.selected, 'opacity-60': !choice.selected}"
				fill="currentColor"
				viewBox="0 0 24 24"
			>
                <path v-if="choice.selected" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                <path v-else d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
			</svg>

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