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
  <button
    type="button"
    class="btn"
    :class="classObject"
  >
    <slot />
  </button>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      primary: {type: Boolean},
      ghost: {type: Boolean},
    },
    computed: {
      classObject(): {
        'btn-outline-primary'?: boolean,
        'btn-outline-secondary'?: boolean,
        'btn-primary'?: boolean,
        'btn-secondary'?: boolean
      } {
        if (this.ghost) {
          return {
            'btn-outline-primary': this.primary,
            'btn-outline-secondary': !this.primary,
          };
        }

        return {
          'btn-primary': this.primary,
          'btn-secondary': !this.primary,
        };
      },
    },
  });
</script>
