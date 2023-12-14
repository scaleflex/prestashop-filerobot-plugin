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
  <div class="row">
    <div class="col-4">
      <h4>{{ $t('step.symbol') }}</h4>
      <input
        data-role="custom-symbol"
        type="text"
        v-model="customSymbol"
      >
    </div>
    <div class="col-8 border-left">
      <h4>{{ $t('step.format') }}</h4>
      <div class="row">
        <div
          class="ps-radio col-6"
          v-for="(pattern, transformation) in availableFormats"
          :key="transformation"
          :id="transformation"
        >
          <input
            type="radio"
            :checked="transformation === customTransformation"
            :value="transformation"
          >
          <label @click.prevent.stop="customTransformation = transformation">
            {{ displayPattern(pattern) }}
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import {NumberFormatter} from '@app/cldr';
  import {defineComponent} from 'vue';

  export default defineComponent({
    name: 'CurrencyFormatForm',
    data: () => ({
      value: {
        symbol: '',
        transformation: '',
      },
    }),
    props: {
      language: {
        type: Object,
        required: true,
        default: () => {},
      },
    },
    computed: {
      availableFormats() {
        return this.language.transformations;
      },
      customSymbol: {
        get() {
          return this.value.symbol;
        },
        set(symbol) {
          this.value.symbol = symbol;
          this.$emit('formatChange', this.value);
        },
      },
      customTransformation: {
        get() {
          return this.value.transformation;
        },
        set(transformation) {
          this.value.transformation = transformation;
          this.$emit('formatChange', this.value);
        },
      },
    },
    methods: {
      displayPattern(pattern) {
        const patterns = pattern.split(';');
        const priceSpecification = {...this.language.priceSpecification};
        priceSpecification.positivePattern = patterns[0];
        priceSpecification.negativePattern = patterns.length > 1 ? patterns[1] : `-${pattern}`;
        priceSpecification.currencySymbol = this.customSymbol;

        const currencyFormatter = NumberFormatter.build(priceSpecification);

        return currencyFormatter.format(14251999.42);
      },
    },
    mounted() {
      this.customSymbol = this.language.priceSpecification.currencySymbol;
      const currencyPattern = this.language.priceSpecification.positivePattern;

      // Detect which transformation matches the language pattern
      /* eslint-disable-next-line no-restricted-syntax,guard-for-in */
      for (const transformation in this.language.transformations) {
        const transformationPatterns = this.language.transformations[
          transformation
        ].split(';');

        if (transformationPatterns[0] === currencyPattern) {
          this.customTransformation = transformation;
          break;
        }
      }
    },
  });
</script>
