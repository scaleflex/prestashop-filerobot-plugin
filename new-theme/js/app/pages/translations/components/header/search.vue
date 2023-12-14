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
  <div id="search">
    <form
      class="search-form"
      @submit.prevent
    >
      <label>{{ trans('search_label') }}</label>
      <div class="input-group">
        <PSTags
          ref="psTags"
          :tags="tags"
          @tagChange="onSearch"
          :placeholder="trans('search_placeholder')"
        />
        <div class="input-group-append">
          <PSButton
            @click="onClick"
            class="search-button"
            :primary="true"
          >
            <i class="material-icons">search</i>
            {{ trans('button_search') }}
          </PSButton>
        </div>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
  import PSTags from '@app/widgets/ps-tags.vue';
  import PSButton from '@app/widgets/ps-button.vue';
  import {defineComponent} from 'vue';
  import TranslationMixin from '@app/pages/translations/mixins/translate';

  export default defineComponent({
    components: {
      PSTags,
      PSButton,
    },
    mixins: [TranslationMixin],
    methods: {
      onClick() {
        const refPsTags = this.$refs.psTags as VTags;
        const {tag} = refPsTags;
        refPsTags.add(tag);
      },
      onSearch() {
        this.$store.dispatch('updateSearch', this.tags);
        this.$emit('search', this.tags);
      },
    },
    watch: {
      $route() {
        this.tags = [];
      },
    },
    data() {
      return {
        tags: [],
      };
    },
  });
</script>
