<template>
    <div class="ticket-actions">
        <div class="main-actions">
            <button
                v-if="iAmApproval && notClosed"
                class="btn btn-light text-primary"
                @click="$refs.comment.open(types.APPROVE_COMMENT)">
                <ThumbUpOutlineIcon :size="18" />
                {{ $t('Approve') }}
            </button>
            <button
                v-if="iAmApproval && notClosed"
                class="btn btn-light text-secondary"
                @click="$refs.comment.open(types.DECLINE_COMMENT)">
                <ThumbDownOutlineIcon :size="18" />
                {{ $t('Decline') }}
            </button>
            <button
                v-if="notClosed"
                class="btn btn-light"
                @click="$refs.comment.open(types.COMMENT)">
                <CommentOutlineIcon :size="18" />
                {{ $t('Comment') }}
            </button>
            <button
                v-if="isAdmin && notClosed"
                class="btn btn-light text-success"
                @click="$refs.comment.open(types.SOLVED_COMMENT)">
                <CommentCheckOutlineIcon :size="18" />
                {{ $t('Solution') }}
            </button>
            <button
                v-if="isAdmin && notClosed"
                class="btn btn-light text-danger"
                @click="$refs.comment.open(types.CLOSE_COMMENT)">
                <CommentRemoveOutlineIcon :size="18" />
                {{ $t('Close') }}
            </button>
            <button
                v-if="isAdmin && !notClosed"
                class="btn btn-light text-danger"
                @click="$refs.comment.open(types.REOPEN_COMMENT)">
                <ReplyIcon :size="18" />
                {{ $t('Reopen') }}
            </button>
        </div>
        <div
            v-if="isAdmin"
            class="additional-actions">
            <Popper :arrow="true">
                <template #content>
                    <div class="other-actions">
                        <div class="title">
                            {{ $t('Other actions') }}
                        </div>
                        <div class="item">
                            <FilePdfBoxIcon :size="18" />
                            {{ $t('Save as PDF') }}
                        </div>
                        <div class="item text-danger">
                            <TrashCanIcon :size="18" />
                            {{ $t('Delete') }}
                        </div>
                    </div>
                </template>
                <button class="btn btn-light">
                    <DotsVerticalIcon :size="18" />
                    {{ $t('Other actions') }}
                </button>
            </Popper>
        </div>
        <TicketComment
            ref="comment"
            :ticket="ticket" />
    </div>
</template>

<script>
import { COMMENT, STATUSES } from '../../js/consts.js'
import Popper from 'vue3-popper'
import TicketComment from './TicketComment.vue'
import ReplyIcon from 'vue-material-design-icons/Reply.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'
import ThumbUpOutlineIcon from 'vue-material-design-icons/ThumbUpOutline.vue'
import ThumbDownOutlineIcon from 'vue-material-design-icons/ThumbDownOutline.vue'
import CommentOutlineIcon from 'vue-material-design-icons/CommentOutline.vue'
import DotsVerticalIcon from 'vue-material-design-icons/DotsVertical.vue'
import CommentCheckOutlineIcon from 'vue-material-design-icons/CommentCheckOutline.vue'
import CommentRemoveOutlineIcon from 'vue-material-design-icons/CommentRemoveOutline.vue'
import FilePdfBoxIcon from 'vue-material-design-icons/FilePdfBox.vue'

export default {
    name: 'TicketActions',
    components: {
        TicketComment,
        CommentOutlineIcon,
        ReplyIcon,
        CommentRemoveOutlineIcon,
        CommentCheckOutlineIcon,
        DotsVerticalIcon,
        ThumbUpOutlineIcon,
        ThumbDownOutlineIcon,
        TrashCanIcon,
        Popper,
        FilePdfBoxIcon
    },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            commentType: COMMENT.COMMENT // default comment type
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        isAdmin() {
            return this.user.is_admin
        },
        iAmApproval() {
            return this.$store.getters['iAmApproval']
        },
        isApproved() {
            if (this.iAmApproval !== null) {
                return this.iAmApproval.approved
            }
            return null
        },
        types() {
            return COMMENT
        },
        statuses() {
            return STATUSES
        },
        notClosed() {
            return this.ticket.status !== STATUSES.CLOSED && this.ticket.status !== STATUSES.SOLVED
        }
    }
}
</script>

<style scoped lang="scss">
.ticket-actions {
    position: absolute;
    bottom: 10px;
    width: 80%;
    margin: 0 10%;
    padding: 6px;
    border-radius: var(--border-radius);
    background: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    display: flex;
    align-items: center;
    justify-content: center;

    .btn {
        margin: 0 4px;
    }

    .other-actions {
        .title {
            background: var(--bs-light);
            border-bottom: 1px solid var(--bs-border-color);
            padding: 6px 10px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            font-weight: bold;
            text-align: center;
        }

        .item {
            padding: 6px 10px;
            cursor: pointer;
            transition: var(--transition-duration);

            &:hover {
                background: var(--bs-light);
            }
        }
    }
}
</style>
