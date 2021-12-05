<style scoped>
    #bcImageWrapper{
    }

    #bcImageWrapper label{
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.7em;
        text-transform: capitalize;
    }

    #bcImageWrapper label:first-letter{
        text-transform: uppercase;
    }

    #bcImageWrapper input{
        background: transparent;
        -webkit-appearance: none;
        box-sizing: border-box;
        padding: 0.3em 0.5em;
        font-size: 1.25em;
        width: 100%;
        border: none;
        resize: none;
    }

    #bcImageWrapper button{
        display: inline-flex;
        align-items: center;
        border-radius: 30px;
        padding: 0.5em 1em;
        padding-right: 1.3em;
        border: none;
        cursor: pointer;
        text-transform: uppercase;
        margin-right: 0.2em;
        outline: none;
        letter-spacing: 0.15em;
        font-size: 0.65em;
    }

    #bcImageWrapper button:not(.active):hover{
        /* background: #e0e0e0; */
    }
    
    #bcImageWrapper button.active{
        cursor: default;
        background: #eaeaea;
    }

    #bcImageWrapper button svg{
        width: 18px;
        height: 18px;
        margin-right: 0.7em;
        margin-left: 0.2em;
    }

    #imageWrapper{
        position: relative;
        background: #f5f5f5;
        text-align: center;
    }

    #imageWrapper img{
        display: inline-block;
        max-width: 100%;
        max-width: 100%;
        min-height: 180px;
        max-height: 230px;
        object-fit: cover;
    }
</style>

<template>
    <div id="bcImageWrapper">
        <label>
            <span class="capitalize">
                {{ label.replace(/_/g, ' ') }}
            </span>
            &nbsp;

            <button class="text-blue-800 font-semibold" type="button" v-if="src !== null"
                @click="src = null">Change
            </button>
        </label>

        <file-uploader 
            v-if="src === null"
            v-model="src"
            :upload-url="uploadUrl" />

        <FieldPreview v-else
            type="file"
            :is-dp="true" 
            :url="src"
        />
    </div>
</template>

<script>
import FileUploader from "./FileUploader";
import FieldPreview from "./FieldPreview";

export default {
    name: "FileField",
    props: {
        url: String,
        label: {
            type: String,
            default: "File"
        },
        uploadUrl: {
            type: String,
            default: ""
        }
    },
    mounted(){
        this.source = this.uploadUrl && this.uploadUrl.length ? 0 : 1;
        this.src = this.url && this.url.length ? this.url : null;
    },
    data() {
        return {
            src: null
        }
    },
    watch: {
        src: {
            immediate: true, 
            handler (val, oldVal) {
                if(val != null)
                    this.$emit("input", val);
            }
        }
    },
    components: {
      FileUploader,
      FieldPreview
    }
}
</script>