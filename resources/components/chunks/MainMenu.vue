<template>
    <div class="menu-wrapper">
        <router-link
            :class="{collapsed: collapsed === 'true'}"
            class="btn btn-orange w-100"
            :title="$t('New ticket')"
            to="/tickets/create">
            <div class="create-inner">
                <PlusIcon :size="18" />
                <span class="title">{{ $t('New ticket') }}</span>
            </div>
        </router-link>
        <div
            class="menu"
            :class="{collapsed: collapsed === 'true'}">
            <div
                v-if="isAdmin && collapsed !== 'true'"
                class="separator">
                {{ $t('Admin menu') }}
            </div>
            <router-link
                :title="$t('Dashboard')"
                to="/">
                <ViewDashboardIcon :size="18" />
                <span class="title">{{ $t('Dashboard') }}</span>
            </router-link>
            <router-link
                v-if="isAdmin"
                :title="$t('All tickets')"
                to="/admin/tickets">
                <FolderMultipleIcon :size="18" />
                <span class="title">{{ $t('All tickets') }}</span>
                <span
                    v-if="counters!== null && counters.new > 0"
                    class="badge rounded-pill">{{ counters.new > 99 ? '99+' : counters.new }}</span>
            </router-link>
            <router-link
                v-if="isAdmin"
                :title="$t('Open tickets')"
                to="/admin/tickets/open">
                <ListBoxIcon :size="18" />
                <span class="title">{{ $t('Open tickets') }}</span>
            </router-link>
            <router-link
                v-if="isAdmin"
                :title="$t('My tickets')"
                to="/admin/tickets/my">
                <StarIcon :size="18" />
                <span class="title">{{ $t('My tickets') }}</span>
                <span
                    v-if="counters!== null && counters.my > 0"
                    class="badge rounded-pill">{{ counters.my > 99 ? '99+' : counters.my }}</span>
            </router-link>
            <router-link
                v-if="isAdmin"
                :title="$t('Statistics')"
                :to="{name:'statistics'}">
                <ChartPieIcon :size="18" />
                <span class="title">{{ $t('Statistics') }}</span>
            </router-link>
            <div
                v-if="isAdmin && collapsed !== 'true'"
                class="separator">
                {{ $t('User menu') }}
            </div>
            <router-link
                :title="$t('On approval')"
                to="/user/tickets/approval">
                <TimerAlertIcon :size="18" />
                <span class="title">{{ $t('On approval') }}</span>
                <span
                    v-if="counters!== null && counters.approval > 0"
                    class="badge rounded-pill">{{ counters.approval > 99 ? '99+' : counters.approval }}</span>
            </router-link>
            <router-link
                :title="$t('On observing')"
                to="/user/tickets/observer">
                <EyeIcon :size="18" />
                <span class="title">{{ $t('On observing') }}</span>
                <span
                    v-if="counters!== null && counters.observer > 0"
                    class="badge rounded-pill">{{ counters.observer > 99 ? '99+' : counters.observer }}</span>
            </router-link>
            <router-link
                v-if="isAdmin"
                :title="$t('Closed tickets')"
                to="/admin/tickets/closed">
                <FolderCheckIcon :size="18" />
                <span class="title">{{ $t('Closed tickets') }}</span>
            </router-link>
            <router-link
                :title="$t('Sent')"
                to="/user/tickets/sent">
                <SendCheckOutlineIcon :size="18" />
                <span class="title">{{ $t('Sent') }}</span>
            </router-link>
        </div>
    </div>
</template>

<script>
import ChartPieIcon from 'vue-material-design-icons/ChartPie.vue'
import EyeIcon from 'vue-material-design-icons/Eye.vue'
import SendCheckOutlineIcon from 'vue-material-design-icons/SendCheckOutline.vue'
import FolderMultipleIcon from 'vue-material-design-icons/FolderMultiple.vue'
import FolderCheckIcon from 'vue-material-design-icons/FolderCheck.vue'
import TimerAlertIcon from 'vue-material-design-icons/TimerAlert.vue'
import StarIcon from 'vue-material-design-icons/Star.vue'
import ListBoxIcon from 'vue-material-design-icons/ListBox.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import ViewDashboardIcon from 'vue-material-design-icons/ViewDashboard.vue'

export default {
    name: 'MainMenu',
    components: {
        ChartPieIcon,
        SendCheckOutlineIcon,
        FolderMultipleIcon,
        FolderCheckIcon,
        StarIcon,
        TimerAlertIcon,
        ListBoxIcon,
        EyeIcon,
        PlusIcon,
        ViewDashboardIcon
    },
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
            return this.$store.state.collapsed
        },
        isAdmin() {
            return this.$store.getters['isAdmin']
        }
    }
}
</script>

<style lang="scss" scoped>
.menu-wrapper {
    width: calc(var(--sidebar-width) - 34px);

    .btn {
        border-color: var(--bs-orange);
        transition: width var(--transition-duration);
        overflow: hidden;

        .create-inner {
            width: 140px;
            margin: 0 auto;
        }

        &:active {
            background-color: var(--bs-purple-hover);
            border-color: var(--bs-purple-darker-hover);
        }

        &.collapsed {
            width: 44px !important;

            .create-inner {
                text-align: left;
            }

            .title {
                display: none;
            }
        }

    }

    .menu {
        margin-top: 16px;
        position: relative;
        overflow: hidden;

        .separator {
            font-weight: bold;
            opacity: 0.5;
            margin-top: 26px;
            margin-left: 14px;
            padding: 8px 4px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
        }

        a {
            position: relative;
            color: var(--bs-white);
            text-decoration: none;
            display: block;
            padding: 4px 0;

            &:not(:last-child) {
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            &.router-link-exact-active {
                font-weight: bold;
            }

            .material-design-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                position: relative;
                top: 2px;
                height: 44px;
                width: 44px;
            }

            .title {
                opacity: 1;
                transition: opacity var(--transition-duration);
            }

            .badge {
                position: absolute;
                right: 6px;
                top: 18px;
                background: var(--bs-orange);
            }
        }

        &.collapsed {
            a {
                &:not(:last-child) {
                    border-bottom-color: transparent;
                }

                .title {
                    opacity: 0;
                    display: none;
                }

                .badge {
                    position: absolute;
                    right: auto;
                    left: 24px;
                    top: 4px;
                    background: var(--bs-orange);
                }

                .material-design-icon {
                    transition: background-color var(--transition-duration);
                    border-radius: var(--border-radius);

                    &:hover {
                        background: var(--bs-purple);
                    }
                }

                &.router-link-exact-active .material-design-icon {
                    background: var(--bs-purple);
                }
            }
        }
    }
}

</style>
