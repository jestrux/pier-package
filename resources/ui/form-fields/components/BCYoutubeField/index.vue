<style scoped>
    button{
        padding: 0.5em 1em;
        padding-right: 1.3em;
        border: none;
        cursor: pointer;
        margin-right: 0.2em;
        outline: none;
        letter-spacing: 0.15em;
        font-size: 0.65em;
    }
</style>
<template>
    <div>
        <label style="display:flex" class="justify-between">
            <span class="capitalize">
                {{ label.replace(/_/g, ' ') }}
            </span>
            &nbsp;

            <button class="text-blue-800 font-semibold inline-flex items-center uppercase" 
                type="button" v-if="src !== null"
                @click="src = null">Change
            </button>
        </label>

        <LinkField v-if="src === null" v-model="src"
            placeholder="Click paste to paste youtube link"
            match-pattern="youtube"
            match-error="Invalid youtube link"
        />
        
        <FieldPreview v-else
            type="youtube-video" 
            :url="src"
        />
    </div>
</template>

<script>

import LinkField from "../LinkField";
import FieldPreview from "../FieldPreview";

export default {
    props: {
        url: String,
        label: {
            type: String,
            default: "Image"
        },
    },
    mounted(){
        this.src = this.url && this.url.length ? this.url : null;
    },
    data() {
        return {
            src: null,
        }
    },
    watch: {
        src: {
            immediate: true,
            handler (val, oldVal) {
                if(val != null){
                    this.$emit("input", val);
                    const match = this._parseYoutubeUrl(val);
                    if(match)
                        this.embed = `https://www.youtube.com/embed/${match[2]}?rel=0&amp;showinfo=0`;
                }
            }
        }
    },
    methods: {
        _parseYoutubeUrl(url){
            var reg = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/
            return url.match(reg);
        }
    },
    components: {
      LinkField,
      FieldPreview
    }
}
</script>