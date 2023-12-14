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
  <section class="stock-overview">
    <ProductsActions />
    <ProductsTable
      :is-loading="isLoading"
      @sort="sort"
    />
  </section>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import ProductsActions from './products-actions.vue';
  import ProductsTable from './products-table.vue';

  const DEFAULT_SORT = 'desc';

  export default defineComponent({
    computed: {
      isLoading(): boolean {
        return this.$store.state.isLoading;
      },
    },
    methods: {
      sort(sortDirection: string): void {
        this.$emit('fetch', sortDirection);
      },
    },
    mounted() {
      this.$store.dispatch('updatePageIndex', 1);
      this.$store.dispatch('updateKeywords', []);
      this.$store.dispatch('updateOrder', 'product_id');
      this.$store.dispatch('isLoading');
      this.$emit('resetFilters');
      this.$emit('fetch', DEFAULT_SORT);
    },
    components: {
      ProductsActions,
      ProductsTable,
    },
  });
</script>
