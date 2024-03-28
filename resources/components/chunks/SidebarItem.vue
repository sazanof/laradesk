<template>
    <div
        class="sidebar"
        :class="{collapsed: collapsed === 'true'}">
        <div
            class="app-logo"
            @click="$router.push({name: 'index'})">
            <img
                :src="appLogo"
                class="logo-image"
                :class="{collapsed: collapsed === 'true'}">
            <div
                v-if="collapsed !== 'true'"
                class="logo-text">
                {{ appName }}
            </div>
        </div>
        <div class="app-menu">
            <MainMenu />
        </div>
    </div>
</template>

<script>
import MainMenu from './MainMenu.vue'

export default {
    name: 'SidebarItem',
    components: {
        MainMenu
    },
    data() {
        return {
            isCollapsed: false
        }
    },
    computed: {
        appName() {
            return this.$store.state.appName
        },
        appLogo() {
            return this.$store.state.appLogo
        },
        collapsed() {
            return this.$store.state.collapsed
        }
    },
    created() {

    }
}
</script>

<style lang="scss" scoped>
.sidebar {
    transition: width var(--transition-duration);
    overflow: hidden;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 0 14px;
    width: var(--sidebar-width);
    z-index: 2;
    background: var(--bs-purple-opacity);
    color: var(--bs-white);

    .app-logo {
        cursor: pointer;
        color: var(--background-white);
        font-size: 32px;
        font-weight: bold;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 6px 0 22px 0;

        .logo-text {
            font-size: 18px;
            margin-top: 16px;
        }

        .logo-image {
            display: block;
            margin-top: 6px;
            width: 80px;
            height: 80px;
            transition: var(--transition-duration);

            &:hover {
                opacity: 0.7;
            }
        }
    }

    &.collapsed {
        width: var(--collapsed-width);

        .app-logo {
            width: 100%;
            text-align: center;

            .logo-image {
                width: 48px;
                height: 48px;
            }
        }
    }
}
</style>
