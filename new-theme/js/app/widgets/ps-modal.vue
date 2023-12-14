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
    class="modal fade"
    id="ps-modal"
    tabindex="-1"
    role="dialog"
  >
    <div
      class="modal-dialog"
      role="document"
    >
      <div class="modal-content">
        <div class="modal-header">
          <button
            type="button"
            class="close"
            data-dismiss="modal"
          >
            <i class="material-icons">close</i>
          </button>
          <h4 class="modal-title">
            {{ translations.modal_title }}
          </h4>
        </div>
        <div class="modal-body">
          {{ translations.modal_content }}
        </div>
        <div class="modal-footer">
          <PSButton
            @click="onSave"
            class="btn-lg"
            primary
            data-dismiss="modal"
          >
            {{ translations.button_save }}
          </PSButton>
          <PSButton
            @click="onLeave"
            class="btn-lg"
            ghost
            data-dismiss="modal"
          >
            {{ translations.button_leave }}
          </PSButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import PSButton from '@app/widgets/ps-button.vue';
  import {EventEmitter} from '@components/event-emitter';
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      translations: {
        type: Object,
        required: false,
        default: () => ({}),
      },
    },
    mounted() {
      EventEmitter.on('showModal', () => {
        this.showModal();
      });
      EventEmitter.on('hideModal', () => {
        this.hideModal();
      });
    },
    methods: {
      showModal(): void {
        $(this.$el).modal('show');
      },
      hideModal(): void {
        $(this.$el).modal('hide');
      },
      onSave(): void {
        this.$emit('save');
      },
      onLeave(): void {
        this.$emit('leave');
      },
    },
    components: {
      PSButton,
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .modal-header .close {
    font-size: 1.2rem;
    color: $gray-medium;
    opacity: 1;
  }
  .modal-content {
    border-radius: 0
  }
</style>
