<template>
    <FinishProfileSettingsModal
        v-if="emptyData"
        :user="user" />
    <VLayout
        v-else
        full-height
        class="page">
        <SidebarItem />
        <VMain class="main-content">
            <VSheet class="pa-4">
                <ContentItem />
            </VSheet>
        </VMain>
        <HeaderItem :user="user" />

        <Teleport to="body">
            <UserNews />
        </Teleport>
    </VLayout>
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

@media print {
    .page {
        .main-content {
            box-shadow: none;
            position: static;
        }
    }
}
</style>
