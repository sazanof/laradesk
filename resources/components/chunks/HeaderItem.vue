<script>
import MultiselectElement from '../elements/MultiselectElement.vue'
import LanDisconnectIcon from 'vue-material-design-icons/LanDisconnect.vue'
import AccountGroupIcon from 'vue-material-design-icons/AccountGroup.vue'
import LanCheckIcon from 'vue-material-design-icons/LanCheck.vue'
import LanPendingIcon from 'vue-material-design-icons/LanPending.vue'
import ArrowLeftIcon from 'vue-material-design-icons/ArrowLeft.vue'
import CogIcon from 'vue-material-design-icons/Cog.vue'
import CrownIcon from 'vue-material-design-icons/Crown.vue'
import MenuIcon from 'vue-material-design-icons/Menu.vue'
import AccountIcon from 'vue-material-design-icons/Account.vue'
import LogoutVariantIcon from 'vue-material-design-icons/LogoutVariant.vue'
import ShieldIcon from 'vue-material-design-icons/Shield.vue'
import DropdownElement from '../elements/DropdownElement.vue'
import Avatar from './Avatar.vue'
import NotificationsWrapper from '../elements/NotificationsWrapper.vue'


export default {
    name: 'HeaderItem',
    components: {
        ArrowLeftIcon,
        AccountIcon,
        LogoutVariantIcon,
        MenuIcon,
        DropdownElement,
        CogIcon,
        CrownIcon,
        ShieldIcon,
        Avatar,
        LanDisconnectIcon,
        LanCheckIcon,
        LanPendingIcon,
        NotificationsWrapper,
        MultiselectElement,
        AccountGroupIcon
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
        elevation="1"
        class="header">
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
            <VSheet class="informational-block">
                <NotificationsWrapper />
                <div class="socket-connect">
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
                </div>
            </VSheet>

            <VMenu
                :close-on-content-click="false"
                width="300"
                hover>
                <template #activator="{props}">
                    <div
                        v-bind="props"
                        class="avatar-trigger position-relative">
                        <div
                            v-if="isSuperAdmin"
                            class="icon-super-admin">
                            <CrownIcon
                                :size="14"
                                fill-color="yellow" />
                        </div>

                        <div
                            v-if="isAdmin"
                            class="icon-admin">
                            <ShieldIcon
                                :size="14"
                                fill-color="orange" />
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
.header {
    height: var(--header-height);
    display: flex;
    align-items: center;
    width: 100%;
    border-bottom: 1px solid var(--bs-border-color);
    position: relative;

    .department-info {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        border-radius: var(--border-radius);
        color: var(--bs-purple);
        font-weight: bold;
        margin-right: 16px;

        .material-design-icon {
            margin-right: 6px;
            position: relative;
            top: -2px
        }

        .elipsis {
            display: block;
            width: 350px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
    }

    .informational-block {
        position: absolute;
        z-index: 2;
        right: 74px;
        top: 18px;
        display: flex;

        .socket-connect {
            margin-left: 6px;

            .lan-check-icon {
                color: var(--bs-green)
            }

            .lan-pending-icon {
                color: var(--bs-gray)
            }

            .lan-disconnect-icon {
                color: var(--bs-danger)
            }
        }
    }


    .user-dropdown {
        position: absolute;
        right: 8px;
        top: 8px;
        z-index: 1;
    }

    .connection-status {
        padding: 4px;
        font-size: var(--font-small);
        width: 140px;
    }

    .icon-admin {
        position: absolute;
        bottom: 6px;
        right: 0
    }

    .icon-super-admin {
        position: absolute;
        bottom: 6px;
        left: 0
    }

    .avatar-trigger {
        cursor: pointer;
        transition: var(--transition-duration);
        border-radius: var(--border-radius);

        &:hover {
            background: var(--color-hover-rgba-black);
        }
    }

    .menu-toggle, .back {
        position: absolute;
        border-radius: var(--bs-border-radius);
        top: 8px;
        left: 8px;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-duration);
        cursor: pointer;

        &:hover {
            color: #fff;
            background: var(--bs-purple);
        }
    }

    .back {
        left: 54px;
        color: var(--bs-gray-200);
    }


}

.user-dropdown-inner {
    min-width: 230px;

    background-color: var(--bs-light);

    .departments-select {
        width: 100%;
        padding: 4px;
    }

    .d-username {
        font-weight: bold;
        padding: 8px;
        text-align: center;
        position: relative;
        z-index: 2;
        border-radius: var(--border-radius);
    }
}

@media print {
    .header {
        display: none !important;
    }
}
</style>
