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
  <div class="ps-radio">
    <input
      type="radio"
      :id="id"
      name="radio-group"
      :checked="checked"
      @change="onChange"
    >
    <label :for="id">{{ label }}</label>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      id: {
        type: String,
        required: true,
      },
      label: {
        type: String,
        required: false,
        default: '',
      },
      checked: {
        type: Boolean,
        required: false,
      },
      value: {
        type: String,
        required: false,
        default: '',
      },
    },
    methods: {
      onChange(): void {
        this.$emit('change', this.value);
      },
    },
  });
</script>
