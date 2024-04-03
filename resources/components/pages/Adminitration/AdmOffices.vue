<template>
    <div class="offices">
        <div class="actions">
            <button
                class="btn btn-primary"
                @click="openOfficeModal">
                <PlusIcon :size="18" />
                {{ $t('Add office') }}
            </button>
            <button
                class="btn btn-icon btn-purple mx-2"
                @click="$store.dispatch('getOffices')">
                <RefreshIcon :size="18" />
            </button>
        </div>
        <AdmOfficeItem
            v-for="o in offices"
            :key="o.id"
            :office="o"
            @on-delete-office="deleteOffice"
            @on-edit-click="openOfficeModal" />
        <Modal
            ref="officeModal"
            :title="office !== null ? $t('Edit office') : $t('Add office')">
            <div class="form-group">
                <label for="">{{ $t('Name') }}</label>
                <input
                    v-model="name"
                    type="text"
                    class="form-control">
            </div>
            <div class="form-group">
                <label for="">{{ $t('Address') }}</label>
                <input
                    v-model="address"
                    type="text"
                    class="form-control">
            </div>
            <div class="form-group">
                <button
                    :disabled="disabled"
                    class="btn btn-purple w-100"
                    @click="saveOffice">
                    <ContentSaveIcon :size="20" />
                    {{ $t('Save') }}
                </button>
            </div>
        </Modal>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import Modal from '../../elements/Modal.vue'
import RefreshIcon from 'vue-material-design-icons/Refresh.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import AdmOfficeItem from '../../chunks/AdmOfficeItem.vue'

const toast = useToast()

export default {
    name: 'AdmOffices',
    components: {
        AdmOfficeItem,
        PlusIcon,
        RefreshIcon,
        Modal,
        ContentSaveIcon
    },
    data() {
        return {
            office: null,
            name: null,
            address: null
        }
    },
    computed: {
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
            toast.success(this.$t('Office saved'))
            this.$refs.officeModal.close()
        },
        async deleteOffice(o) {
            await this.$store.dispatch('deleteOffice', o.id)
            toast.warning(this.$t('Office deleted'))
        }
    }
}
</script>

<style scoped lang="scss">
.actions {
    margin-bottom: 16px;
}
</style>
