<template>
    <div class="dashboard">
        <h2 v-if="isAdmin">
            {{ $t('Department information') }}
        </h2>
        <div
            v-if="isAdmin"
            class="d-section">
            <DashboardCard
                v-for="(counter, key) in dashboard.admin"
                :key="key"
                :description="key"
                :counter="counter"
                @click="setAdditionalCriteria(key, true)">
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
        <h2 class="mt-4">
            {{ $t('Sent tickets') }}
        </h2>
        <div
            class="d-section">
            <DashboardCard
                v-for="(counter, key) in dashboard.user"
                :key="key"
                :description="key"
                :counter="counter"
                @click="setAdditionalCriteria(key)">
                <template #icon>
                    <EyeIcon
                        v-if="key === 'in-observing'"
                        :size="64" />
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
import EyeIcon from 'vue-material-design-icons/Eye.vue'
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
        AccountClockIcon,
        EyeIcon
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        activeDepartment() {
            return this.$store.getters['getActiveDepartment']
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
    watch: {
        '$route.name': {
            handler: async function (name) {
                if (name === 'index') {
                    await this.updateDashboard()
                }
            },
            deep: true,
            immediate: true
        }
    },
    mounted() {
        this.emitter.on('after-department-changed', async () => {
            // alert('on-department-changed')
            await this.updateDashboard()
        })

        this.emitter.on('on-notification-received', async () => {
            await this.updateDashboard()
        })
    },
    unmounted() {
        this.emitter.off('on-notification-received')
        this.emitter.off('after-department-changed')
    },
    methods: {
        setAdditionalCriteria(key, admin = false) {
            if (key === 'in-observing') {
                this.$store.commit('setAdditionalCriteria', null)
                this.$nextTick(() => {
                    this.$router.push({ name: 'user_is_observer' })
                })

            } else if (key === 'i-am-approval') {
                this.$store.commit('setAdditionalCriteria', null)
                this.$nextTick(() => {
                    this.$router.push({ name: 'user_is_approval' })
                })

            } else {
                this.$store.commit('setAdditionalCriteria', key)
                this.$router.push({
                    name: admin === true ? 'admin_tickets_all' : 'user_tickets_sent',
                    params: { criteria: 'sent' }
                })
            }

        },
        async updateDashboard() {
            if (this.isAdmin) {
                await this.$store.dispatch('getAdminDashboard')
            }
            await this.$store.dispatch('getUserDashboard')
        }
    }

}
</script>

<style scoped lang="scss">


</style>
