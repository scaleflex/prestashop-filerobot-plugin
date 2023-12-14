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
  <div class="input-group date">
    <input
      ref="datepicker"
      type="text"
      :class="['form-control', `datepicker-${type}`]"
    >
    <div class="input-group-append">
      <span class="input-group-text">
        <i class="material-icons">event</i>
      </span>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      locale: {
        type: String,
        required: true,
        default: 'en',
      },
      type: {
        type: String,
        required: true,
      },
    },
    mounted() {
      $(<HTMLInputElement> this.$refs.datepicker).datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
        useCurrent: false,
      }).on('dp.change', (infos: Record<string, any>) => {
        infos.dateType = this.type;
        this.$emit(
          infos.date ? 'dpChange' : 'reset',
          infos,
        );
      });
    },
  });
</script>

<style lang="scss">
  @import '~@scss/config/_settings.scss';

  .date {
    a[data-action='clear']::before {
      font-family: 'Material Icons';
      content: "\E14C";
      font-size: 20px;
      position: absolute;
      bottom: 15px;
      left: 50%;
      margin-left: -10px;
      color: $gray-dark;
      cursor:pointer;
    }
    .bootstrap-datetimepicker-widget tr td span:hover {
      background-color: white;
    }
  }

</style>
