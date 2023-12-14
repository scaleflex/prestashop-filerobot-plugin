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
    class="ps-sortable-column"
    :data-sort-col-name="this.order"
    :data-sort-is-current="isCurrent"
    :data-sort-direction="sortDirection"
    @click="sortToggle"
  >
    <span role="columnheader"><slot /></span>
    <span
      role="button"
      class="ps-sort"
      aria-label="Tri"
    />
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      // column name
      order: {
        type: String,
        required: true,
      },
      // indicates the currently sorted column in the table
      currentSort: {
        type: String,
        required: true,
      },
    },
    methods: {
      sortToggle(): void {
        // toggle direction
        this.sortDirection = (this.sortDirection === 'asc') ? 'desc' : 'asc';
        this.$emit('sort', this.order, this.sortDirection);
      },
    },
    data() {
      return {
        sortDirection: 'desc',
      };
    },
    computed: {
      isCurrent(): boolean {
        return this.currentSort === this.order;
      },
    },
  });
</script>
