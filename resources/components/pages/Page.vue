<template>
    <FinishProfileSettingsModal
        v-if="emptyData" />
    <VMain
        v-else>
        <SidebarItem />
        <VSheet
            class="fill-height"
            :color="$vuetify.theme.name === 'light'?'grey-lighten-4':'default'">
            <VSheet
                class="fill-height">
                <ContentItem />
            </VSheet>
        </VSheet>
        <HeaderItem />

        <Teleport to="body">
            <UserNews />
            <LightboxImage />
        </Teleport>
    </VMain>
</template>

<script>
import FinishProfileSettingsModal from '../chunks/FinishProfileSettingsModal.vue'
import HeaderItem from '../chunks/HeaderItem.vue'
import SidebarItem from '../chunks/SidebarItem.vue'
import ContentItem from '../chunks/ContentItem.vue'
import UserNews from '../chunks/UserNews.vue'
import LightboxImage from '../chunks/LightboxImage.vue'

export default {
    name: 'Page',
    components: {
        LightboxImage,
        HeaderItem,
        SidebarItem,
        ContentItem,
        FinishProfileSettingsModal,
        UserNews
    },

    computed: {
        user() {
            return this.$store.getters['getUser']
        },
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
