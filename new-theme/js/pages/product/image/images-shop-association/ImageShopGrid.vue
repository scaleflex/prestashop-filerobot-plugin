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
<script lang="ts" setup>
  interface ImageShopGridProps {
    productImages: ProductImage[];
    productShops: ProductShop[];
  }

  defineProps<ImageShopGridProps>();

  const isImageDelete = (productImage: ProductImage): boolean => {
    let isImageDeleted = true;

    productImage.associations.forEach((association: ProductShopImage) => {
      if (association.isAssociated) {
        isImageDeleted = false;
      }
    });

    return isImageDeleted;
  };
</script>
<template>
  <div>
    <table class="image-shop-grid">
      <tr class="header-row">
        <th>
          {{ $t('grid.imageHeader') }}
        </th>
        <th
          :key="`shop-header${shop.shopId}`"
          v-for="shop in productShops"
        >
          {{ shop.shopName }}
        </th>
      </tr>
      <tr
        :key="`image-row-${productImage.imageId}`"
        v-for="productImage in productImages"
        :class="`${isImageDelete(productImage) ? 'deleted-image' : ''}`"
      >
        <td class="shop-image-cell">
          <img
            class="img-fluid"
            :src="productImage.thumbnailUrl"
          >
        </td>
        <td
          :key="`image-shop-association-${productImage.imageId}_${shopAssociation.shopId}`"
          v-for="shopAssociation in productImage.associations"
        >
          <div :class="`md-checkbox md-checkbox-inline ${shopAssociation.isCover ? 'cover-checkbox' : ''}`">
            <label>
              <input
                :name="`shop_association_${productImage.imageId}_${shopAssociation.shopId}`"
                type="checkbox"
                class="form-check-input"
                v-model="shopAssociation.isAssociated"
                :disabled="shopAssociation.isCover"
              >
              <i class="md-checkbox-control" />
            </label>
          </div>
          <span
            class="cover-label"
            v-if="shopAssociation.isCover"
          >
            {{ $t('cover.label') }}
          </span>
        </td>
      </tr>
    </table>
  </div>
</template>
