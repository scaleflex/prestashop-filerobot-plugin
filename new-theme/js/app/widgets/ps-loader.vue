/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Scaleflex
 *  @copyright Since 2022 Scaleflex
 *  @license   LICENSE.txt
 */ 
<template>
  <div class="ps-loader">
    <div class="timeline-item">
      <div class="animated-background">
        <slot />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .ps-loader {
    width: 100%;
    .animated-background {
      animation-duration: 1s;
      animation-iteration-count: infinite;
      animation-name: loading;
      animation-timing-function: linear;
      background: $gray-soft;
      background: linear-gradient(to right, $gray-soft 8%, #ccc 18%, $gray-soft 33%);
      background-size: 100%;
      height: 40px;
      position: relative;
    }

    .background-masker {
      background: white;
      position: absolute;
    }
  }

  @keyframes loading{
    0%{
      background-position: -500px 0
    }
    100%{
      background-position: 500px 0
    }
  }
</style>
