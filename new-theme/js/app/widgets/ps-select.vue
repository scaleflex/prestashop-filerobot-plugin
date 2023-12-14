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
  <div
    class="ps-select"
    :id="itemId"
  >
    <select
      class="form-control"
      v-model="selected"
      @change="onChange"
    >
      <option
        value="default"
        selected
      >
        <slot />
      </option>
      <option
        v-for="(item, index) in items"
        :key="index"
        :value="item[itemId]"
      >
        {{ item[itemName] }}
      </option>
    </select>
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';

  export default defineComponent({
    props: {
      items: {
        type: Array as PropType<Array<Record<string, any>>>,
        required: true,
      },
      itemId: {
        type: String,
        required: false,
        default: '',
      },
      itemName: {
        type: String,
        required: false,
        default: '',
      },
    },
    methods: {
      onChange(): void {
        this.$emit('change', {
          value: this.selected,
          itemId: this.itemId,
        });
      },
    },
    data() {
      return {
        selected: 'default',
      };
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .ps-select {
    position: relative;
    select {
      appearance: none;
      border-radius: 0;
    }
    &::after {
      content: "\E313";
      font-family: 'Material Icons';
      color: $gray-medium;
      font-size: 20px;
      position: absolute;
      right: 5px;
      top: 5px;
    }
  }
</style>
