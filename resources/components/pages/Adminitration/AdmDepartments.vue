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
            <div class="form mb-4">
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
                <div class="form-group">
                    <button
                        class="btn btn-purple"
                        :disabled="formDisabled"
                        @click="saveDepartment">
                        <ContentSaveIcon :size="18" />
                        {{ selectedDepartment.id ? $t('Save') : $t('Create') }}
                    </button>
                </div>
            </div>
            <div
                v-if="selectedDepartment.id"
                class="department-members">
                <h4>{{ $t('Department members') }}</h4>
                <UsersMultiselect
                    :close-on-select="true"
                    mode="single"
                    :value="memberSelected"
                    @on-users-changed="onUsersChanged" />
                <div
                    v-if="members"
                    class="members">
                    <UserListItem
                        v-for="user in members"
                        :key="user.id"
                        :user="user">
                        <template #actions>
                            <button
                                class="btn btn-transparent btn-icon"
                                @click="deleteMember(user)">
                                <CloseIcon :size="18" />
                            </button>
                        </template>
                    </UserListItem>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import CloseIcon from 'vue-material-design-icons/Close.vue'
import UserListItem from '../../chunks/UserListItem.vue'
import UsersMultiselect from '../../elements/UsersMultiselect.vue'
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
        CloseIcon,
        ContentSaveIcon,
        PlusIcon,
        PencilIcon,
        TrashCanIcon,
        EyeIcon,
        EyeOffIcon,
        UsersMultiselect,
        UserListItem
    },
    data() {
        return {
            loading: false,
            selectedDepartment: {},
            members: [],
            memberSelected: null
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
    watch: {
        selectedDepartment() {
            if (!this.selectedDepartment.hasOwnProperty('id')) {
                this.members = []
            }
        }
    },
    methods: {
        async selectDepartment(d) {
            if (this.selectedDepartment.id === d.id) {
                this.selectedDepartment = {}
                this.members = []
            } else {
                this.selectedDepartment = d
                const res = await this.$store.dispatch('getDepartmentMembers', d.id)
                if (res) {
                    this.members = res.data
                }
            }

        },
        async saveDepartment() {
            if (!this.formDisabled) {
                this.loading = true
                const method = this.selectedDepartment.id ? 'updateDepartment' : 'addDepartment'
                await this.$store.dispatch(method, this.selectedDepartment).then(() => {
                    this.selectedDepartment = []
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
        },
        async onUsersChanged(e) {
            if (typeof e === 'object' && e.hasOwnProperty('id')) {
                this.memberSelected = e
                const res = await this.$store.dispatch('addMember', {
                    departmentId: this.selectedDepartment.id,
                    memberId: this.memberSelected.id
                })
                if (res) {
                    const founded = this.members.find(m => m.id === this.memberSelected.id)
                    if (typeof founded !== 'object') {
                        this.members.push(this.memberSelected)
                    }
                    this.memberSelected = null
                    toast.success(this.$t('Participant added successfully'))
                }
            }

        },
        async deleteMember(user) {
            await this.$store.dispatch('deleteMember', {
                departmentId: this.selectedDepartment.id,
                memberId: user.id
            }).then(() => {
                this.members = this.members.filter(m => m.id !== user.id)
                toast.warning(this.$t('Participant deleted successfully'))
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.departments {
    position: relative;
    height: inherit;
    margin: -16px;
    display: flex;
    align-items: stretch;


    .departments-list {
        background: var(--bs-light);
        width: 30%;
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
        width: 70%;

        .actions {
            margin-bottom: 16px;

            .btn {
                margin-right: 6px;
            }
        }

        h4 {
            margin: 30px 0 20px 0;
            font-size: 20px;
        }

        .members {
            margin-top: 20px;
        }
    }

}
</style>
