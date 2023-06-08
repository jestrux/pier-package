<template>
  <div class="pier-form-fields grid grid-cols-12 gap-5">
    <template v-for="field in formattedFields">
      <div
        :key="field.label"
        v-if="field.type == 'group'"
        class="col-span-12 border-b border-neutral-500 mt-4"
      >
        <h3 class="mb-2 text-lg leading-none font-semibold">
          {{ field.cleanLabel ? field.cleanLabel : field.label }}
        </h3>
      </div>

      <div
        v-else
        :key="field.label"
        class="grid grid-cols-12"
        :class="{
          'col-span-12': !field.width || field.width == 'full',
          'col-span-6': field.width == 'half',
          'col-span-4': field.width == 'third',
        }"
      >
        <div
          :class="{
            'col-span-12': !field.stretch || field.stretch == 'full',
            'col-span-6': field.stretch == 'half',
            'col-span-4': field.stretch == 'third',
          }"
        >
          <PierEditorField :field="field" v-model="values[field.label]" />
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import PierEditorField from "./PierEditorField.vue";

export default {
  name: "PierFormFields",
  props: {
    fields: Array,
    values: Object,
  },
  computed: {
    formattedFields() {
      if (!this.fields) return [];

      return this.fields.reduce((agg, field, index) => {
        const newGroup =
          field.group && this.fields[index - 1]?.group != field.group;

        return [
          ...agg,
          ...(newGroup ? [{ type: "group", label: field.group }] : []),
          field,
        ];
      }, []);
    },
  },
  components: {
    PierEditorField,
  },
};
</script>