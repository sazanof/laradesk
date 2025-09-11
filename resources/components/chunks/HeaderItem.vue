<script>
import LanDisconnectIcon from 'vue-material-design-icons/LanDisconnect.vue'
import LanCheckIcon from 'vue-material-design-icons/LanCheck.vue'
import LanPendingIcon from 'vue-material-design-icons/LanPending.vue'
import CrownIcon from 'vue-material-design-icons/Crown.vue'
import ShieldIcon from 'vue-material-design-icons/Shield.vue'
import Avatar from './Avatar.vue'
import NotificationsWrapper from '../elements/NotificationsWrapper.vue'


export default {
    name: 'HeaderItem',
    components: {
        CrownIcon,
        ShieldIcon,
        Avatar,
        LanDisconnectIcon,
        LanCheckIcon,
        LanPendingIcon,
        NotificationsWrapper
    },
    data() {
        return {
            showUserPopper: false,
            isCollapsed: this.$store.state.collapsed,
            activeDepartmentData: null
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        connected() {
            return this.$store.getters['getConnectionState']
        },
        connecting() {
            return this.$store.getters['getConnectingState']
        },
        departments() {
            return this.$store.getters['getUserDepartments']
        },
        activeDepartment() {
            return this.$store.getters['getActiveDepartment']
        },
        status() {
            if (this.connecting) {
                return this.$t('Connecting to websocket server...')
            } else if (!this.connecting && this.connected) {
                return this.$t('Connected to websocket server')
            } else {
                return this.$t('Error connect to websocket server!')
            }
        },
        isAdmin() {
            return this.$store.state.isAdmin
        },
        isSuperAdmin() {
            return this.$store.state.isSuperAdmin
        }
    },
    watch: {
        activeDepartment() {
            this.activeDepartmentData = this.activeDepartment
        }
    },
    mounted() {
        this.$nextTick(() => {
            // find if active department really exists in all departments list
            let res = null
            if (this.activeDepartment !== null) {
                res = this.departments.find(d => {
                    return d.id === this.activeDepartment.id
                })
            }

            if (typeof res !== 'object') {
                this.$store.commit('setActiveDepartment', null)
            } else {
                console.log(this.user)
                const activeDepartment = this.user.departments.find(d => d.is_default)
                this.$store.commit('setActiveDepartment', activeDepartment?.department)
            }
        })
    },
    methods: {
        setCollapsed() {
            this.isCollapsed = this.isCollapsed === 'false' ? 'true' : 'false'
            this.$store.dispatch('setCollapsed', this.isCollapsed)
        },
        changeDepartment(e) {
            this.emitter.emit('on-department-changed', e)
            window.location.reload()
        }
    }
}
</script>
<template>
    <VAppBar
        color="rgba(0,0,0,0.05)"
        elevation="1">
        <template #prepend>
            <VBtn
                rounded
                :icon="isCollapsed === 'true' ?'mdi-menu-close' : 'mdi-menu-open'"
                @click="setCollapsed" />
            <VBtn
                rounded
                icon="mdi-arrow-left"
                @click="$router.back(-1)" />
            <VBtn
                v-if="activeDepartment !== null && activeDepartment !== undefined"
                v-tooltip="activeDepartment.name"
                rounded
                :text="activeDepartment.name"
                icon="mdi-account-group" />
        </template>
        <template #append>
            <NotificationsWrapper />
            <VBtn :color="connected?'success':'error'">
                <span v-tooltip="status">
                    <LanPendingIcon
                        v-if="connecting"
                        :size="24" />
                    <LanCheckIcon
                        v-if="connected && !connecting"
                        :size="24" />
                    <LanDisconnectIcon
                        v-if="!connected && !connecting"
                        :size="24" />
                </span>
            </VBtn>

            <VMenu
                :close-on-content-click="false"
                width="300"
                hover>
                <template #activator="{props}">
                    <div
                        v-bind="props"
                        class="position-relative avatar-trigger position-relative">
                        <div class="position-absolute bottom-0 right-0">
                            <VIcon
                                v-if="isSuperAdmin"
                                icon="mdi-crown"
                                :size="14"
                                color="yellow" />
                            <VIcon
                                v-if="isAdmin"
                                end
                                icon="mdi-shield"
                                color="green"
                                size="14" />
                        </div>
                        <Avatar
                            :size="48"
                            :user="user" />
                    </div>
                </template>
                <template #default>
                    <VCard>
                        <template
                            #prepend>
                            {{ user.firstname }} {{ user.lastname }}
                        </template>
                        <template #append>
                            <VBtn
                                v-tooltip="$t('Logout')"
                                variant="text"
                                density="comfortable"
                                href="/logout"
                                icon="mdi-logout-variant" />
                        </template>

                        <template #text>
                            <div
                                v-if="isAdmin && departments.length > 0"
                                class="departments-select">
                                <VSelect
                                    v-model="activeDepartmentData"
                                    :items="departments"
                                    :return-object="true"
                                    item-title="name"
                                    item-value="id"
                                    @update:model-value="changeDepartment" />
                            </div>

                            <VList
                                density="compact"
                                rounded
                                @click="showUserPopper = !showUserPopper">
                                <VListItem
                                    to="/profile"
                                    prepend-icon="mdi-account"
                                    class="list-group-item"
                                    :title="$t('Profile')" />
                                <VListItem
                                    v-if="isSuperAdmin"
                                    to="/admin/management"
                                    prepend-icon="mdi-crown"
                                    class="list-group-item"
                                    :title="$t('Administration')" />
                                <VListItem
                                    v-if="isSuperAdmin"
                                    to="/admin/settings"
                                    prepend-icon="mdi-cog"
                                    :title="$t('Settings')"
                                    class="list-group-item" />
                            </VList>
                        </template>
                    </VCard>
                </template>
            </VMenu>
        </template>
    </VAppBar>
</template>

<style lang="scss" scoped>

</style>
