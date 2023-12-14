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
  <div class="header-toolbar">
    <div class="container-fluid">
      <Breadcrumb />
      <div class="title-row">
        <h1 class="title">
          {{ trans('head_title') }}
        </h1>
      </div>
    </div>
    <Tabs />
  </div>
</template>

<script lang="ts">
  import ComponentsMap from '@components/components-map';
  import {defineComponent} from 'vue';
  import translate from '@app/pages/stock/mixins/translate';
  import Breadcrumb from './breadcrumb.vue';
  import Tabs from './tabs.vue';

  const {$} = window;

  function getOldHeaderToolbarButtons() {
    return $('.header-toolbar')
      .first()
      .find('.toolbar-icons');
  }

  function getNotificationsElements() {
    return $(`${ComponentsMap.ajaxConfirmation}, #${ComponentsMap.contextualNotification.messageBoxId}`);
  }

  export default defineComponent({
    components: {
      Breadcrumb,
      Tabs,
    },
    mixins: [translate],
    mounted() {
      const $vueElement = $(this.$el);
      // move the toolbar buttons to this header
      const toolbarButtons = getOldHeaderToolbarButtons();
      toolbarButtons.insertAfter($vueElement.find('.title-row > .title'));

      const notifications = getNotificationsElements();
      notifications.insertAfter($vueElement);

      // signal header change (so size can be updated)
      const event = $.Event('vueHeaderMounted', {
        name: 'stock-header',
      });
      $(document).trigger(event);
    },
  });
</script>
