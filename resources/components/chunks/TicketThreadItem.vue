<template>
    <div
        class="thread-item"
        :class="{'my-thread-item': user.id === author.id}">
        <div class="author">
            <div class="pic">
                <Avatar
                    :size="56"
                    :user="author" />
            </div>
            <div
                class="comment"
                :class="type">
                <div class="text">
                    {{ content }}
                </div>
                <div class="info">
                    <div class="status">
                        <ThumbUpOutlineIcon
                            v-if="type === statuses.APPROVE_COMMENT"
                            :size="18" />
                        <ThumbDownOutlineIcon
                            v-if="type === statuses.DECLINE_COMMENT"
                            :size="18" />
                        <CloseIcon
                            v-if="type === statuses.CLOSE_COMMENT"
                            :size="18" />
                        <CheckIcon
                            v-if="type === statuses.SOLVED_COMMENT"
                            :size="18" />
                        <RedoIcon
                            v-if="type === statuses.REOPEN_COMMENT"
                            :size="18" />
                    </div>
                    <div class="date">
                        <ClockOutlineIcon :size="18" />
                        {{ createdAt }}
                    </div>
                    <div class="name">
                        <AccountCircleOutlineIcon :size="18" />
                        {{ author.firstname }} {{ author.lastname }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { COMMENT } from '../../js/consts.js'
import ThumbDownOutlineIcon from 'vue-material-design-icons/ThumbDownOutline.vue'
import ThumbUpOutlineIcon from 'vue-material-design-icons/ThumbUpOutline.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'
import CheckIcon from 'vue-material-design-icons/Check.vue'
import RedoIcon from 'vue-material-design-icons/Redo.vue'
import ClockOutlineIcon from 'vue-material-design-icons/ClockOutline.vue'
import AccountCircleOutlineIcon from 'vue-material-design-icons/AccountCircleOutline.vue'
import Avatar from './Avatar.vue'
import { formatDate } from '../../js/helpers/moment.js'

export default {
    name: 'TicketThreadItem',
    components: {
        ClockOutlineIcon,
        AccountCircleOutlineIcon,
        ThumbDownOutlineIcon,
        ThumbUpOutlineIcon,
        CloseIcon,
        CheckIcon,
        RedoIcon,
        Avatar
    },
    props: {
        comment: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            statuses: COMMENT
        }
    },
    computed: {
        createdAt() {
            return formatDate(this.comment.created_at)
        },
        user() {
            return this.$store.getters['getUser']
        },
        author() {
            return this.comment.user
        },
        content() {
            return this.comment.content
        },
        type() {
            return this.comment.type
        }
    }
}
</script>

<style lang="scss" scoped>
.thread-item {
    padding: var(--padding-box);
    border: 1px solid var(--bs-border-color);
    border-radius: var(--border-radius);
    margin-top: 16px;
    max-width: 700px;
    position: relative;

    .status {
        opacity: 0.5;
    }

    .author {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .comment {
        margin-left: 16px;

        .text {
            color: var(--bs-gray);
            margin-bottom: 10px;
        }

        .info {
            font-size: var(--font-medium);
            display: flex;
            align-items: center;
            justify-content: flex-start;

            & > div {
                display: flex;
                align-items: center;
                margin-right: 10px;

                .material-design-icon {
                    margin-right: 4px;
                    position: relative;
                    top: -1px
                }
            }
        }
    }

    &.my-thread-item {
        background: var(--bs-light);
        align-self: end;

        .author {
            flex-direction: row-reverse;
        }

        .comment {
            margin-left: 0;
            margin-right: 16px;
        }
    }
}
</style>
