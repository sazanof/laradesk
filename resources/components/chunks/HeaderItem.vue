<template>
    <div
        class="header">
        <div
            class="menu-toggle"
            @click="setCollapsed">
            <MenuIcon :size="32" />
        </div>
        <div
            class="back"
            @click="$router.back(-1)">
            <ArrowLeftIcon :size="32" />
        </div>
        <div
            v-if="activeDepartment !== null && activeDepartment !== undefined"
            class="department-info">
            <AccountGroupIcon :size="24" />
            {{ activeDepartment.name }}
        </div>
        <div class="informational-block">
            <NotificationsWrapper />
            <div class="socket-connect">
                <Popper
                    placement="auto"
                    :arrow="true"
                    :hover="true">
                    <template #content>
                        <div class="connection-status">
                            {{ status }}
                        </div>
                    </template>

                    <span>
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
                </Popper>
            </div>
        </div>

        <div
            class="user-dropdown">
            <DropdownElement :show="showUserPopper">
                <template #trigger>
                    <div
                        class="avatar-trigger"
                        @click="showUserPopper = !showUserPopper">
                        <Avatar
                            :size="48"
                            :user="user" />
                    </div>
                </template>
                <div class="user-dropdown-inner">
                    <div class="d-username">
                        {{ user.firstname }} {{ user.lastname }}
                    </div>

                    <div
                        v-if="isAdmin && departments.length > 0"
                        class="departments-select">
                        <MultiselectElement
                            v-model="activeDepartmentData"
                            :can-clear="false"
                            :options="departments"
                            :object="true"
                            label="name"
                            value-prop="id"
                            track-by="id"
                            @select="changeDepartment" />
                    </div>

                    <div
                        class="list-group"
                        @click="showUserPopper = !showUserPopper">
                        <router-link
                            to="/profile"
                            class="list-group-item">
                            <AccountIcon :size="18" />
                            {{ $t('Profile') }}
                        </router-link>
                        <router-link
                            v-if="user.is_super_admin"
                            to="/admin/management"
                            class="list-group-item">
                            <CrownIcon :size="18" />
                            {{ $t('Administration') }}
                        </router-link>
                        <router-link
                            v-if="user.is_admin"
                            to="/admin/settings"
                            class="list-group-item">
                            <CogIcon :size="18" />
                            {{ $t('Settings') }}
                        </router-link>
                        <a
                            href="/logout"
                            class="list-group-item">
                            <LogoutVariantIcon :size="18" />
                            {{ $t('Logout') }}
                        </a>
                    </div>
                </div>
            </DropdownElement>
        </div>
    </div>
</template>

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
import DropdownElement from '../elements/DropdownElement.vue'
import Avatar from './Avatar.vue'
import NotificationsWrapper from '../elements/NotificationsWrapper.vue'
import Popper from 'vue3-popper'

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
        Avatar,
        LanDisconnectIcon,
        LanCheckIcon,
        LanPendingIcon,
        Popper,
        NotificationsWrapper,
        MultiselectElement,
        AccountGroupIcon
    },
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            showUserPopper: false,
            isCollapsed: this.$store.state.collapsed,
            activeDepartmentData: null
        }
    },
    computed: {
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
            return this.$store.getters['isAdmin']
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
        }
    }
}
</script>

<style lang="scss" scoped>
.header {
    height: var(--header-height);
    display: flex;
    align-items: center;
    width: 100%;
    border-bottom: 1px solid var(--bs-border-color);
    position: relative;

    .department-info {
        position: absolute;
        left: 50%;
        width: 340px;
        margin-left: -170px;
        border-radius: var(--border-radius);
        color: var(--bs-purple);
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;

        .material-design-icon {
            margin-right: 6px;
            position: relative;
            top: -2px
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

    .avatar-trigger {
        cursor: pointer;
        transition: var(--transition-duration);
        border-radius: var(--border-radius);

        &:hover {
            background: var(--color-hover-rgba-black);
        }
    }

    .user-dropdown-inner {
        min-width: 230px;

        .d-username {
            font-weight: bold;
            padding: 8px;
            text-align: center;
            background-color: var(--bs-light);
            position: relative;
            z-index: 2;
            border-radius: var(--border-radius);
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

    .departments-select {
        width: 100%;
        padding: 4px;
    }
}
</style>
