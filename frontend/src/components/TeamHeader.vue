<template>
  <div class="p-4 space-y-3">
    <!-- Logo e Team Plan -->
    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <h1 class="text-xl font-bold text-indigo-600">MC</h1>
        <span class="ml-2 text-sm text-gray-600">Team Plan</span>
      </div>
    </div>
    
    <!-- Store Selector -->
    <StoreSelector />
  </div>
</template>

<script setup>
import StoreSelector from './StoreSelector.vue'
</script>
