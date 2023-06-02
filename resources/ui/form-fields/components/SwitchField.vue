<template>
    <label style="display: flex" class="cursor-pointer justify-start items-center capitalize"
        @click="val = val === 0 ? 1 : 0"
    >
        <span class="rounded-full overflow-hidden relative flex items-center"
            :class="{
                'justify-end': val === 1,
            }"
            style="width: 42px; padding: 1px; padding-bottom: 1px; border: 1px solid #ddd"
            :style="{background: val === 1 ? '#2c5282' : '#eee'}"
        >
            <span class="block rounded-full bg-white"
                style="width: 18px; height: 18px; border: 1px solid #ddd"
            ></span>
        </span>

        <span class="ml-3">
            {{ label }}
        </span>
    </label>
</template>

<script>
export default {
    name: "SwitchField",
    props: {
        label: String,
        value: Number|String
    },
    mounted() {
        if(this.value === 0 || this.value === 1)
            this.val = this.value;
        else{
            this.val = 0;
            this.$emit('input', 0);
        }
    },
    data() {
        return {
            val: 0,
            dontUpdate: false
        }
    },
    watch: {
        value: function(newValue){
            this.dontUpdate = true;
            if(newValue === 0 || newValue === 1)
                this.val = this.value;
            else{
                this.val = 0;
                this.$emit('input', 0);
            }
        },
        val: function(newValue){
            // if(this.dontUpdate){
            //     this.dontUpdate = false;
            //     return;
            // }

            this.$emit('input', newValue);
        }
    },
}
</script>