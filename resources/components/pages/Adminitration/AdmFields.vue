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
            <VTextField
                v-model="term"
                clearable
                prepend-inner-icon="mdi-magnify"
                @update:model-value="filterFields">
                <template #append>
                    <VBtn
                        prepend-icon="mdi-plus"
                        :text="$t('Add field')"
                        @click="$refs.fieldModal.open()" />
                </template>
            </VTextField>
        </div>
        <div
            v-for="field in fieldFiltered"
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
                <VBtn
                    density="comfortable"
                    color="default"
                    variant="text"
                    icon="mdi-pencil"
                    @click="openModalWithField(field)" />
                <VBtn
                    density="comfortable"
                    color="error"
                    variant="text"
                    icon="mdi-close"
                    @click="deleteField(field.id)" />
            </div>
        </div>
        <ModalDialog
            ref="fieldModal"
            size="big"
            :title="id === null ? $t('Add field') : $t('Edit field')"
            @on-close="resetData">
            <VTextField
                v-model="name"
                :label="$t('Name')" />
            <VTextField
                v-model="description"
                class="mt-4"
                :label="$t('Description')" />
            <VSelect
                v-model="type"
                clearable
                class="mt-4"
                return-object
                item-value="value"
                item-title="name"
                track-by="value"
                :label="$t('Type')"
                :items="types" />
            <VTextarea
                v-model="options"
                class="mt-4"
                :label="$t('Options')" />
            <template #actions>
                <VBtn
                    variant="flat"
                    :disabled="disabled"
                    prepend-icon="mdi-content-save"
                    :text="$t('Save')"
                    @click="saveField" />
            </template>
        </ModalDialog>
        <ConfirmDialog ref="confirmField" />
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import { TYPES } from '../../../js/consts.js'
import ModalDialog from '../../chunks/ModalDialog.vue'
import ConfirmDialog from '../../elements/ConfirmDialog.vue'

const toast = useToast()

export default {
    name: 'AdmFields',
    components: {
        ConfirmDialog,
        ModalDialog
    },
    data() {
        return {
            term: null,
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
        },
        fieldFiltered() {
            return this.term === null ? this.fields : this.fields.filter(f => {
                console.log(this.term, f.name, f.name.includes(this.term.toLowerCase()))
                return f.name.toLowerCase().includes(this.term.toLowerCase())
            })
        }
    },
    async created() {
        this.loading = true
        await this.$store.dispatch('getFields')
        this.loading = false
    },
    methods: {
        filterFields(v) {

        },
        fieldType(field) {
            return this.$t(field.type)
        },
        async saveField() {
            const options = this.options
            // let options = null
            // try {
            //     options = JSON.parse(this.options)
            // } catch (e) {
            //     console.error(e)
            //     options = this.options
            // }
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
