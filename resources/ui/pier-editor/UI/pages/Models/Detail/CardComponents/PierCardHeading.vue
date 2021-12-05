<template>
    <div>
      <h3 class="text-2xl mb-1 font-bold leading-tight">
        {{ heading }}
      </h3>

      <p v-if="subHeading && subHeading.length" class="mb-3 text-lg">
        {{ subHeading }}
      </p>
    </div>
</template>

<script>

export default {
  name: "PierCardHeading",
  props: {
    heading: {
      type: String,
      default: "The trouble with always withering onwards, part II"
    },
    subHeading: {
      type: String,
      // default: "Making sure you don't get caught up in everyday kerfuffles is not the way to..."
    }
  }
};
</script>