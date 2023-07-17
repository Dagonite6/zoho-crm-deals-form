<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    showing: {
        type: Boolean,
        required: true,
    },
});

defineEmits(['update:showing']);


</script>

<template>
    <div v-if="showing">
      The modal will go here.
    </div>
  </template>