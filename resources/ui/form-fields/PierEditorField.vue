<style scoped>
/* label{
        display: block;
        margin-bottom: 0.1em;
    }

    label:first-letter{
        text-transform: uppercase;
    }

    input, select, textarea{
        background: transparent;
        -webkit-appearance: none;
        box-sizing: border-box;
        padding: 0.3em 0.5em;
        font-size: 1.25em;
        width: 100%;
        border: 1px solid #ddd !important;
        resize: none;
    }

    select{
        background: #fff url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100' fill='%23999'><polygon points='0,0 100,0 50,50'/></svg>") no-repeat scroll 96.5% 60%;
        background-size: 12px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    } */

    #qlEditorWrapper .quill-container,
    #qlEditorWrapper .ql-editor{
      height: auto !important;
    }
</style>

<template>
  <div class="input-group" style="margin: 0;">
    <template v-if="field">
      <input type="hidden" :name="field.label" :value="val" />
      
      <label
        v-if="!hideLabel"
        class="inline-block first-letter:uppercase"
        :for="field.label"
      >{{ (field.cleanLabel ? field.cleanLabel : field.label).replace(/_/g, ' ') }}</label>


      <bc-image-field
        v-if="field.type == 'image'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :url="val"
        :is-dp="field.meta.face"
        :required="field.required"
        :meta="field.meta"
      />
      
      <FileField
        v-else-if="field.type == 'file'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :url="val"
        :type="field.meta.type"
        :required="field.required"
      />

      <SwitchField
        v-else-if="field.type == 'boolean'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :required="field.required"
      />
      
      <StatusField v-else-if="field.type == 'status'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        :meta="field.meta"
        v-model="val"
        :required="field.required"
      />
      
      <DateField
        v-else-if="field.type == 'date'"
        :field="field"
        v-model="val"
        :required="field.required"
      />
      
      <ReferenceField
        v-else-if="field.type == 'reference'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        :reference-model="field.meta.model"
        :reference-model-main-field="field.meta.mainField"
        :add-reference-inline="field.meta.addInline"
        v-model="val"
        :required="field.required"
      />
      
      <MultiReferenceField
        v-else-if="field.type == 'multi-reference'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        :field="field"
        v-model="val"
        :required="field.required"
      />
      
      <LinkField
        v-else-if="field.type == 'link'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        show-preview
        v-model="val"
        :required="field.required"
      />
      
      <bc-youtube-field
        v-else-if="field.type == 'video'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :url="val"
        :required="field.required"
      />

      <vue-star-rating
        v-else-if="field.type === 'rating'"
        active-color="#e9b531"
        :increment="0.5"
        :max-rating="parseInt(field.meta.outOf)"
        :star-size="28"
        v-model="val"
        :required="field.required"
      />

      <PasswordField
        v-else-if="field.type == 'password'"
        :field="field"
        v-model="val"
        :required="field.required"
      />

      <PhoneField
        v-else-if="field.type == 'phone'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :required="field.required"
      />

      <LocationField
        v-else-if="field.type == 'location'"
        :label="field.cleanLabel ? field.cleanLabel : field.label"
        v-model="val"
        :meta="field.meta"
        :required="field.required"
      />

      <textarea 
        v-else-if="field.type === 'long text' && !field.meta.wysiwyg" 
        ref="longTextInput" 
        rows="1" 
        v-model="val" 
        :required="field.required"
      />

      <div id="qlEditorWrapper" v-else-if="field.type === 'long text' && field.meta.wysiwyg">
        <vue-editor
          :editorToolbar="wysiwygToolbar"
          v-model="val"
        />

        <placeholder-input :required="field.required" :value="val" />
      </div>

      <input
        v-else-if="field.type === 'number'"
        :id="field.label"
        :name="field.label"
        :required="field.required"
        type="number"
        v-model="val"
      />
      
      <input
        v-else-if="field.type != 'auth'"
        :id="field.label"
        :name="field.label"
        :required="field.required"
        :type="field.type"
        v-model="val"
      />
    </template>

    <!-- <select v-else-if="field.type == 'choice'" v-model="field.value">
            <option disabled value="">Choose {{field.name}}</option>

            <option v-for="(choice, index) in field.choices" :key="index" :value="choice">
                {{ choice }}
            </option>
        </select>

        <vue-editor v-else-if="field.type == 'ws-text'" :editorToolbar="customToolbar" v-model="field.value"></vue-editor>
        
    -->
  </div>
</template>

<script>
import { mapState } from "vuex";
import { VueEditor } from "vue2-editor";
import VueStarRating from "vue-star-rating";
import autosize from "autosize";
import BcImageField from "./components/ImageField";
import FileField from "./components/FileField";
import BcYoutubeField from "./components/BCYoutubeField";
import SwitchField from "./components/SwitchField";
import StatusField from "./components/StatusField";
import DateField from "./components/DateField";
import ReferenceField from "./components/ReferenceField";
import MultiReferenceField from "./components/MultiReferenceField";
import PasswordField from "./components/PasswordField";
import PhoneField from "./components/PhoneField";
import LinkField from "./components/LinkField";
import LocationField from "./components/LocationField";
import PlaceholderInput from "../components/PlaceholderInput";

const UNSPLASH_CLIENT_ID = process.env.MIX_UNSPLASH_CLIENT_ID;

export default {
  name: "PierEditorField",
  props: {
    field: {
      type: Object,
      default: function() {
        return {
          label: "",
          type: "",
          meta: {}
        };
      }
    },
    nolabel: Boolean,
    value: String | Number
  },
  inject: ["PierCMSConfig"],
  mounted() {
    const longTextInput = this.$refs.longTextInput;
    if(this.field.type == "auth" && this.PierCMSConfig.authUser)
      this.val = this.PierCMSConfig.authUser;
    else if(this.value){
      this.val = this.value;

      this.$nextTick(() => {
        if (longTextInput) autosize(longTextInput);
      });
    }
    else if (longTextInput) autosize(longTextInput);
  },
  data() {
    return {
      UNSPLASH_CLIENT_ID,
      val: "",
      wysiwygToolbar: [
        ["bold", "italic", "underline"],
        [{ list: "ordered" }, { list: "bullet" }],
      ]
    };
  },
  computed: {
    ...mapState(["selectedModelName"]),
    hideLabel: function() {
      if (!this.field || !this.field.type) return false;

      const fieldTypesWithoutLabel = ["hidden", "auth", "image", "video", "boolean", "file"];
      return this.nolabel || fieldTypesWithoutLabel.includes(this.field.type.toLowerCase());
    }
  },
  watch: {
    val: function(newValue) {
      this.$emit("input", newValue);
    }
  },
  components: {
    VueEditor,
    VueStarRating,
    BcImageField,
    FileField,
    BcYoutubeField,
    StatusField,
    SwitchField,
    DateField,
    ReferenceField,
    MultiReferenceField,
    LinkField,
    PasswordField,
    PhoneField,
    LocationField,
    PlaceholderInput
  }
};
</script>