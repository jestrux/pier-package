<template>
  <div>
    <c-form-control>
      <c-form-label color="#777" fontSize="lg">
        Set Default Value
      </c-form-label>
      <c-input
        id="fieldDefaultValue"
        placeholder="Enter default value here"
        size="md"
        v-model="defaultValue"
        :type="field.type"
      />
    </c-form-control>
    <!-- <PierBooleanField v-if="field.type === 'toggle'" 
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
        </c-text> -->
  </div>
</template>

<script>
import PierBooleanField from "./PierBooleanField";
import PierTextField from "./PierTextField";
import PierChoicesField from "./PierChoicesField";
import PierRadioField from "./PierRadioField";

import { CText, CFormControl, CFormLabel, CInput, CSelect } from "@chakra-ui/vue";

export default {
  name: "PierModelFieldDefaultValue",
  props: {
    value: Object | String | Boolean,
  },
  mounted() {
    const field = {...this.value};
    if (field) {
      const { value, defaultValue, type } = field;
      if (value === undefined && defaultValue !== undefined)
        field.value = defaultValue;
        
      if (type && type.value)
        field.type = type.value;

      if (field.default)
        this.defaultValue = field.default;
    }

    this.field = field;
  },
  data() {
    return {
      field: {},
      defaultValue: null,
    };
  },
  watch: {
    defaultValue: {
      deep: true,
      handler: function (newValue) {
        this.$emit("input", newValue);
      },
    },
  },
  components: {
    PierTextField,
    PierBooleanField,
    PierChoicesField,
    PierRadioField,
    CText,
    CFormControl,
    CFormLabel,
    CInput,
    CSelect,
  },
};
</script>