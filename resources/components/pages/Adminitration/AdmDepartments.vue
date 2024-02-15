<template>
    <div
        v-if="departments"
        class="departments"
        :class="{'is-mobile': isMobile}">
        <div
            class="departments-list">
            <div
                v-for="d in departments"
                :key="d.id"
                class="department"
                :class="{active: selectedDepartment?.id === d.id, disabled: d.deleted_at !== null}"
                @click="selectDepartment(d)">
                <div class="name">
                    {{ d.name }}
                </div>
                <div class="description">
                    {{ d.description }}
                </div>
            </div>
        </div>
        <div class="department-content">
            <div class="actions">
                <button
                    class="btn btn-purple"
                    @click="selectedDepartment = []">
                    <PlusIcon :size="18" />
                    {{ $t('Add') }}
                </button>
                <button
                    v-if="selectedDepartment"
                    class="btn btn-secondary">
                    <PencilIcon :size="18" />
                    {{ $t('Edit') }}
                </button>
                <button
                    v-if="selectedDepartment && selectedDepartment.deleted_at === null"
                    class="btn btn-secondary"
                    @click="disableDepartment">
                    <EyeOffIcon :size="18" />
                    {{ $t('Disable') }}
                </button>
                <button
                    v-if="selectedDepartment && typeof selectedDepartment.deleted_at === 'string'"
                    class="btn btn-secondary"
                    @click="enableDepartment">
                    <EyeIcon :size="18" />
                    {{ $t('Enable') }}
                </button>
                <button
                    v-if="selectedDepartment"
                    class="btn btn-danger">
                    <TrashCanIcon :size="18" />
                    {{ $t('Delete') }}
                </button>
            </div>
            <div class="form">
                <div class="form-group">
                    <label for="">{{ $t('Name') }}</label>
                    <input
                        v-model="selectedDepartment.name"
                        class="form-control"
                        type="text">
                </div>
                <div class="form-group">
                    <label for="">{{ $t('Description') }}</label>
                    <input
                        v-model="selectedDepartment.description"
                        class="form-control"
                        type="text">
                </div>
                <button
                    class="btn btn-purple"
                    :disabled="formDisabled"
                    @click="saveDepartment">
                    <ContentSaveIcon :size="18" />
                    {{ selectedDepartment.id ? $t('Save') : $t('Create') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'
import PencilIcon from 'vue-material-design-icons/Pencil.vue'
import EyeOffIcon from 'vue-material-design-icons/EyeOff.vue'
import EyeIcon from 'vue-material-design-icons/Eye.vue'

const toast = useToast()

export default {
    name: 'AdmDepartments',
    components: {
        ContentSaveIcon,
        PlusIcon,
        PencilIcon,
        TrashCanIcon,
        EyeIcon,
        EyeOffIcon
    },
    data() {
        return {
            loading: false,
            selectedDepartment: []
        }
    },
    computed: {
        formDisabled() {
            if (this.selectedDepartment.hasOwnProperty('name') && this.selectedDepartment.hasOwnProperty('description')) {
                return this.loading || this.selectedDepartment.name.length <= 3 || this.selectedDepartment.description.length <= 3
            }
            return true
        },
        departments() {
            return this.$store.getters['getDepartments']
        },
        isMobile() {
            return this.$store.getters['isMobile']
        }
    },
    methods: {
        selectDepartment(d) {
            if (this.selectedDepartment.id === d.id) {
                this.selectedDepartment = []
            } else {
                this.selectedDepartment = d
            }
        },
        async saveDepartment() {
            if (!this.formDisabled) {
                this.loading = true
                const method = this.selectedDepartment.id ? 'updateDepartment' : 'addDepartment'
                await this.$store.dispatch(method, this.selectedDepartment).then(() => {
                    toast.success(this.$t('Department saved'))
                }).catch(e => {
                    toast.error(this.$t('Error saving department'))
                }).finally(() => {
                    this.loading = false
                })

            }
        },
        async disableDepartment() {
            if (!this.selectedDepartment.hasOwnProperty('id')) return
            this.loading = true
            await this.$store.dispatch('disableDepartment', this.selectedDepartment.id).then(() => {
                toast.warning(this.$t('Department disabled'))
                this.selectedDepartment.deleted_at = new Date().toDateString()
            }).catch(e => {
                console.log(e)
                toast.error(this.$t('Error disabling department'))
            })
        },

        async enableDepartment() {
            if (!this.selectedDepartment.hasOwnProperty('id')) return
            this.loading = true
            await this.$store.dispatch('enableDepartment', this.selectedDepartment.id).then(() => {
                toast.warning(this.$t('Department enabled'))
                this.selectedDepartment.deleted_at = null
            }).catch(e => {
                console.log(e)
                toast.error(this.$t('Error enabling department'))
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.departments {
    position: relative;
    height: calc(100vh - var(--header-height));
    margin: -16px;


    .departments-list {
        background: var(--bs-light);
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 400px;
        border-right: var(--bs-border-width) solid var(--bs-border-color);

        .department {
            padding: var(--padding-box);
            border-bottom: 1px solid var(--bs-border-color);
            transition: var(--transition-duration);
            cursor: pointer;

            .name {
                font-weight: bold;
            }

            &:hover {
                background: var(--bs-gray-200);
            }

            &.active {
                background-color: var(--bs-gray-200);
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1) inset;
            }

            &.disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
        }
    }

    .department-content {
        padding: var(--padding-box);
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        left: 400px;

        .actions {
            margin-bottom: 16px;

            .btn {
                margin-right: 6px;
            }
        }
    }

}
</style>
