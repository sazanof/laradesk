<template>
    <div class="dashboard">
        <h1 v-if="isAdmin">
            {{ $t('Summary information') }}
        </h1>
        <div
            v-if="isAdmin"
            class="d-section">
            <DashboardCard
                v-for="(counter, key) in dashboard.admin"
                :key="key"
                :description="key"
                :counter="counter">
                <template #icon>
                    <FolderMultipleIcon
                        v-if="key === 'all'"
                        :size="64" />
                    <TimerSandCompleteIcon
                        v-if="key === 'waiting'"
                        :size="64" />
                    <NewBoxIcon
                        v-if="key === 'new'"
                        :size="64" />
                    <TimerAlertOutlineIcon
                        v-if="key === 'in-approval'"
                        :size="64" />
                    <FolderClockIcon
                        v-if="key === 'in-work'"
                        :size="64" />
                    <FolderRemoveIcon
                        v-if="key === 'closed'"
                        :size="64" />
                    <FolderCheckIcon
                        v-if="key === 'solved'"
                        :size="64" />
                    <FolderAccountIcon
                        v-if="key === 'my'"
                        :size="64" />
                    <AccountClockIcon
                        v-if="key === 'i-am-approval'"
                        :size="64" />
                </template>
            </DashboardCard>
        </div>
        <h1>{{ $t('Sent tickets') }}</h1>
        <div
            class="d-section">
            <DashboardCard
                v-for="(counter, key) in dashboard.user"
                :key="key"
                :description="key"
                :counter="counter">
                <template #icon>
                    <FolderMultipleIcon
                        v-if="key === 'all'"
                        :size="64" />
                    <TimerSandCompleteIcon
                        v-if="key === 'waiting'"
                        :size="64" />
                    <NewBoxIcon
                        v-if="key === 'new'"
                        :size="64" />
                    <TimerAlertOutlineIcon
                        v-if="key === 'in-approval'"
                        :size="64" />
                    <FolderClockIcon
                        v-if="key === 'in-work'"
                        :size="64" />
                    <FolderRemoveIcon
                        v-if="key === 'closed'"
                        :size="64" />
                    <FolderCheckIcon
                        v-if="key === 'solved'"
                        :size="64" />
                    <FolderAccountIcon
                        v-if="key === 'my'"
                        :size="64" />
                    <AccountClockIcon
                        v-if="key === 'i-am-approval'"
                        :size="64" />
                </template>
            </DashboardCard>
        </div>
    </div>
</template>

<script>
import AccountClockIcon from 'vue-material-design-icons/AccountClock.vue'
import FolderAccountIcon from 'vue-material-design-icons/FolderAccount.vue'
import FolderCheckIcon from 'vue-material-design-icons/FolderCheck.vue'
import FolderRemoveIcon from 'vue-material-design-icons/FolderRemove.vue'
import FolderClockIcon from 'vue-material-design-icons/FolderClock.vue'
import TimerAlertOutlineIcon from 'vue-material-design-icons/TimerAlertOutline.vue'
import NewBoxIcon from 'vue-material-design-icons/NewBox.vue'
import FolderMultipleIcon from 'vue-material-design-icons/FolderMultiple.vue'
import TimerSandCompleteIcon from 'vue-material-design-icons/TimerSandComplete.vue'
import DashboardCard from '../chunks/DashboardCard.vue'

export default {
    name: 'Dashboard',
    components: {
        DashboardCard,
        FolderMultipleIcon,
        TimerSandCompleteIcon,
        NewBoxIcon,
        TimerAlertOutlineIcon,
        FolderClockIcon,
        FolderRemoveIcon,
        FolderCheckIcon,
        FolderAccountIcon,
        AccountClockIcon
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        isAdmin() {
            return this.$store.getters['isAdmin']
        },
        isSuperAdmin() {
            return this.$store.getters['isSuperAdmin']
        },
        dashboard() {
            return this.$store.getters['getDashboardData']
        }
    },
    async mounted() {
        if (this.isAdmin) {
            await this.$store.dispatch('getAdminDashboard')
        }
        await this.$store.dispatch('getUserDashboard')
    }
}
</script>

<style scoped lang="scss">
.dashboard {
    padding: var(--padding-box);

    .d-section {
        display: flex;
        flex-wrap: wrap;
    }
}

</style>
