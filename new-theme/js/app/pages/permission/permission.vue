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
  <div class="card">
    <h3 class="card-header">
      {{ title }}
    </h3>
    <div class="card-body">
      <div class="table js-permissions-table">
        <bulk
          :types="types"
          :profile-permissions.sync="profileDataPermissions"
          @updateBulk="updateBulk"
        />
        <div
          class="col-xs-12"
          v-if="permissions === null"
        >
          <td colspan="6">
            {{ emptyData }}
          </td>
        </div>

        <template
          v-else
          v-for="(permission, permissionId) in permissions"
          :key="permissionId"
        >
          <row
            :can-edit="canEdit"
            :level-depth="1"
            :max-level-depth="4"
            :permission="permission"
            :permission-id="permissionId"
            :permission-key="permissionKey"
            :profile-permissions.sync="profileDataPermissions"
            :employee-permissions="employeePermissions"
            :parent="permission.children !== undefined"
            :types="Object.keys(types)"
            @sendRequest="sendRequest"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import Bulk from './components/bulk.vue';
  import Row from './components/row.vue';

  const {$} = window;

  interface Data {
    profileDataPermissions: Record<string, any>;
  }

  export default defineComponent({
    name: 'Permission',
    components: {
      Bulk,
      Row,
    },
    props: {
      title: {
        type: String,
        required: true,
      },
      emptyData: {
        type: String,
        required: true,
      },
      profileId: {
        type: Number,
        required: true,
      },
      messages: {
        type: Object,
        required: true,
      },
      updateUrl: {
        type: String,
        required: true,
      },
      permissionKey: {
        type: String,
        required: true,
      },
      types: {
        type: Object,
        required: true,
      },
      permissions: {
        type: Object,
        required: true,
      },
      profilePermissions: {
        type: Object,
        required: true,
      },
      employeePermissions: {
        type: Object,
        required: true,
      },
      canEdit: {
        type: Boolean,
        required: false,
        default: false,
      },
    },
    data(): Data {
      return {
        profileDataPermissions: this.profilePermissions,
      };
    },
    methods: {
      /**
       * Send ajax request to target url
       */
      sendRequest(data: Record<string, any>): void {
        data.profile_id = this.profileId;

        $.ajax(
          this.updateUrl,
          {
            method: 'POST',
            data,
          },
        ).then((response) => {
          if (response.success) {
            window.showSuccessMessage(this.messages.success);
            return;
          }

          window.showErrorMessage(this.messages.error);
        }).catch(() => {
          window.showErrorMessage(this.messages.error);
        });
      },
      /**
       * Update user permissions from bulk action
       */
      updateBulk(data: Record<string, any>): void {
        Object.keys(this.profileDataPermissions).forEach((key: string) => {
          data.types.forEach((type: string) => {
            this.profileDataPermissions[key][type] = data.status ? '1' : '0';
          });
        });

        const params: Record<string, any> = {
          is_active: data.status,
          permission: data.updateType,
        };

        params[this.permissionKey] = '-1';

        this.sendRequest(params);
      },
    },
  });
</script>

<style lang="scss">
  .js-permissions-table {
    .permission-row {
      padding: 4px 0;
      border-bottom: 1px solid #bbcdd2;
    }

    .bulk-row {
      padding-bottom: 10px;
      border-bottom: .125rem solid #25b9d7;
      strong {
        display: block;
        font-size: 12px;
        font-weight: 600;
        font-family: Open-sans, sans-serif;
        white-space: nowrap;
        padding-bottom: 5px;
      }
    }
  }
</style>
