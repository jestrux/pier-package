<template>
    <div class="rounded-lg overflow-hidden bg-white text-black p-5">
        <div class="hidde relative overflow-hidden flex-shrink-0 h-56 bg-grey-500 rounded-md -mt-1 -mx-1 mb-4">
            <img class="absolute pin object-cover w-full h-full" 
                src="https://images.unsplash.com/photo-1551731409-43eb3e517a1a?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ"
                alt="" 
            />

            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10">
                <svg class="hidde w-20 h-20" fill="#eee" viewBox="0 0 24 24"><path d="M10 16.5l6-4.5-6-4.5v9zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
            </div>
        </div>

        <div class="flex items-center justify-betwee mb-4 -ml-1 -mt-1 text-red-500">
            <vue-star-rating
                active-color="currentColor"
                read-only
                inactive-color="#ddd"
                :increment="0.5"
                :max-rating="5"
                :star-size="30"
                :show-rating="false"
                v-model="rating"
            />

            <span class="w-2 h-2 rounded-full mt-2 mx-4" style="background: #aaa" />

            <span class="tracking-widest uppercase text-md text-gray-700 inline-block mt-2 ml-1">
                2.5 Hrs long
            </span>
        </div>

        <PierCardHeading :heading="heading" :sub-heading="subHeading" />

        <span class="tracking-widest uppercase text-sm text-gray-700 inline-block mt-1 mb-2">
            Hosted By
        </span>

        <PierCardMiniProfile 
            :name="instrucorName"
            :image="instrucorImage"
            :position="instrucorPosition"
        />
    </div>
</template>

<script>

import VueStarRating from "vue-star-rating";
import { 
    PierCardHeading,
    PierCardMiniProfile,
} from "../CardComponents";

export default {
  name: "CourseCard",
  props: {
    data: Object
  },
  data() {
    return {
      image: "https://images.unsplash.com/photo-1484712401471-05c7215830eb?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      heading: "Women in tech Manilla: navigating the UI design space",
      subHeading: "Join Reina as she takes you through what it means to create relevant designs for the mode...",
      instrucorName: "Reina St. Patrick",
      instrucorPosition: "Lead designer, Apple Podcast",
      instrucorImage: "https://images.unsplash.com/photo-1590133853249-4166757d26ec?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      rating: 4.5
    };
  },
  components: {
    PierCardHeading,
    PierCardMiniProfile,
    VueStarRating,
  }
};
</script>