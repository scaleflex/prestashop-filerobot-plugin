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
  <div class="md-checkbox">
    <label>
      <input
        type="checkbox"
        :id="id"
        v-model="checked"
        :class="{'indeterminate' : isIndeterminate }"
      >
      <i class="md-checkbox-control" />
      <slot name="label" />
    </label>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  /**
   * @deprecated since 8.0, use app/components/checkbox.vue instead
   */
  export default defineComponent({
    props: {
      id: {
        type: String,
        required: false,
        default: '',
      },
      model: {
        type: Object,
        required: false,
        default: () => ({}),
      },
      isIndeterminate: {
        type: Boolean,
        required: false,
        default: false,
      },
    },
    watch: {
      checked(val: boolean): void {
        this.$emit('checked', {
          checked: val,
          item: this.model,
        });
      },
    },
    data() {
      return {
        checked: false,
      };
    },
  });
</script>
