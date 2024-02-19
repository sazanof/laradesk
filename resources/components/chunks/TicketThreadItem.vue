<template>
    <div
        class="thread-item"
        :class="[{'my-thread-item': user.id === author.id},typeText]">
        <div class="author">
            <div class="pic">
                <Avatar
                    :size="56"
                    :user="author" />
            </div>
            <div
                class="comment">
                <div class="text">
                    {{ content }}
                </div>
                <div
                    v-if="comment?.files && comment.files.length > 0"
                    class="files">
                    <div
                        class="show-files"
                        @click="showFiles = !showFiles">
                        <PaperclipIcon :size="14" />
                        {{ !showFiles ? $t('Show {count} files', {count: comment.files.length}) : $t('Show less') }}
                    </div>
                    <div
                        v-if="showFiles"
                        class="files-list">
                        <TicketThreadFile
                            v-for="file in comment.files"
                            :key="file.id"
                            :file="file" />
                        <a
                            v-if="comment.files.length > 1"
                            :href="`/user/tickets/thread/${comment.id}/files`"
                            class="download-all">
                            <DownloadIcon :size="18" />
                            {{ $t('Download all files') }}
                        </a>
                    </div>
                </div>

                <div class="info">
                    <div class="status">
                        <ThumbUpOutlineIcon
                            v-if="comment.type === statuses.APPROVE_COMMENT"
                            :size="18" />
                        <ThumbDownOutlineIcon
                            v-if="comment.type === statuses.DECLINE_COMMENT"
                            :size="18" />
                        <CloseIcon
                            v-if="comment.type === statuses.CLOSE_COMMENT"
                            :size="18" />
                        <CheckIcon
                            v-if="comment.type === statuses.SOLVED_COMMENT"
                            :size="18" />
                        <RedoIcon
                            v-if="comment.type === statuses.REOPEN_COMMENT"
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
import TicketThreadFile from './TicketThreadFile.vue'
import ThumbDownOutlineIcon from 'vue-material-design-icons/ThumbDownOutline.vue'
import DownloadIcon from 'vue-material-design-icons/Download.vue'
import ThumbUpOutlineIcon from 'vue-material-design-icons/ThumbUpOutline.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'
import CheckIcon from 'vue-material-design-icons/Check.vue'
import RedoIcon from 'vue-material-design-icons/Redo.vue'
import ClockOutlineIcon from 'vue-material-design-icons/ClockOutline.vue'
import AccountCircleOutlineIcon from 'vue-material-design-icons/AccountCircleOutline.vue'
import Avatar from './Avatar.vue'
import PaperclipIcon from 'vue-material-design-icons/Paperclip.vue'

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
        Avatar,
        TicketThreadFile,
        DownloadIcon,
        PaperclipIcon

    },
    props: {
        comment: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            statuses: COMMENT,
            showFiles: false
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
        typeText() {
            switch (this.comment.type) {
                case COMMENT.CLOSE_COMMENT:
                    return 'comment-close'
                case COMMENT.DECLINE_COMMENT:
                    return 'comment-decline'
                case COMMENT.SOLVED_COMMENT:
                    return 'comment-solved'
                case COMMENT.APPROVE_COMMENT:
                    return 'comment-approve'
                case COMMENT.REOPEN_COMMENT:
                    return 'comment-reopen'
                default:
                    return 'comment-none'

            }
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
    width: 100%;
    max-width: 700px;
    position: relative;


    &::before {
        content: "";
        position: absolute;
        top: 2px;
        left: 2px;
        width: 6px;
        bottom: 2px;
        background: var(--bs-gray);
        border-radius: var(--border-radius);
    }

    &.comment-close::before {
        background: var(--ticket-color-closed);
    }

    &.comment-decline::before {
        background: var(--ticket-color-waiting);
    }

    &.comment-solved::before {
        background: var(--ticket-color-solved);
    }

    &.comment-approve::before {
        background: var(--ticket-color-approved);
    }

    &.comment-reopen::before {
        background: var(--ticket-color-my);
    }

    .status {
        opacity: 0.5;
    }

    .author {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
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

        &:before {
            left: auto;
            right: 2px;
        }

        .author {
            flex-direction: row-reverse;
            justify-content: space-between;
        }

        .comment {
            margin-left: 0;
            margin-right: 16px;
        }
    }

    .files {
        margin: 10px 0 10px 0;

        .show-files {
            color: var(--bs-purple);
            cursor: pointer;
            font-size: var(--font-small);
        }

        .download-all {
            display: block;
        }
    }
}
</style>
