<template>
  <span>
    {{ text }}
    <v-icon
      v-if="required"
      icon="mdi-asterisk"
      size="x-small"
      color="red-darken-4"
    />
  </span>
</template>

<script setup>
defineProps({
  text: {
    type: String,
    required: true
  },
  required: {
    type: Boolean,
    default: false
  }
});
</script>
