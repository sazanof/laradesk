<template>
    <VContainer max-width="1200">
        <VRow>
            <VCol
                cols="12">
                <div
                    v-if="isAdmin"
                    class="text-h5 text-center">
                    {{ $t('Department information') }}
                </div>
                <VContainer
                    v-if="isAdmin">
                    <VRow>
                        <VCol
                            v-for="(counter, key) in dashboard.admin"
                            :key="key"
                            md="3"
                            cols="12">
                            <DashboardCard
                                :description="key"
                                :counter="counter"
                                @click="setAdditionalCriteria(key, true)">
                                <template #icon>
                                    <VIcon
                                        v-if="key === 'all'"
                                        icon="mdi-folder-multiple"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'waiting'"
                                        icon="mdi-timer-sand-complete"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'new'"
                                        icon="mdi-new-box"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'in-approval'"
                                        icon="mdi-timer-alert-outline"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'in-work'"
                                        icon="mdi-folder-clock"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'closed'"
                                        icon="mdi-folder-remove"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'solved'"
                                        icon="mdi-folder-check"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'my'"
                                        icon="mdi-folder-account"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'i-am-approval'"
                                        icon="mdi-account-clock"
                                        :size="64" />
                                </template>
                            </DashboardCard>
                        </VCol>
                    </VRow>
                </VContainer>
            </VCol>
            <VCol
                cols="12">
                <div class="text-h5 text-center">
                    {{ $t('Sent tickets') }}
                </div>
                <VContainer>
                    <VRow>
                        <VCol
                            v-for="(counter, key) in dashboard.user"
                            :key="key"
                            md="3"
                            cols="12">
                            <DashboardCard
                                :description="key"
                                :counter="counter"
                                @click="setAdditionalCriteria(key)">
                                <template #icon>
                                    <VIcon
                                        v-if="key === 'in-observing'"
                                        icon="mdi-eye"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'all'"
                                        icon="mdi-folder-multiple"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'waiting'"
                                        icon="mdi-timer-sand-complete"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'new'"
                                        icon="mdi-new-box"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'in-approval'"
                                        icon="mdi-timer-alert-outline"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'in-work'"
                                        icon="mdi-folder-clock"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'closed'"
                                        icon="mdi-folder-remove"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'solved'"
                                        icon="mdi-folder-check"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'my'"
                                        icon="mdi-folder-account"
                                        :size="64" />
                                    <VIcon
                                        v-if="key === 'i-am-approval'"
                                        icon="mdi-account-clock"
                                        :size="64" />
                                </template>
                            </DashboardCard>
                        </VCol>
                    </VRow>
                </VContainer>
            </VCol>
        </VRow>
    </VContainer>
</template>

<script>
import DashboardCard from '../chunks/DashboardCard.vue'

export default {
    name: 'Dashboard',
    components: {
        DashboardCard
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
