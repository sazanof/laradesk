<script>
import Avatar from './Avatar.vue'
import ThemeSwitcher from './ThemeSwitcher.vue'
import NotificationsWrapper from '../elements/NotificationsWrapper.vue'


export default {
    name: 'HeaderItem',
    components: {
        Avatar,
        ThemeSwitcher,
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
            //window.location.reload()
        }
    }
}
</script>
<template>
    <VAppBar
        scroll-behavior="elevate"
        height="76"
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
            <ThemeSwitcher class="mr-2" />
            <NotificationsWrapper />
            <VBtn
                icon=""
                density="comfortable"
                :color="connected?'success':'error'">
                <span v-tooltip="status">
                    <VIcon
                        v-if="connecting"
                        icon="mdi-lan-pending"
                        :size="24" />
                    <VIcon
                        v-if="connected && !connecting"
                        icon="mdi-lan-check"
                        :size="24" />
                    <VIcon
                        v-if="!connected && !connecting"
                        icon="mdi-lan-disconnect"
                        :size="24" />
                </span>
            </VBtn>

            <VBtn
                :to="{name:'contacts'}"
                variant="tonal"
                color="primary"
                rounded="pill"
                size="x-large"
                class="mx-2"
                icon="mdi-book-account-outline"
                density="comfortable" />

            <VMenu
                :close-on-content-click="false"
                width="300"
                hover>
                <template #activator="{props}">
                    <div
                        v-bind="props"
                        class="mr-2 position-relative">
                        <div
                            class="position-absolute bottom-0 right-0"
                            style="z-index:10">
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
                            <div class="font-weight-bold">
                                {{ user.firstname }} {{ user.lastname }}
                            </div>
                        </template>
                        <template #append>
                            <VBtn
                                v-tooltip="$t('Logout')"
                                color="default"
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
