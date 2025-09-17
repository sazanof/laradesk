<template>
    <VListItem
        v-if="user"
        density="compact"
        :class="{'centered': !showInfo}"
        @click="showUserModal">
        <template #prepend>
            <Avatar
                :user="user"
                :size="size" />
        </template>
        <VListItemTitle>
            <div
                v-if="showName">
                {{ user.firstname }} {{ user.lastname }}
            </div>
            <slot name="actions" />
        </VListItemTitle>
        <VListItemSubtitle>
            <div
                v-if="showInfo"
                class="position">
                {{ user.position }}, {{ user.department }}
            </div>
            <div
                v-if="showInfo">
                <VChip
                    size="small"
                    density="comfortable"
                    prepend-icon="mdi-email"
                    target="_blank"
                    :href="`mailto:${user.email}`"
                    :text="user.email" />
            </div>
        </VListItemSubtitle>
        <Modal
            v-if="showMore"
            ref="userModal"
            :title="$t('User card')">
            <div class="user-modal">
                <div class="pic">
                    <Avatar
                        :user="user"
                        :size="64" />
                </div>
                <div class="info">
                    <div
                        class="name">
                        {{ user.firstname }} {{ user.lastname }}
                    </div>
                    <div
                        class="company">
                        {{ user.organization }}
                    </div>
                    <div
                        class="position">
                        {{ user.position }}, {{ user.department }}
                    </div>
                    <div class="email icon">
                        <EmailIcon :size="14" />
                        <a
                            :href="`mailto:${user.email}`">
                            {{ user.email }}
                        </a>
                    </div>

                    <div class="phone icon">
                        <PhoneIcon :size="14" />
                        {{ user.phone }}
                    </div>
                </div>
            </div>
        </Modal>
    </VListItem>
</template>

<script>
import PhoneIcon from 'vue-material-design-icons/Phone.vue'
import EmailIcon from 'vue-material-design-icons/Email.vue'
import Modal from '../elements/Modal.vue'
import Avatar from './Avatar.vue'

export default {
    name: 'UserInTicketList',
    components: {
        Avatar,
        Modal,
        EmailIcon,
        PhoneIcon
    },
    props: {
        title: {
            type: String,
            default: null
        },
        user: {
            type: Object,
            required: true
        },
        showName: {
            type: Boolean,
            default: true
        },
        showInfo: {
            type: Boolean,
            default: true
        },
        size: {
            type: Number,
            default: 24
        }
    },
    data() {
        return {
            showMore: false
        }
    },
    methods: {
        showUserModal() {
            this.showMore = true
            this.$nextTick(() => {
                this.$refs.userModal.open()
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.user {
    margin-bottom: 6px;
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    transition: var(--transition-duration);

    &.centered {
        align-items: center;
    }

    &:hover {
        opacity: 0.8;

        .name {
            color: var(--bs-purple)
        }
    }

    &:last-child {
        margin-bottom: 0;
    }

    .pic {
        margin-right: 4px;
    }

    .info {
        .name {
            font-weight: bold;
        }

        .position {
            font-size: var(--font-small);
        }

        .email {
            font-size: var(--font-small);
            color: var(--bs-primary)
        }
    }
}

.user-modal {
    display: flex;
    align-items: flex-start;

    .info {
        margin-left: 10px;

        .icon {
            .material-design-icon {
                margin-right: 4px;
            }
        }


        .name {
            font-weight: bold;
            font-size: 18px;
        }
    }


}
</style>
