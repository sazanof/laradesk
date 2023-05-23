<template>
    <div
        class="sidebar"
        :class="{collapsed: collapsed === 'true'}">
        <div class="app-logo">
            {{ collapsed === 'true' ? 'H' : appName }}
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
    background: var(--bs-purple-opecity);
    color: var(--bs-white);

    .app-logo {
        color: var(--background-white);
        font-size: 32px;
        margin: 20px 7px;
        font-weight: bold;
    }

    &.collapsed {
        width: var(--collapsed-width);

        .app-logo {
            width: 100%;
            text-align: center;
            margin: 20px 0;
        }
    }
}
</style>
