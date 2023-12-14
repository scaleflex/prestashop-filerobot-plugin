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
  <div class="media">
    <img
      v-if="displayThumb"
      :src="thumbnail"
      class="thumbnail d-flex"
    >
    <div
      v-else
      class="no-img"
    />
    <div class="ml-2 desc media-body">
      <slot />
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      thumbnail: {
        type: String,
        required: false,
        default: '',
      },
    },
    computed: {
      displayThumb(): boolean {
        return this.thumbnail === '' ? false : !!this.thumbnail;
      },
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .product-title {
    .has-combination & {
      font-weight: 600;
    }
  }
  .thumbnail, .no-img {
    border: $gray-light 1px solid;
    max-width: 47px;
  }
  .no-img {
    background: white;
    width: 47px;
    height: 47px;
    display: inline-block;
    vertical-align: middle;
  }
  .desc {
    white-space: normal;
  }
  small {
    color: $gray-medium;
  }
</style>
