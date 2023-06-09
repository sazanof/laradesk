<template>
    <div class="menu-wrapper">
        <router-link
            :class="{collapsed: collapsed === 'true'}"
            class="btn btn-primary w-100"
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
            <router-link
                v-if="isAdmin"
                :title="$t('All tickets')"
                to="/admin/tickets">
                <FolderMultipleIcon :size="18" />
                <span class="title">{{ $t('All tickets') }}</span>
                <span class="badge rounded-pill">14</span>
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
            </router-link>
            <router-link
                v-if="isAdmin"
                :title="$t('On approval')"
                to="/admin/tickets/approval">
                <TimerAlertIcon :size="18" />
                <span class="title">{{ $t('On approval') }}</span>
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
                to="/user/tickets">
                <SendCheckOutlineIcon :size="18" />
                <span class="title">{{ $t('Sent') }}</span>
            </router-link>
        </div>
    </div>
</template>

<script>
import SendCheckOutlineIcon from 'vue-material-design-icons/SendCheckOutline.vue'
import FolderMultipleIcon from 'vue-material-design-icons/FolderMultiple.vue'
import FolderCheckIcon from 'vue-material-design-icons/FolderCheck.vue'
import TimerAlertIcon from 'vue-material-design-icons/TimerAlert.vue'
import StarIcon from 'vue-material-design-icons/Star.vue'
import ListBoxIcon from 'vue-material-design-icons/ListBox.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'

export default {
    name: 'MainMenu',
    components: {
        SendCheckOutlineIcon,
        FolderMultipleIcon,
        FolderCheckIcon,
        StarIcon,
        TimerAlertIcon,
        ListBoxIcon,
        PlusIcon
    },
    data() {
        return {
            isCollapsed: false
        }
    },
    computed: {
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
        background-color: var(--bs-purple);
        border-color: var(--bs-purple-darker);
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
