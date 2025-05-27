<script setup>
  defineProps({
    visible: Boolean
  })
</script>

<template>
  <div v-if="visible" id="content" class="bg-white dark:bg-gray-900">
    <span class="text-lg font-semibold text-gray-800 dark:text-white/90">agik Harmony</span>
    <div class="loading-bar">
       <div class="blue-bar"></div>
    </div>
  </div>
</template>

<style scoped>
  #content{
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    z-index: 99999;
    justify-content: center;
    align-items: center;
    flex-direction: column;
  }
  .loading-bar {
    width: 130px;
    height: 5px;
    margin: 0 auto;
    border-radius: 2px;
    background-color: #e8ebef;
    position: relative;
    overflow: hidden;
    z-index: 1;
    transform: rotateY(0);
    transition: transform .3s ease-in
  }
  .blue-bar {
    height: 100%;
    width: 68px;
    position: absolute;
    transform: translate(-34px);
    background-color: #4861fb;
    border-radius: 2px;
    animation: initial-loading 1.5s ease infinite
  }
  @keyframes initial-loading {
    0% {
      transform: translate(-34px)
    }

    50% {
      transform: translate(96px)
    }

    to {
      transform: translate(-34px)
    }
  }
</style>