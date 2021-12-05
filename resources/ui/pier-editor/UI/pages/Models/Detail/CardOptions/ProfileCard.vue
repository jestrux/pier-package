<style>
  .ProjectCard h3.text-2xl{
    line-height: 1.4;
    font-size: 1.6rem;
  }
</style>
<template>
<div v-if="noCard" class="PierCard ProfileCard rounded-lg overflow-hidden bg-white text-black px-5 pt-5 pb-4"></div>
  <div v-else class="PierCard ProfileCard rounded-lg overflow-hidden bg-white text-black px-5 pt-5 pb-4">
    <div class="flex" v-if="!noImage || !noName || !noBio">
      <div v-if="!noImage" class="relative flex-shrink-0 w-16 h-16 bg-grey-500 rounded-full mr-3 mt-1">
        <img class="absolute pin rounded-full object-cover w-full h-full" 
          :src="image"
          alt="" 
        />
      </div>

      <PierCardHeading v-if="!noName || !noBio"
        :heading="name"
        :sub-heading="bio"
      />
    </div>
    <div v-if="!noSocials || !noStatus" class="flex items-center border-t pt-2 -mx-5 mt-1 -mb-2 px-5">
      <div v-if="!noSocials" class="flex flex-1 items-center">
        <!-- <a title="youtube" target="_blank" href="https://www.youtube.com/"
            class="mr-5"
        >
            <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24">
                <path d="M10,15L15.19,12L10,9V15M21.56,7.17C21.69,7.64 21.78,8.27 21.84,9.07C21.91,9.87 21.94,10.56 21.94,11.16L22,12C22,14.19 21.84,15.8 21.56,16.83C21.31,17.73 20.73,18.31 19.83,18.56C19.36,18.69 18.5,18.78 17.18,18.84C15.88,18.91 14.69,18.94 13.59,18.94L12,19C7.81,19 5.2,18.84 4.17,18.56C3.27,18.31 2.69,17.73 2.44,16.83C2.31,16.36 2.22,15.73 2.16,14.93C2.09,14.13 2.06,13.44 2.06,12.84L2,12C2,9.81 2.16,8.2 2.44,7.17C2.69,6.27 3.27,5.69 4.17,5.44C4.64,5.31 5.5,5.22 6.82,5.16C8.12,5.09 9.31,5.06 10.41,5.06L12,5C16.19,5 18.8,5.16 19.83,5.44C20.73,5.69 21.31,6.27 21.56,7.17Z"></path>
            </svg>
        </a> -->
        
        <a v-if="!noTwitter" title="twitter" target="_blank" :href="twitter"
          class="mr-5">
          <svg width="18" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-twitter"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
        </a>

        <a v-if="!noLinkedin" title="linkedin" target="_blank" :href="linkedin"
          class="mr-5">
          <svg width="18" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-linkedin"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
        </a>

        <a v-if="!noInstagram" title="instagram" target="_blank" :href="instagram">
          <svg width="18" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.5" y2="6.5"></line></svg>
        </a>
      </div>

      <span v-if="!noStatus" class="rounded-full px-3 py-1 bg-blue-200 text-blue-900 font-bold uppercase text-sm tracking-wider">
        {{ status }}
      </span>
    </div>
  </div>
</template>

<script>
import {
    PierCardHeading
} from "../CardComponents";

export default {
  name: "ProfileCard",
  props: {
    values: Object,
  },
  mounted(){
    this.bindValues();
  },
  data() {
    return {
      name: "Dewanda Raisif",
      image: "https://images.unsplash.com/photo-1553804194-fb1475b509b5?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      bio: "Experienced developer working on some cool stuff @Microsoft on Azure.",
      twitter: "https://twitter.com",
      linkedin: "https://linkedin.com",
      instagram: "https://instagram.com",
      status: "verified"
    };
  },
  computed: {
    noImage(){
      return !this.image || !this.image.length;
    },
    noName(){
      return !this.name || !this.name.length;
    },
    noBio(){
      return !this.bio || !this.bio.length;
    },
    noTwitter(){
      return !this.twitter || !this.twitter.length;
    },
    noLinkedin(){
      return !this.linkedin || !this.linkedin.length;
    },
    noInstagram(){
      return !this.instagram || !this.instagram.length;
    },
    noSocials(){
      return this.noTwitter && this.noLinkedin && this.noInstagram;
    },
    noStatus(){
      return !this.status || !this.status.length;
    },
    noCard(){
      return this.noImage && this.noName && this.noBio && this.noSocials && this.noStatus;
    }
  },
  watch:{
    values:{
      deep: true,
      handler: function(){
        this.bindValues();
      }
    }
  },
  methods: {
    bindValues(){
      if(!this.values) return;

      const {
        name,
        image,
        bio,
        twitter,
        linkedin,
        instagram,
        status,
      } = this.values;

      this.image = image;
      this.name = name;
      this.bio = bio;
      this.twitter = twitter;
      this.linkedin = linkedin;
      this.instagram = instagram;
      this.status = status;
    }
  },
  components: {
    PierCardHeading
  },
};
</script>