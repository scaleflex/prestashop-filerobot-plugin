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
    class="tags-input search-input search d-flex flex-wrap"
    :class="{ 'search-with-icon': hasIcon }"
    @click="focus()"
  >
    <div class="tags-wrapper">
      <span
        v-for="(tag, index) in tags"
        :key="index"
        class="tag"
      >{{ tag }}<i
        class="material-icons"
        @click="close(index)"
      >close</i></span>
    </div>
    <input
      ref="tags"
      :placeholder="placeholderToDisplay"
      type="text"
      v-model="tag"
      class="form-control input"
      @keyup="onKeyUp"
      @keydown.enter="add(tag)"
      @keydown.delete.stop="remove()"
      :size="inputSize"
    >
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      tags: {
        type: Array,
        required: false,
        default: () => ([]),
      },
      placeholder: {
        type: String,
        required: false,
        default: '',
      },
      hasIcon: {
        type: Boolean,
        required: false,
      },
    },
    computed: {
      inputSize(): number {
        return !this.tags.length && this.placeholder ? this.placeholder.length : 0;
      },
      placeholderToDisplay(): string {
        return this.tags.length ? '' : this.placeholder;
      },
    },
    methods: {
      onKeyUp() {
        this.$emit('typing', (<VTagsInput> this.$refs.tags).value);
      },
      add(tag: string): void {
        if (tag) {
          this.tags.push(tag.trim());
          this.tag = '';
          this.focus();
          this.$emit('tagChange', this.tag);
        }
      },
      close(index: number): void {
        const tagName = this.tags[index];
        this.tags.splice(index, 1);
        this.$emit('tagChange', tagName);
      },
      remove(): void {
        if (this.tags && this.tags.length && !this.tag.length) {
          const tagName = this.tags[this.tags.length - 1];
          this.tags.pop();
          this.$emit('tagChange', tagName);
        }
      },
      focus(): void {
        (<HTMLInputElement> this.$refs.tags).focus();
      },
    },
    data: () => ({tag: ''}),
  });
</script>
