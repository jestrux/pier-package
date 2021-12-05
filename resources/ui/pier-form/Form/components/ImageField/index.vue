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

            <div v-if="src === null && (imageUploadUrl || unsplashClientId)">
                <button type="button" v-if="imageUploadUrl && imageUploadUrl.length" :class="{'active' : source === 0}"
                    @click="source = 0">
                    <svg viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/></svg>
                    Upload
                </button>

                <button type="button" :class="{'active' : source === 1}"
                    @click="source = 1">
                    <svg viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                    Link
                </button>

                <button type="button" v-if="unsplashClientId && unsplashClientId.length" :class="{'active' : source === 2}"
                    @click="source = 2">
                    <svg viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M18 13v7H4V6h5.02c.05-.71.22-1.38.48-2H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-5l-2-2zm-1.5 5h-11l2.75-3.53 1.96 2.36 2.75-3.54zm2.8-9.11c.44-.7.7-1.51.7-2.39C20 4.01 17.99 2 15.5 2S11 4.01 11 6.5s2.01 4.5 4.49 4.5c.88 0 1.7-.26 2.39-.7L21 13.42 22.42 12 19.3 8.89zM15.5 9C14.12 9 13 7.88 13 6.5S14.12 4 15.5 4 18 5.12 18 6.5 16.88 9 15.5 9z"/></svg>
                    Search
                </button>
            </div>

            <button class="text-blue-800 font-semibold" type="button" v-if="src !== null"
                @click="src = null">Change
            </button>
        </label>

        <div v-if="src === null">
            <LinkField 
                v-if="source === 1" 
                v-model="src"
                placeholder="Click paste to paste image link"
                match-error="Invalid image link"
            />
        
            <unsplash-search
                :client-id="unsplashClientId"
                per-page="6"
                v-else-if="unsplashClientId && unsplashClientId.length && source === 2" v-model="src"/>

            <file-uploader 
                v-else-if="source === 0 && imageUploadUrl && imageUploadUrl.length" v-model="src"
                :upload-url="imageUploadUrl"/>
        </div>

        <!-- <div id="imageWrapper">
            <img v-if="src !== null" :src="src" alt="">
        </div> -->

        <FieldPreview v-if="src !== null" 
            type="image" 
            :url="src"
            :isDp="isDp"
        />
    </div>
</template>

<script>
import FileUploader from "../FileUploader";
import UnsplashSearch from "./UnsplashSearch";
import LinkField from "../LinkField";
import FieldPreview from "../FieldPreview";

export default {
    props: {
        url: String,
        label: {
            type: String,
            default: "Image"
        },
        isDp: Boolean,
        imageUploadUrl: {
            type: String,
            default: ""
        },
        unsplashClientId: {
            type: String,
            default: ""
        },
    },
    mounted(){
        this.source = this.imageUploadUrl && this.imageUploadUrl.length ? 0 : 1;

        this.$nextTick(() => {
            this.src = this.url && this.url.length ? this.url : null;
        });
    },
    data() {
        return {
            enteringLink: false,
            src: null,
            source: 2
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
      UnsplashSearch,
      LinkField,
      FieldPreview
    }
}
</script>