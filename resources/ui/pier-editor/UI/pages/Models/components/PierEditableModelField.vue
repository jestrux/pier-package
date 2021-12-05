<style>
.fieldItem:not(:hover) .fieldActionButtons {
  display: none;
}

#fieldTypeOptions {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
  grid-gap: 0.8rem;
  /* margin-top: -2rem; */
  user-select: none;
}
.action-label {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  padding: 0.8rem;
  border: 1px solid #555;
  border-radius: 5px;
  width: auto;
  cursor: pointer;
  color: #777;
  font-size: 0.8rem;
  text-transform: capitalize;
}
.action-label.active {
  border-color: #8c6d52;
  background: #28231f;
  color: #ffba7f;
}

.action-label svg {
  display: inline-block;
  margin-bottom: 0.2rem;
}

.action-label span:last-child {
  display: block;
  width: 100%;
}

.action-label input {
  display: none !important;
}
</style>
<template>
  <c-pseudo-box
    role="group"
    mb="0"
    :pointerEvents="!selectable ? 'none' : 'auto'"
  >
    <div style="margin-bottom: -0.6rem">
      <c-box rounded="md" color="#777" px="4" py="3" background="#222">
        <c-box
          mb="1"
          d="flex"
          align-items="center"
          justify-content="space-between"
        >
          <c-box d="flex" align-items="center">
            <c-box mr="3">
              <MDIcon
                :icon="
                  field.type && field.type.value ? field.type.value : 'down'
                "
                :size="20"
              />
            </c-box>

            <c-text fontSize="lg" color="#999">
              <span style="color: #555" v-if="!field.type || !field.type.label"
                >Pick field type</span
              >
              <span v-else>
                {{ field.label || "New Field" }}
              </span>
            </c-text>&nbsp;
            <c-text
              fontSize="md"
              color="#777"
              v-if="field.type && field.type.label"
              >( {{ field.type.label.toLowerCase() }} )</c-text
            >
          </c-box>

          <template v-if="hasCancelListener">
            <c-box v-if="selected" d="flex" align-items="center">
              <c-button
                v-if="!editing"
                size="sm"
                align-self="flex-start"
                :variant="field.type && field.type.label ? 'outline' : 'ghost'"
                color="#777"
                @click="$emit('removeField')"
              >
                <c-icon
                  v-if="!field.type || !field.type.label"
                  name="close"
                  size="11px"
                  mr="3"
                />Cancel
              </c-button>

              <c-button
                v-if="field.type && field.type.label"
                size="sm"
                :disabled="!field.label || !field.type || !field.type.label"
                ml="3"
                px="4"
                variant-color="orange"
                @click="$emit('cancelAddField')"
              >
                Done
              </c-button>
            </c-box>

            <c-pseudo-box
              v-else
              opacity="0"
              pointerEvents="none"
              :_groupHover="{ opacity: 1, pointerEvents: 'auto' }"
            >
              <c-button
                size="sm"
                variant="ghost"
                color="#777"
                @click="$emit('selectField')"
              >
                Edit
              </c-button>

              <c-button
                size="sm"
                variant="ghost"
                color="red.200"
                @click="$emit('removeField')"
              >
                Remove
              </c-button>
            </c-pseudo-box>
          </template>

          <c-button
            v-else-if="field.type && field.type.label"
            size="sm"
            variant="link"
            color="#777"
            @click="field.type = null"
          >
            Change Type
          </c-button>
        </c-box>

        <template v-if="selected">
          <c-box my="3">
            <div style="color: #999">
              <form
                v-if="field.type && field.type.value"
                action="#"
                @submit.prevent="
                  field.label.length ? $emit('cancelAddField') : null
                "
              >
                <c-form-control mb="6">
                  <c-form-label color="#777" fontSize="lg" for="fieldLabel"
                    >Field Label</c-form-label
                  >
                  <c-input
                    id="fieldLabel"
                    type="text"
                    :placeholder="
                      field.type ? field.type.placeholder : 'E.g description'
                    "
                    size="md"
                    v-model="field.label"
                  />
                </c-form-control>

                <PierModelReferenceFieldOption
                  v-if="
                    ['reference', 'multi-reference'].includes(field.type.value)
                  "
                  v-model="field.type.options"
                />

                <PierModelStatusFieldOption
                  v-else-if="field.type.value === 'status'"
                  v-model="field.type.options"
                />

                <template v-else>
                  <PierModelFieldOption
                    :key="key"
                    v-for="(option, key) in field.type.options"
                    v-model="field.type.options[key]"
                  />
                </template>

                <c-form-control>
                  <c-switch
                    id="required"
                    mr="2"
                    color="orange"
                    size="md"
                    v-model="field.required"
                  />

                  <c-form-label html-for="required">
                    This field is required
                  </c-form-label>
                </c-form-control>

                <c-stack isInline spacing="3" v-if="!field.required">
                  <c-form-control flexShrink="0" flex="1" mt="3" mb="3">
                    <c-form-label color="#777" fontSize="lg">
                      Field Default Value
                    </c-form-label>
                    <c-select
                      v-model="field.defaultValue"
                      placeholder="Choose one"
                    >
                      <option value="null">Null</option>
                      <option value="currentTimestamp" v-if="field.type && field.type.value == 'date'">
                        Current Timestamp
                      </option>
                      <option v-else-if="field.type && fieldTypesWithCustomDefaultValue.includes(field.type.value)" value="other">Other</option>
                    </c-select>
                  </c-form-control>

                  <c-box flexShrink="0" flex="1" mt="3" mb="3">
                    <PierModelFieldDefaultValue
                      v-show="field.defaultValue == 'other'"
                      ref="fieldDefaultValue"
                      size="md"
                      :value="field"
                      @input="field.default = $event"
                    />
                  </c-box>
                </c-stack>
              </form>

              <c-form-control v-if="!field.type || !field.type.value">
                <c-box mt="2">
                  <div id="fieldTypeOptions">
                    <template v-for="(type, index) in dbFieldTypes">
                      <label
                        :key="index"
                        :for="`dbFieldtype${type.value}`"
                        class="action-label text-center"
                        :class="{
                          active: field.type && field.type.value === type.value,
                        }"
                        @click="setFieldType(type)"
                      >
                        <MDIcon :icon="type.value" :size="32" />
                        <span>{{ type.label }}</span>
                      </label>
                    </template>

                    <label
                      :for="`dbFieldtypeStatus`"
                      class="action-label text-center"
                      :class="{
                        active: field.type && field.type.value === 'status',
                      }"
                      @click="setFieldType('status')"
                    >
                      <MDIcon icon="status" :size="32" />
                      <span>Status</span>
                    </label>

                    <label
                      :for="`dbFieldtypeReference`"
                      class="action-label text-center"
                      :class="{
                        active: field.type && field.type.value === 'reference',
                        'pointer-events-none opacity-50':
                          !models || !models.length,
                      }"
                      @click="setFieldType('reference')"
                    >
                      <MDIcon icon="reference" :size="32" />
                      <span>Reference</span>
                    </label>

                    <label
                      :for="`dbFieldtypeMultiReference`"
                      class="action-label text-center"
                      :class="{
                        active:
                          field.type && field.type.value === 'multi-reference',
                        'pointer-events-none opacity-50':
                          !models || !models.length,
                      }"
                      @click="setFieldType('multi-reference')"
                    >
                      <MDIcon icon="multi-reference" :size="32" />
                      <span>Multi Reference</span>
                    </label>
                  </div>
                </c-box>
              </c-form-control>
            </div>
          </c-box>
        </template>
      </c-box>
    </div>
  </c-pseudo-box>
</template>

<script>
import {
  CBox,
  CPseudoBox,
  CText,
  CButton,
  CIcon,
  CStack,
  CFormControl,
  CFormLabel,
  CFormHelperText,
  CInput,
  CSelect,
  CCheckbox,
  CSwitch,
} from "@chakra-ui/vue";

import dbFieldTypes from "../DbFieldTypes";
import MDIcon from "./MDIcon";
import PierModelReferenceFieldOption from "./PierModelReferenceFieldOption";
import PierModelStatusFieldOption from "./PierModelStatusFieldOption";
import PierModelFieldOption from "./PierModelFieldOption";
import PierModelFieldDefaultValue from "./PierModelFieldDefaultValue";

import { mapState } from "vuex";

export default {
  name: "PierEditableModelField",
  props: {
    editing: Boolean,
    selectable: Boolean,
    selected: Boolean,
    value: Object,
  },
  data() {
    return {
      dbFieldTypes,
      field: {
        label: "",
        type: {},
      },
      fieldTypesWithCustomDefaultValue: [
        "string",
        "long text",
        "number",
        "name",
        "email",
        "phone",
        'color',
        // 'image',
        // 'file',
        // 'link',
        // 'location',
        // 'rating',
        // 'boolean',
      ],
    };
  },
  computed: {
    ...mapState(["models"]),
    hasCancelListener() {
      return this.$listeners && this.$listeners.cancelAddField;
    },
    fieldIsRequired: function () {
      return this.field.required;
    },
    defaultValue: function () {
      return this.field.defaultValue;
    },
  },
  watch: {
    selected(selectedStatus) {
      if (selectedStatus) this.focusInput("fieldLabel");
    },
    field: function (newValue) {
      this.$emit("input", newValue);
    },
    fieldIsRequired: function (newValue) {
      if (!newValue) this.field.defaultValue = "null";
    },
    defaultValue: function (newValue) {
      if(newValue == "null") 
        this.field.default = undefined;
      else if (newValue == "other") 
        this.focusInput("fieldDefaultValue");
      else
        this.field.default = newValue;
    },
  },
  methods: {
    setFieldType(type) {
      if (type === "reference") {
        type = {
          label: "Reference",
          value: "reference",
          options: {},
        };
      } else if (type === "multi-reference") {
        type = {
          label: "Multi Reference",
          value: "multi-reference",
          options: {},
        };
      } else if (type === "status") {
        type = {
          label: "Status",
          value: "status",
          placeholder: "E.g. status",
          options: {},
        };
      }
      const clonedType = JSON.parse(JSON.stringify(type));
      this.field = {
        label: "",
        type: clonedType,
        required: true,
        defaultValue: "null",
      };
      this.focusInput("fieldLabel");
    },
    focusInput(id) {
      this.$nextTick(() => {
        document.querySelector("#" + id).focus();
      });
    },
  },
  components: {
    CPseudoBox,
    CBox,
    CText,
    CButton,
    CIcon,
    CStack,
    MDIcon,
    CFormControl,
    CFormLabel,
    CFormHelperText,
    CInput,
    CSelect,
    CCheckbox,
    CSwitch,
    PierModelReferenceFieldOption,
    PierModelStatusFieldOption,
    PierModelFieldOption,
    PierModelFieldDefaultValue,
  },
};
</script>