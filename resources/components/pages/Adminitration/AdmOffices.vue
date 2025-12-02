<template>
    <div class="offices">
        <div class="actions">
            <VBtn
                prepend-icon="mdi-plus"
                :text="$t('Add office')"
                @click="openOfficeModal" />
            <VBtn
                :disabled="loading"
                :loading="loading"
                color="default"
                density="comfortable"
                class="ml-2"
                icon="mdi-refresh"
                @click="$store.dispatch('getOffices')" />
        </div>
        <VList>
            <AdmOfficeItem
                v-for="o in offices"
                :key="o.id"
                :office="o"
                @on-delete-office="deleteOffice"
                @on-edit-click="openOfficeModal" />
        </VList>
        <ModalDialog
            ref="officeModal"
            :title="office !== null ? $t('Edit office') : $t('Add office')">
            <VTextField
                v-model="name"
                :label="$t('Name')" />
            <VTextarea
                v-model="address"
                class="mt-4"
                :label="$t('Address')" />
            <template #actions>
                <VBtn
                    :disabled="disabled"
                    prepend-icon="mdi-content-save"
                    :text="$t('Save')"
                    @click="saveOffice" />
            </template>
        </ModalDialog>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import ModalDialog from '../../chunks/ModalDialog.vue'
import AdmOfficeItem from '../../chunks/AdmOfficeItem.vue'
import { createSuccessNotification, createWarningNotification } from '@/js/helpers/notificationHelper.js'

const toast = useToast()

export default {
    name: 'AdmOffices',
    components: {
        AdmOfficeItem,
        ModalDialog
    },
    data() {
        return {
            office: null,
            name: null,
            address: null
        }
    },
    computed: {
        loading() {
            return this.$store.getters['isLoading']
        },
        disabled() {
            return this.name?.left < 3 || this.address?.length < 3 || this.name === null || this.address === null
        },
        offices() {
            return this.$store.getters['getOffices']
        }
    },
    methods: {
        openOfficeModal(e) {
            if (e !== null && e.hasOwnProperty('id')) {
                this.office = e
                this.name = this.office.name
                this.address = this.office.address
            } else {
                this.office = null
                this.name = null
                this.address = null
            }

            this.$refs.officeModal.open()
        },
        async saveOffice() {
            let data = {
                name: this.name,
                address: this.address
            }
            let action = 'createOffice'
            if (this.office !== null) {
                action = 'editOffice'
                data = Object.assign({ id: this.office.id }, data)
            }
            await this.$store.dispatch(action, data)
            this.$store.commit('addNotification', createSuccessNotification(this.$t('Office deleted')))
            this.$refs.officeModal.close()
        },
        async deleteOffice(o) {
            await this.$store.dispatch('deleteOffice', o.id)
            this.$store.commit('addNotification', createWarningNotification(this.$t('Office deleted')))
        }
    }
}
</script>
