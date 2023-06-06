<template>
  <div>
    <autocomplete
      ref="autocomplete"
      :placeholder="placeholder"
      :search="search"
      :get-result-value="getResultValue"
      :debounceTime="300"
      @submit="handleSubmit"
    />
  </div>
</template>

<script>
import Autocomplete from "@trevoreyre/autocomplete-vue";
import "@trevoreyre/autocomplete-vue/dist/style.css";

export default {
  name: "ModelAutoComplete",
  props: {
    modelName: String,
    modelMainField: String,
    value: Object | String,
    placeholder: String,
  },
  inject: ["API"],
  mounted() {
    if (this.value) this.val = this.value;
    this.$refs.autocomplete.setValue(" ");
  },
  data() {
    return {
      val: false,
      dontUpdate: false,
    };
  },
  methods: {
    search(input) {
      return new Promise(async (resolve, reject) => {
        try {
          const results = await this.API.searchModel(this.modelName, input);
          resolve(results);
        } catch (error) {
          reject("Failed to search for field.");
          console.log("Failed to search for field.", error);
        }
      });
    },
    getResultValue(result) {
      return result.label;
    },
    handleSubmit(result) {
      this.val = result;
      //   this.$refs.autocomplete.setValue(" ");
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
    Autocomplete,
  },
};
</script>