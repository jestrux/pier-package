<template>
    <div class="PierCard rounded-lg overflow-hidden bg-white text-black p-5">
        <PierCardImage :src="image" />
        <PierCardHeading :heading="heading" :sub-heading="subHeading" />
        <PierCardMiniProfile
          :image="authorImage"
          :name="authorName"
          position="guest author"
        />
    </div>
</template>

<script>

import {
    PierCardImage,
    PierCardHeading,
    PierCardMiniProfile
} from "../CardComponents";

export default {
  name: "ArticleCard",
  props: {
    data: Object
  },
  data() {
    return {
      image: "https://images.unsplash.com/photo-1590826914838-4eaac1fee9ad?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      heading: "The trouble with always withering onwards blindly, part I",
      subHeading: "Think quick, when was the first time you noticed withering and automatically felt a...",
      authorImage: "https://images.unsplash.com/photo-1564923630403-2284b87c0041?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      authorName: "Rashida Harris",
    };
  },
  components: {
    PierCardImage,
    PierCardHeading,
    PierCardMiniProfile
  }
};
</script>