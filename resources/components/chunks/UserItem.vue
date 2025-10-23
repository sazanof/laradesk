<template>
    <VSheet
        v-if="user"
        class="position-relative d-flex">
        <Avatar
            class="mr-2 mt-1"
            :user="user"
            :size="size" />

        <div>
            <div class="text-subtitle-1 font-weight-bold">
                {{ user.firstname }} {{ user.lastname }}
            </div>

            <div
                v-if="showInfo"
                class="position text-sm-caption opacity-70">
                {{ user.position }}, {{ user.department }}
            </div>
            <div
                v-if="showInfo && showEmail">
                <VChip
                    rounded="pill"
                    size="x-small"
                    density="comfortable"
                    prepend-icon="mdi-email"
                    target="_blank"
                    :href="`mailto:${user.email}`"
                    :text="user.email" />
            </div>
        </div>
        <VMenu>
            <template #activator="{props}">
                <VBtn
                    color="default"
                    variant="text"
                    size="small"
                    density="comfortable"
                    v-bind="props"
                    icon="mdi-dots-horizontal" />
            </template>
            <VList density="compact">
                <VListItem
                    prepend-icon="mdi-card-account-details"
                    :title="$t('Contact card')"
                    @click="showMore=true" />
                <slot name="actions" />
            </VList>
        </VMenu>
        <ModalDialog
            ref="userModal"
            v-model="showMore"
            :title="$t('User card')">
            <ContactCard :user="user" />
        </ModalDialog>
    </VSheet>
</template>

<script>
import ContactCard from './ContactCard.vue'
import ModalDialog from './ModalDialog.vue'
import Avatar from './Avatar.vue'

export default {
    name: 'UserItem',
    components: {
        Avatar,
        ContactCard,
        ModalDialog
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
        showEmail: {
            type: Boolean,
            default: false
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
    methods: {}
}
</script>

<style
    lang="scss"
    scoped>
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
