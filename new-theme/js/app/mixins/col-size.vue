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
<script lang="ts">
  /**
   * This mixin component will help you to calculate your col size
   * depending on object size.
   */

  // Bootstrap row length
  const ROW_LENGTH = 12;

  export default {
    methods: {
      getClasses(object: Record<string, any>, offsetCondition = true): Array<string> {
        const objectLength = Object.keys(object).length;
        const size = Math.floor(ROW_LENGTH / objectLength);
        const left = Math.ceil(ROW_LENGTH % objectLength);
        const classes = [`col-${size}`];

        if (left !== 0 && offsetCondition) {
          classes.push(`offset-${left}`);
        }

        return classes;
      },
    },
  };
</script>
