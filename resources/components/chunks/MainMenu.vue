<template>
    <VSheet
        color="transparent">
        <VSheet
            :class="!collapsed ? 'pa-4' : 'pa-1'"
            color="transparent">
            <VBtn
                :text="$t('New ticket')"
                :prepend-icon="!collapsed ? 'mdi-plus' : null"
                :icon="collapsed ? 'mdi-plus' : null"
                color="deep-orange"
                rounded="lg"
                block
                variant="flat"
                :to="{name:'create_ticket'}"
                @click="navigateCreateTicket" />
        </VSheet>
        <VList
            color="white"
            class="pa-0">
            <VListSubheader
                v-if="!collapsed"
                :title="$t('Admin menu')" />
            <VListItem
                prepend-icon="mdi-view-dashboard"
                :title="$t('Dashboard')"
                to="/"
                @click="resetFilter" />
            <VListItem
                v-if="isAdmin"
                :title="$t('All tickets')"
                to="/admin/tickets"
                prepend-icon="mdi-folder-multiple"
                @click="resetFilter">
                <template #append>
                    <VBadge
                        v-if="counters!== null && counters.new > 0"
                        color="deep-orange"
                        class="pr-4"
                        :content="counters.new > 99 ? '99+' : counters.new" />
                </template>
            </VListItem>
            <VListItem
                v-if="isAdmin"
                :title="$t('Open tickets')"
                to="/admin/tickets/open"
                prepend-icon="mdi-list-box"
                @click="resetFilter" />
            <VListItem
                v-if="isAdmin"
                :title="$t('My tickets')"
                to="/admin/tickets/my"
                prepend-icon="mdi-star"
                @click="resetFilter">
                <template #append>
                    <VBadge
                        v-if="counters!== null && counters.my > 0"
                        color="deep-orange"
                        class="mr-4"
                        :content="counters.my > 99 ? '99+' : counters.my" />
                </template>
            </VListItem>
            <VListItem
                v-if="isAdmin"
                :title="$t('Statistics')"
                :to="{name:'statistics'}"
                prepend-icon="mdi-chart-pie"
                @click="resetFilter" />
            <VListSubheader
                v-if="isAdmin && !collapsed"
                :title="$t('User menu')" />
            <VListItem
                :title="$t('On approval')"
                to="/user/tickets/approval"
                prepend-icon="mdi-timer-alert"
                @click="resetFilter">
                <template #append>
                    <VBadge
                        v-if="counters!== null && counters.approval > 0"
                        color="deep-orange"
                        :content="counters.approval > 99 ? '99+' : counters.approval"
                        class="mr-4" />
                </template>
            </VListItem>
            <VListItem
                :title="$t('On observing')"
                to="/user/tickets/observer"
                prepend-icon="mdi-eye"
                @click="resetFilter">
                <template #append>
                    <VBadge
                        v-if="counters!== null && counters.observer > 0"
                        color="deep-orange"
                        class="mr-4"
                        :content="counters.observer > 99 ? '99+' : counters.observer" />
                </template>
            </VListItem>
            <VListItem
                v-if="isAdmin"
                :title="$t('Closed tickets')"
                to="/admin/tickets/closed"
                prepend-icon="mdi-folder-check"
                @click="resetFilter" />
            <VListItem
                :title="$t('Sent')"
                to="/user/tickets/sent"
                prepend-icon="mdi-send-check-outline"
                @click="resetFilter" />
        </VList>
    </VSheet>
</template>

<script>

export default {
    name: 'MainMenu',
    data() {
        return {
            isCollapsed: false
        }
    },
    computed: {
        counters() {
            return this.$store.state.counters
        },
        collapsed() {
            return this.$store.state.collapsed === 'true'
        },
        isAdmin() {
            return this.$store.getters['isAdmin']
        }
    },
    methods: {
        resetFilter() {
            this.emitter.emit('on-menu-click')
        },
        navigateCreateTicket() {
            this.$store.commit('clearCopyTicketData')
            this.emitter.emit('on-create-ticket-navigate')
        }
    }
}
</script>

<style lang="scss" scoped>
.bg {
    opacity: 0.6;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: calc(100% + 20px);
    height: calc(100% + 20px);
    transition: 0.5s;
    overflow: hidden;

}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100;
    opacity: 0.3;
}
</style>
