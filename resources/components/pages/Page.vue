<template>
    <FinishProfileSettingsModal
        v-if="emptyData"
        :user="user" />
    <div
        v-else
        class="page"
        :class="{collapsed: collapsed === 'true'}">
        <SidebarItem />
        <div class="main-content">
            <HeaderItem :user="user" />
            <ContentItem />
        </div>
        <Teleport to="body">
            <UserNews />
        </Teleport>
    </div>
</template>

<script>
import FinishProfileSettingsModal from '../chunks/FinishProfileSettingsModal.vue'
import HeaderItem from '../chunks/HeaderItem.vue'
import SidebarItem from '../chunks/SidebarItem.vue'
import ContentItem from '../chunks/ContentItem.vue'
import UserNews from '../chunks/UserNews.vue'

export default {
    name: 'Page',
    components: {
        HeaderItem,
        SidebarItem,
        ContentItem,
        FinishProfileSettingsModal,
        UserNews
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
        },
        emptyData() {
            return this.user.room_id === 0 || this.user.office_id === 0 || this.user.room_id === null || this.user.office_id === null
        }
    },
    async created() {
        await this.getUserNotifications()
        await this.$store.dispatch('getOffices')
        await this.getUserNews()
    },
    methods: {
        async getUserNotifications() {
            await this.$store.dispatch('getUserLastNotifications')
        },
        async getUserNews() {
            return this.$store.dispatch('getUserNews')
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
        overflow: hidden;
    }

    &.collapsed {
        .main-content {
            left: var(--collapsed-width)
        }
    }
}

@media print {
    .page {
        .main-content {
            box-shadow: none;
            position: static;
        }
    }
}
</style>
