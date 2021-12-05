<template>
    <div class="PierCard rounded-lg overflow-hidden bg-white text-black px-5 pt-5 pb-3">
        <PierCardImage :src="image" />
        <PierCardHeading :heading="heading" :sub-heading="subHeading" />
        <PierCardDate />
        <PierCardLocation />
    </div>
</template>

<script>

import {
    PierCardImage,
    PierCardHeading,
    PierCardLocation,
    PierCardDate
} from "../CardComponents";

export default {
  name: "EventCard",
  props: {
    data: Object
  },
  data() {
    return {
      image: "https://images.unsplash.com/photo-1484712401471-05c7215830eb?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
      heading: "Happy young folk summit",
      subHeading: "Join us as we remind each other to be how we once were, happy to be alive group and..."
    };
  },
  components: {
    PierCardImage,
    PierCardHeading,
    PierCardLocation,
    PierCardDate
  }
};
</script>