<template>
    <div class="header">
        <div
            class="menu-toggle"
            @click="setCollapsed">
            <MenuIcon :size="32" />
        </div>
        <div class="user-dropdown">
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
                        class="list-group"
                        @click="showUserPopper = !showUserPopper">
                        <a class="list-group-item">
                            <AccountIcon :size="18" />
                            {{ $t('Profile') }}
                        </a>
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
import CogIcon from 'vue-material-design-icons/Cog.vue'
import MenuIcon from 'vue-material-design-icons/Menu.vue'
import AccountIcon from 'vue-material-design-icons/Account.vue'
import LogoutVariantIcon from 'vue-material-design-icons/LogoutVariant.vue'
import DropdownElement from '../ elements/DropdownElement.vue'
import Avatar from './Avatar.vue'

export default {
    name: 'HeaderItem',
    components: {
        AccountIcon,
        LogoutVariantIcon,
        MenuIcon,
        DropdownElement,
        CogIcon,
        Avatar
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
            isCollapsed: this.$store.state.collapsed
        }
    },
    methods: {
        setCollapsed() {
            this.isCollapsed = this.isCollapsed === 'false' ? 'true' : 'false'
            this.$store.dispatch('setCollapsed', this.isCollapsed)
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

    .user-dropdown {
        position: absolute;
        z-index: 2;
        right: 8px;
        top: 8px;
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
        min-width: 200px;

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

    .menu-toggle {
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
}
</style>
