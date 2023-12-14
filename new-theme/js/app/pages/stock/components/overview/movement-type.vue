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
  <div class="col-md-4">
    <div class="movements">
      <PSButton
        type="button"
        class="update-qty float-sm-right"
        :class="classObject"
        :disabled="disabled"
        :primary="true"
        @click="sendQty"
      >
        <i class="material-icons">edit</i>
        {{ trans('button_movement_type') }}
      </PSButton>
    </div>
  </div>
</template>

<script lang="ts">
  import PSButton from '@app/widgets/ps-button.vue';
  import {defineComponent} from 'vue';
  import TranslationMixin from '@app/pages/stock/mixins/translate';

  export default defineComponent({
    computed: {
      disabled(): boolean {
        return !this.$store.state.hasQty;
      },
      classObject(): {'btn-primary': boolean} {
        return {
          'btn-primary': !this.disabled,
        };
      },
    },
    mixins: [TranslationMixin],
    methods: {
      sendQty(): void {
        this.$store.state.hasQty = false;
        this.$store.dispatch('updateQtyByProductsId');
      },
    },
    components: {
      PSButton,
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .update-qty {
    color: white;
    transition: background-color 0.2s ease;
  }
</style>
