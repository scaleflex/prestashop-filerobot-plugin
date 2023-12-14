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
    class="page-head-tabs"
    id="head_tabs"
  >
    <ul class="nav nav-pills">
      <li class="nav-item">
        <router-link
          data-toggle="tab"
          class="nav-link"
          :class="{active : isOverview}"
          to="/"
          role="tab"
        >
          {{ trans('menu_stock') }}
        </router-link>
      </li>
      <li class="nav-item">
        <router-link
          data-toggle="tab"
          class="nav-link"
          :class="{active : isMovements}"
          to="/movements"
          role="tab"
        >
          {{ trans('menu_movements') }}
        </router-link>
      </li>
    </ul>
  </div>
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
      isMovements(): boolean {
        return this.$route.name === 'movements';
      },
    },
  });
</script>
