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
  <div class="ps-checkboxes-dropdown">
    <div class="dropdown">
      <button
        :class="[
          'btn',
          'dropdown-toggle',
          selectedChoiceIds.length > 0 ? 'btn-primary' : 'btn-outline-secondary',
          'btn',
          {disabled: this.disabled}
        ]"
        type="button"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
        :data-role="`filter-by-${label.toLowerCase()}`"
      >
        {{ label }} {{ nbFiles }}
      </button>
      <div
        class="dropdown-menu"
        @click="preventClose"
      >
        <div
          class="md-checkbox"
          v-for="choice in choices"
          :key="choice.id"
        >
          <label class="dropdown-item">
            <div class="md-checkbox-container">
              <input
                :value="choice.id"
                :name="choice.name"
                type="checkbox"
                :checked="isSelected(choice)"
                @change="toggleSelection(choice)"
              >
              <i class="md-checkbox-control" />
              {{ choice.label }}
            </div>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';
  import {Choice} from '@app/components/checkboxes-dropdown/types';

  export default defineComponent({
    props: {
      parentId: {
        type: Number,
        default: 1,
      },
      choices: {
        type: Array as PropType<Choice[]>,
        required: true,
      },
      selectedChoiceIds: {
        type: Array as PropType<number[]>,
        default: () => [],
      },
      label: {
        type: String,
        required: true,
      },
      disabled: {
        type: Boolean,
        default: false,
      },
    },
    computed: {
      nbFiles(): string | null {
        return this.selectedChoiceIds.length > 0
          ? `(${this.selectedChoiceIds.length})`
          : null;
      },
    },
    methods: {
      isSelected(choice: Choice): boolean {
        return this.selectedChoiceIds.some((id) => choice.id === id);
      },
      toggleSelection(choice: Choice): void {
        if (this.selectedChoiceIds.some((id) => choice.id === id)) {
          this.$emit('unselectChoice', choice, this.parentId);
        } else {
          this.$emit('selectChoice', choice, this.parentId);
        }
      },
      preventClose(event: Event): void {
        event.stopPropagation();
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";
@import "~@scss/config/_bootstrap.scss";

.ps-checkboxes-dropdown {
  margin: 0 0.35rem;

  @include media-breakpoint-down(xs) {
    margin-bottom: .5rem;
  }

  .dropdown-item {
    padding: 0.438rem 1rem 0.438rem 0.938rem;
    line-height: normal;
    color: inherit;
    border-bottom: 0;

    .md-checkbox-container {
      position: relative;
      padding-left: 28px;
    }
  }
}
</style>
