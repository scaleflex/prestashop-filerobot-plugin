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
  <nav aria-label="Breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a :href="catalogLink">{{ trans('link_catalog') }}</a>
      </li>
      <li class="breadcrumb-item">
        <a :href="stockLink">{{ trans('link_stock') }}</a>
      </li>
      <li class="breadcrumb-item active">
        <span v-if="isOverview">{{ trans('link_overview') }}</span>
        <span v-else>{{ trans('link_movements') }}</span>
      </li>
    </ol>
  </nav>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import translate from '@app/pages/stock/mixins/translate';

  export default defineComponent({
    mixins: [translate],
    computed: {
      isOverview(): boolean {
        return this.$route.name === 'overview';
      },
      catalogLink(): string {
        return window.data.catalogUrl;
      },
      stockLink(): string {
        return window.data.stockUrl;
      },
    },
  });
</script>
