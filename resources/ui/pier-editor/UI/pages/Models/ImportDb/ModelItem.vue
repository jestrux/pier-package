<template>
  <c-box
    background-color="#222"
    py="2"
    px="4"
    d="flex"
    align-items="center"
    rounded="md"
  >
    <c-text color="#888" fontSize="lg">
      {{ model.name }}
    </c-text>

    <c-box
      ml="auto"
      height="20px"
      d="flex"
      align-items="center"
      justify-content="center"
    >
      <c-spinner v-if="model.status == 'saving'" size="sm" color="#555" />
      <c-icon
        v-if="model.status == 'saved'"
        name="check-circle"
        color="green.400"
        size="14px"
      />
    </c-box>
  </c-box>
</template>

<script>
import { CBox, CText, CIcon, CSpinner } from "@chakra-ui/vue";
import { insertModel } from "../../../../services/API";

export default {
  name: "ModelItem",
  props: ["model"],
  mounted() {
    if (!["saved", "saving"].includes(this.model.status)) {
      this.saveModel();
    }
  },
  methods: {
    async saveModel() {
      this.$emit("change", "saving");
      let fields = this.model.fields;
      let settings = this.model.settings;

      try {
        const parsedFields = JSON.parse(fields);
        fields = parsedFields;
      } catch (error) {
          
      }
      
      try {
        const parsedSettings = JSON.parse(settings);
        settings = parsedSettings;
      } catch (error) {
          
      }

      const model = {
        ...this.model,
        displayField: this.model.display_field,
        name: this.model.name.replace(/ /g,""),
        fields,
        settings,
      };
      console.log("Model: ", model);
      delete model.settings;
      await insertModel(model);
      this.$emit("change", "saved");
    },
  },
  components: {
    CBox,
    CText,
    CIcon,
    CSpinner,
  },
};
</script>