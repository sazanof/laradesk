<template>
    <div
        class="page"
        :class="{collapsed: collapsed === 'true'}">
        <SidebarItem />
        <div class="main-content">
            <HeaderItem :user="user" />
            <ContentItem />
        </div>
    </div>
</template>

<script>
import HeaderItem from '../chunks/HeaderItem.vue'
import SidebarItem from '../chunks/SidebarItem.vue'
import ContentItem from '../chunks/ContentItem.vue'

export default {
    name: 'Page',
    components: {
        HeaderItem,
        SidebarItem,
        ContentItem
    },
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    computed: {
        authenticated() {
            return this.$store.getters['isAuthenticated']
        },
        collapsed() {
            return this.$store.state.collapsed
        }
    }
}
</script>

<style lang="scss" scoped>
.page {
    .main-content {
        transition: left var(--transition-duration);
        position: absolute;
        left: var(--sidebar-width);
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 5;
        background: var(--background-white);
        box-shadow: var(--bs-box-shadow);
    }

    &.collapsed {
        .main-content {
            left: var(--collapsed-width)
        }
    }
}
</style>
