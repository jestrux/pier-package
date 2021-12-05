<style scoped>
    #FieldPreview:hover{
        background: #f8f8f8;
    }

    #FieldPreviewImage{
        background: #eee;
    }
    
    #FieldPreview.youtube-video #FieldPreviewImage:before{
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 1;
    }
    
    #FieldPreviewImage svg{
        width: 32px;
        height: 32px;
        z-index: 1;
    }
    
    #FieldPreview h3{
        max-width: 80%;
        text-overflow: ellipsis;
    }
    
    #FieldPreview p{
        max-width: 80%;
        text-overflow: ellipsis;
    }
</style>
<template>
    <div v-if="type === 'link' && !linkDetails" />

    <a v-else :href="link" target="_blank" id="FieldPreview" :title="title"
        class="relative overflow-hidden flex items-center rounded-lg p-3 border"
        :class="[type]">
        <div id="FieldPreviewImage" class="flex-shrink-0 relative overflow-hidden mr-3 flex items-center justify-center"
            :class="{'rounded': !isDp, 'rounded-full': isDp}"
            :style="imageDimensions"
        >
            <img v-if="type !== 'file'"
                class="absolute inset-0 w-full h-full object-cover" 
                :src="image" alt="" />

            <svg v-if="type === 'youtube-video'" class="text-red-500" fill="currentColor" width="20px" viewBox="0 0 24 24">
                <rect fill="#fff" x="5" y="5" width="12" height="12" />
                <path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>
            </svg>
            
            <svg v-else-if="type === 'file'" fill="#656d77" viewBox="0 0 24 24"><path d="M6 2c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6H6zm7 7V3.5L18.5 9H13z"/></svg>
        </div>
        <div style="min-width: 0;">
            <h3 class="text-lg whitespace-no-wrap overflow-hidden">
                {{ title }}
            </h3>
            <p class="text-sm whitespace-no-wrap overflow-hidden text-gray-600">
                {{ subtitle }}
            </p>
        </div>
    </a>
</template>

<script>

function getYoutubeVideoIdFromurl(url){
    const reg = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(reg);
    return match[2];
}

import { getLinkPreview } from "../../../API";

export default {

    name: "FieldPreview",
    props: {
        type: String,
        url: String,
        isDp: Boolean
    },
    data(){
        return {
            youtubeTitle: "Youtube video",
            linkDetails: null
        }
    },
    computed:{
        title(){
            if((this.type === "youtube-video" || this.type === "link") && this.linkDetails)
                return this.linkDetails.title;

            return this.url;
        },
        subtitle(){
            if((this.type === "youtube-video" || this.type === "link") && this.linkDetails)
                return this.linkDetails.description;

            let subtitle = "( Click to see preview ";
            return subtitle + this.type.replace(/-/g, ' ') + " )";
        },
        link(){
            return this.url;
        },
        image(){
            if(this.type === "youtube-video")
                return `https://i.ytimg.com/vi/${getYoutubeVideoIdFromurl(this.url)}/maxresdefault.jpg`;
            else if(this.type === "link" && this.linkDetails)
                return this.linkDetails.image;

            return this.url;
        },
        imageDimensions(){
            let width = 70, height = 60;

            if(this.isDp)
                width = 60;
            else if(this.type === "youtube-video"){
                width = 110;
                height = 70;
            }

            return {
                width: `${width}px`, height: `${height}px`
            }
        }
    },
    watch:{
        url:{
            immediate: true,
            handler: function(){
                if(this.type === "youtube-video" || this.type === "link")
                    this.fetchLinkDetails();
            }
        }
    },
    methods: {
        async fetchLinkDetails(){
            try {
                this.linkDetails = null;

                const { cover, images, title, description } = await getLinkPreview(this.url);
                if((!title || !title.length) && (!description && !description.length))
                    return;

                let image = cover;
                if((!cover || !cover.length) && images && images.length)
                    image = images[0];

                this.linkDetails = {
                    image, title, description
                };

                this.$emit('preview-ready');
            } catch (error) {
                console.log("failed to fetch youtube video title:", error);
            }
        }
    }
}
</script>