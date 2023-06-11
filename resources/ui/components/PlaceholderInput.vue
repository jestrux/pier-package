<template>
  <div style="position: relative; pointer-events: none">
    <input
      style="position: absolute; top: -3rem; opacity: 0"
      type="text"
      :value="value"
      :required="required"
    />
  </div>
</template>

<script>
export default {
  name: "PlaceholderInput",
  props: ["value", "required"],
};
</script>