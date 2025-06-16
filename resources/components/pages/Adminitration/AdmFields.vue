<template>
    <div
        v-if="loading"
        class="alert alert-info">
        {{ $t('Loading, please wait...') }}
    </div>
    <div
        v-else
        class="fields">
        <div class="actions">
            <button
                class="btn btn-primary"
                @click="$refs.fieldModal.open()">
                <PlusIcon :size="18" />
                {{ $t('Add field') }}
            </button>
        </div>
        <div
            v-for="field in fields"
            :key="field.id"
            class="field">
            <div class="title">
                <div class="name">
                    {{ field.name }}
                </div>
                <div class="description">
                    {{ field.description }}
                </div>
                <div class="type">
                    {{ fieldType(field) }}
                </div>
            </div>
            <div class="buttons">
                <button
                    class="btn btn-purple"
                    @click="openModalWithField(field)">
                    <PencilIcon :size="18" />
                </button>
                <button
                    class="btn btn-danger"
                    @click="deleteField(field.id)">
                    <TrashCanIcon :size="18" />
                </button>
            </div>
        </div>
        <Modal
            ref="fieldModal"
            size="big"
            :title="id === null ? $t('Add field') : $t('Edit field')"
            @on-close="resetData">
            <div class="form-group">
                <label>{{ $t('Name') }}</label>
                <input
                    v-model="name"
                    type="text"
                    class="form-control">
            </div>
            <div class="form-group">
                <label>{{ $t('Description') }}</label>
                <input
                    v-model="description"
                    type="text"
                    class="form-control">
            </div>
            <div class="form-group">
                <label>{{ $t('Type') }}</label>
                <MultiselectElement
                    v-model="type"
                    :object="true"
                    value-prop="value"
                    label="name"
                    track-by="value"
                    :options="types" />
            </div>
            <div class="form-group">
                <label>{{ $t('Options') }}</label>
                <textarea
                    v-model="options"
                    class="form-control" />
            </div>
            <div class="form-group">
                <button
                    :disabled="disabled"
                    class="btn btn-primary w-100"
                    @click="saveField">
                    <ContentSaveIcon :size="18" />
                    {{ $t('Save') }}
                </button>
            </div>
        </Modal>
        <ConfirmDialog ref="confirmField" />
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import { TYPES } from '../../../js/consts.js'
import MultiselectElement from '../../elements/MultiselectElement.vue'
import Modal from '../../elements/Modal.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import PencilIcon from 'vue-material-design-icons/Pencil.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import ConfirmDialog from '../../elements/ConfirmDialog.vue'

const toast = useToast()

export default {
    name: 'AdmFields',
    components: {
        PencilIcon,
        TrashCanIcon,
        PlusIcon,
        ContentSaveIcon,
        Modal,
        MultiselectElement,
        ConfirmDialog
    },
    data() {
        return {
            loading: false,
            id: null,
            name: '',
            description: '',
            options: '',
            type: null
        }
    },
    computed: {
        disabled() {
            return this.name.length < 3 || this.description.length < 3 || this.type === null
        },
        fields() {
            return this.$store.getters['getFields']
        },
        types() {
            return Object.values(TYPES).map(type => {
                return {
                    name: this.$t(type),
                    value: type
                }
            })
        }
    },
    async created() {
        this.loading = true
        await this.$store.dispatch('getFields')
        this.loading = false
    },
    methods: {
        fieldType(field) {
            return this.$t(field.type)
        },
        async saveField() {
            let options = null
            try {
                options = JSON.parse(this.options)
            } catch (e) {
                console.error(e)
                options = this.options
            }
            const data = {
                id: this.id,
                name: this.name,
                description: this.description,
                options: options, // конвертируем обратно
                type: this.type?.value
            }
            if (this.id > 0) {
                await this.$store.dispatch('editField', data).catch(e => {
                    toast.error(e?.response?.data?.message)
                }).then(() => {
                    toast.success(this.$t('Field edited successfully'))
                })
            } else {
                await this.$store.dispatch('addField', data).catch(e => {
                    toast.error(e?.response?.data?.message)
                }).then(() => {
                    toast.success(this.$t('Field created successfully'))
                })
            }
            this.$refs.fieldModal.close()
            this.resetData()
        },
        async deleteField(id) {
            const ok = await this.$refs.confirmField.show({
                title: this.$t('Delete field'),
                message: this.$t('Are you sure you want to delete this field?'),
                okButton: this.$t('Delete')
            })
            if (ok) {
                await this.$store.dispatch('deleteField', id).catch(e => {
                    toast.error(e?.response?.data?.message)
                }).then(() => {
                    toast.warning('Field deleted')
                })
            }
        },
        openModalWithField(field) {
            this.id = field.id
            this.name = field.name
            this.description = field.description
            try {
                this.options = field.options !== null ? JSON.stringify(JSON.parse(field.options), null, '  ') : null
            } catch (e) {
                console.error(e)
                this.options = field.options
            }
            this.type = {
                name: this.fieldType(field),
                value: field.type
            }
            this.$refs.fieldModal.open()
        },
        resetData() {
            this.id = null
            this.name = ''
            this.description = ''
            this.options = ''
            this.type = null
        }
    }

}
</script>

<style lang="scss" scoped>
.actions {
    padding-bottom: var(--padding-box);
}

.field {
    padding: calc(var(--padding-box) / 2);
    margin-bottom: var(--padding-box);
    border-radius: var(--border-radius);
    border: 1px solid var(--bs-border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: var(--transition-duration);

    .title {
        .name {
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }

        .description {
            font-size: 12px;
        }

        .type {
            margin-top: 4px;
            font-family: var(--bs-font-monospace);
            font-size: 12px;
        }
    }

    .buttons {
        .btn {
            margin-left: 6px;
        }
    }

    &:hover {
        background-color: var(--bs-light);
    }
}
</style>
