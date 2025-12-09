<template>
    <VContainer class="pa-0">
        <VRow class="pa-0">
            <VCol
                cols="12"
                md="5">
                <VSheet
                    v-if="departments"
                    class="fill-height"
                    :class="{'is-mobile': isMobile}">
                    <VList>
                        <VListItem
                            v-for="d in departments"
                            :key="d.id"
                            :class="{'opacity-50':d.deleted_at !== null}"
                            :active="selectedDepartment?.id === d.id"
                            @click="selectDepartment(d)">
                            <template #title>
                                {{ d.name }}
                            </template>
                            <template #subtitle>
                                {{ d.description }}
                            </template>
                        </VListItem>
                    </VList>
                </VSheet>
            </VCol>
            <VCol
                cols="12"
                md="7">
                <VSheet>
                    <VSheet class="actions">
                        <VBtn
                            prepend-icon="mdi-plus"
                            :text="$t('Add')"
                            @click="selectedDepartment = {}" />
                        <VBtn
                            v-if="selectedDepartment"
                            class="ml-4"
                            prepend-icon="mdi-pencil"
                            :text="$t('Edit')" />
                        <VBtn
                            v-if="selectedDepartment && selectedDepartment.deleted_at === null"
                            class="ml-4"
                            color="default"
                            variant="tonal"
                            :text="$t('Disable')"
                            prepend-icon="mdi-eye-off"
                            @click="disableDepartment" />
                        <VBtn
                            v-if="selectedDepartment && typeof selectedDepartment.deleted_at === 'string'"
                            class="ml-4"
                            color="default"
                            variant="tonal"
                            :text="$t('Enable')"
                            prepend-icon="mdi-eye"
                            @click="enableDepartment" />
                        <VBtn
                            v-if="selectedDepartment"
                            color="error"
                            variant="tonal"
                            class="ml-4"
                            prepend-icon="mdi-close"
                            :text="$t('Delete')" />
                    </VSheet>
                    <VSheet class="mt-4">
                        <VTextField
                            v-model="selectedDepartment.name"
                            :label="$t('Name')" />
                        <VTextField
                            v-model="selectedDepartment.description"
                            class="mt-4"
                            :label="$t('Description')" />
                        <VBtn
                            class="mt-4"
                            :disabled="formDisabled"
                            prepend-icon="mdi-content-save"
                            :text="selectedDepartment.id ? $t('Save') : $t('Create')"
                            @click="saveDepartment" />
                    </VSheet>
                    <VSheet
                        v-if="selectedDepartment.id"
                        class="mt-4">
                        <h4 class="text-h6 mb-2">
                            {{ $t('Department members') }}
                        </h4>
                        <UsersMultiselect
                            class="mb-2"
                            :close-on-select="true"
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
                    </VSheet>
                </VSheet>
            </VCol>
        </VRow>
    </VContainer>
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
import { createSuccessNotification, createWarningNotification } from '@/js/helpers/notificationHelper.js'

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
        async onUsersChanged(users) {
            //e - Array
            users.map(async e => {
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
                        this.$store.commit('addNotification', createSuccessNotification(this.$t('Participant added successfully')))
                    }
                }
            })


        },
        async deleteMember(user) {
            await this.$store.dispatch('deleteMember', {
                departmentId: this.selectedDepartment.id,
                memberId: user.id
            }).then(() => {
                this.members = this.members.filter(m => m.id !== user.id)
                this.$store.commit('addNotification', createWarningNotification(this.$t('Participant deleted successfully')))
            })
        }
    }
}
</script>

<style lang="scss" scoped>
</style>
