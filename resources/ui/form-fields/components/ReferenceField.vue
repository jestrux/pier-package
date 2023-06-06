<template>
    <div>
        <div :class="{'hidden': !val, 'flex' : val}" class="flex items-center px-2 rounded bg-gray-200 bg-opacity-25 border border-gray-300" style="height: 40px">
            <span class="ml-1">{{displayValue}}</span>

            <button type="button" class="ml-auto h-full flex items-center text-primary text-xs uppercase px-1 border-none bg-transparent" 
                @click="changeValue">
                Change
            </button>
        </div>

        <div ref="autocomplete">
            <autocomplete
                v-if="!val"
                :model="referenceModel"
                :modelMainField="referenceModelMainField"
                :placeholder="`Type to search for ${label}`"
                v-model="val"
            />
        </div>
    </div>
</template>

<script>
import Autocomplete from '../../components/ModelAutoComplete.vue'

export default {
    name: "ReferenceField",
    props: {
        referenceModel: String,
        referenceModelMainField: String,
        label: String,
        value: Object|String
    },
    data() {
        return {
            val: "",
            dontUpdate: false,
        };
    },
    computed: {
        displayValue() {
            if (this.val && this.referenceModelMainField) return this.val[this.referenceModelMainField];

            return null;
        },
    },
    methods: {
        changeValue() {
            this.val = null;
            this.$nextTick(() => {
                this.$refs.autocomplete.querySelector(".autocomplete-input").focus();
            });
        },
    },
    watch: {
        value: function (newValue) {
            this.dontUpdate = true;
            this.val = newValue;
        },
        val: function (newValue) {
            if (this.dontUpdate) {
                this.dontUpdate = false;
                return;
            }

            if (newValue != null) this.$emit("input", newValue);
        },
    },
    components: {
        Autocomplete
    }
}
</script>