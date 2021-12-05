<style>
.Slider {
  position: relative;
}

.Slider .SlideContainer {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.Slider .SlideScroller {
  width: 100%;
  margin: auto;
  display: flex;
  transition: transform 0.3s ease-out 0.06s;
}

.Slider .SlideItem {
  width: 100%;
  min-width: 100%;
  margin: auto;
  transition: opacity 0.5s ease-out 0.15s, transform 0.2s ease-out 0.15s;
}

.ShopItem{
  box-shadow: inset 1px 1px 4px rgba(0, 0, 0, 0.25);
}

.Slider .SlideItem:not(.current) {
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.1s ease-out, transform 0.5s ease-out;
}

.Slider .SlideMovers {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 3;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.Slider.show-actions-on-hover:not(:hover) .SlideMovers {
  opacity: 0;
  pointer-events: none;
}

.Slider .SlideMovers button {
  border-radius: 50%;
  width: 50px;
  height: 50px;
  border: none;
  background: transparent;
  outline: none;
  cursor: pointer;
  will-change: transform;
  transition: transform 0.2s ease-out;
}

.Slider .SlideMovers button:hover {
  transform: scale(1.2);
}

.Slider .SlideMovers button.disabled {
  box-shadow: none;
  pointer-events: none;
  opacity: 0.3;
}

.Slider .SlideMovers button svg {
  width: 40px;
  height: 40px;
  fill: #464647;
}

.Slider .SlideMarkers {
  pointer-events: none;
  display: flex;
  width: 100%;
  z-index: 3;
  position: relative;
  margin-top: 0.6rem;
  margin-bottom: -0.1rem;
  justify-content: center;
}

.Slider .SlideMarkers button {
  pointer-events: auto;
  cursor: pointer;
  /* width: 16px;
  height: 16px; */
  /* width: 13px;
  height: 13px; */
  border-radius: 50%;
  margin: 0 8px;
  padding: 0;
  padding: 2px;
  background-color: transparent;
  color: #464647;
  border: 2px solid transparent;
  transition: opacity 0.35s ease-out;
  outline: none;
  align-items: center;
}

.Slider .SlideMarkers button:before {
  content: "";
  display: block;
  width: 10px;
  height: 10px;
  color: inherit;
  background: currentColor;
  border-radius: 50%;
}

.Slider .SlideMarkers button.active {
  border-color: currentColor;
}
</style>

<template>
  <div class="rounded-lg bg-white text-black p-5">
    <div class="relative flex-shrink-0 -mt-5 -mx-5 mb-4">
      <div ref="imageList">
        <div v-for="(image, index) in images" 
          :key="index"
          class="ShopItem relative overflow-hidden h-64 bg-grey-500"
          :color="colors[index]"
        >
          <img class="absolute pin object-contain w-full h-full"
            :src="image"
            alt
          />
        </div>
      </div>
    </div>

    <PierCardHeading :heading="heading" />

    <div class="mt-3 flex align-items-end justify-between">
      <div class="flex flex-col">
        <span class="tracking-widest text-gray-700">PRICE</span>
        <span class="text-3xl">$1,200</span>
      </div>

      <vue-star-rating
        active-colors="#ffd055"
        rounded-corners
        glow
        read-only
        inactive-color="#e5e5e5"
        :increment="0.5"
        :max-rating="5"
        :star-size="30"
        :show-rating="false"
        v-model="rating"
      />
    </div>
  </div>
</template>

<script>
import Slider from "./Slider";
import VueStarRating from "vue-star-rating";
import { PierCardHeading } from "../../CardComponents";

export default {
  name: "ShopCard",
  props: {
    data: Object,
  },
  mounted(){
    if(this.images.length > 1){
      new Slider(this.$refs.imageList, {
        showMarkers: true
      });
    }
  },
  data() {
    return {
      heading: "Google Pixel 4 Case",
      rating: 4.5,
      images: [
        "https://lh3.googleusercontent.com/ldO0h4FPYbLLnXLgAxrbphjg-a5ninv5CK0ZR7ZfsvTc7_mQYbxFCM9ek72HKeXSlUc5SGExV2IVyOVfAgfZUA=rw-w1180",
        "https://lh3.googleusercontent.com/JMnaITMmUF2fgC-XDD4U_Blh4n4f3aPPV-TuAxd7hYCG-EhmVF1WV6CqyejB3D4sau8-0ME2jZBmjogewr0m=rw-w1180",
        "https://lh3.googleusercontent.com/TBXrkii6JoXPFuhgfGaqIM8DbNFxnbC1MgO5wt6G1rE5cF8wdgDACXU2QgK5PZbcwDO_ORDEBbtQJoysrABJjw=rw-w1180"
      ],
      colors: ["#83818e", "#718497", "#f17c5c"]
    };
  },
  components: {
    VueStarRating,
    PierCardHeading,
  },
};
</script>