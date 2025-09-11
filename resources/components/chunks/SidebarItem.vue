<template>
    <VNavigationDrawer
        position="fixed"
        permanent
        width="300"
        :rail="collapsed"
        color="deep-purple">
        <template #image>
            <div
                v-if="appBg"
                class="overlay"
                :style="`background-image: url('${appBg}'); background-position:left; background-size:cover`" />
        </template>
        <template #prepend>
            <VSheet
                color="transparent"
                class="app-logo"
                :class="collapsed ? 'pa-1 mt-2':'pa-3'">
                <VImg
                    rounded="pill"
                    width="70"
                    :src="appLogo"
                    color="white"
                    class="ma-auto mb-3 border-lg cursor-pointer"
                    @click="$router.push({name: 'index'})" />
                <div
                    v-if="!collapsed"
                    class="text-h6 text-center">
                    {{ appName }}
                </div>
            </VSheet>
        </template>
        <template #default>
            <MainMenu />
        </template>
    </VNavigationDrawer>
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
        appBg() {
            return this.$store.getters['getAppBg']
        },
        collapsed() {
            return this.$store.state.collapsed === 'true'
        }
    }
}
</script>

<style lang="scss" scoped>
.overlay {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    opacity: 0.4;
    z-index: 0
}
</style>
