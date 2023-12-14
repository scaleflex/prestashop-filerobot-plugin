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
  <div class="md-checkbox md-checkbox-inline">
    <label>
      <input
        v-if="Array.isArray(checked)"
        type="checkbox"
        :checked="checked.includes(value)"
        :class="classes"
        :disabled="disabled"
        @change="change"
      >
      <input
        v-else
        type="checkbox"
        :checked="checked"
        :class="classes"
        :disabled="disabled"
        @change="$emit('input', ($event?.target as HTMLInputElement).checked)"
      >

      <slot>
        <!-- - Fallback content -->
        <i class="md-checkbox-control" />
      </slot>
    </label>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    model: {
      prop: 'checked',
      event: 'input',
    },
    props: {
      classes: {
        type: Array,
        default: () => ([
          'js-tab-checkbox',
        ]),
      },
      checked: {
        required: false,
        type: [Array, Boolean],
        default: false,
      },
      disabled: {
        type: Boolean,
        required: false,
        default: false,
      },
      value: {
        required: true,
        type: String,
      },
    },
    methods: {
      change(): void {
        if ((<Array<string>> this.checked).includes(this.value)) {
          (<Array<string>> this.checked).splice((<Array<string>> this.checked).indexOf(this.value), 1);
        } else {
          (<Array<string>> this.checked).push(this.value);
        }

        this.$emit('change');
      },
    },
  });
</script>
