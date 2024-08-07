<template>
    <div class="ticket-actions">
        <div class="main-actions">
            <div class="actions-header">
                {{ $t('Actions') }}
            </div>
            <button
                class="btn btn-light text-primary"
                @click="getTicket">
                <Loading
                    v-if="loading"
                    :size="20" />
                <RefreshIcon
                    v-else
                    :size="20" />
                {{ $t('Refresh') }}
            </button>
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
                v-if="(isAdmin && isUserBelongsToDepartment) && notClosed"
                class="btn btn-light text-success"
                @click="$refs.comment.open(types.SOLVED_COMMENT)">
                <CommentCheckOutlineIcon :size="18" />
                {{ $t('Solution') }}
            </button>
            <button
                v-if="(isAdmin && isUserBelongsToDepartment) && notClosed"
                class="btn btn-light text-danger"
                @click="$refs.comment.open(types.CLOSE_COMMENT)">
                <CommentRemoveOutlineIcon :size="18" />
                {{ $t('Close') }}
            </button>
            <button
                v-if="(isAdmin && isUserBelongsToDepartment) && !notClosed"
                class="btn btn-light text-danger"
                @click="$refs.comment.open(types.REOPEN_COMMENT)">
                <ReplyIcon :size="18" />
                {{ $t('Reopen') }}
            </button>
        </div>
        <div
            class="additional-actions">
            <VDropdown
                :auto-hide="true"
                placement="auto">
                <template #popper>
                    <div class="other-actions">
                        <div class="title">
                            {{ $t('Other actions') }}
                        </div>
                        <div
                            v-if="user.id === ticket.user_id"
                            class="item"
                            @click="copyTicket">
                            <ContentCopyIcon :size="18" />
                            {{ $t('Clone') }}
                        </div>
                        <div
                            class="item"
                            @click="exportPdf">
                            <FilePdfBoxIcon :size="18" />
                            {{ $t('Save as PDF') }}
                        </div>
                        <div
                            class="item"
                            @click="print">
                            <PrinterIcon :size="18" />
                            {{ $t('Print') }}
                        </div>
                        <div
                            v-if="isAdmin"
                            class="item text-danger"
                            @click="deleteTicket">
                            <TrashCanIcon :size="18" />
                            {{ $t('Delete') }}
                        </div>
                    </div>
                </template>
                <button class="btn btn-light">
                    <DotsVerticalIcon :size="18" />
                    {{ $t('Other actions') }}
                </button>
            </VDropdown>
        </div>
        <TicketComment
            ref="comment"
            :ticket="ticket"
            @on-comment-add="$emit('on-comment-add')" />

        <ConfirmDialog ref="dialog" />
    </div>
</template>

<script>
import { COMMENT, STATUSES } from '../../js/consts.js'

import TicketComment from './TicketComment.vue'
import ReplyIcon from 'vue-material-design-icons/Reply.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'
import ThumbUpOutlineIcon from 'vue-material-design-icons/ThumbUpOutline.vue'
import ThumbDownOutlineIcon from 'vue-material-design-icons/ThumbDownOutline.vue'
import CommentOutlineIcon from 'vue-material-design-icons/CommentOutline.vue'
import DotsVerticalIcon from 'vue-material-design-icons/DotsVertical.vue'
import CommentCheckOutlineIcon from 'vue-material-design-icons/CommentCheckOutline.vue'
import CommentRemoveOutlineIcon from 'vue-material-design-icons/CommentRemoveOutline.vue'
import ContentCopyIcon from 'vue-material-design-icons/ContentCopy.vue'
import FilePdfBoxIcon from 'vue-material-design-icons/FilePdfBox.vue'
import PrinterIcon from 'vue-material-design-icons/Printer.vue'
import ConfirmDialog from '../elements/ConfirmDialog.vue'
import RefreshIcon from 'vue-material-design-icons/Refresh.vue'
import Loading from '../elements/Loading.vue'

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
        FilePdfBoxIcon,
        ConfirmDialog,
        RefreshIcon,
        PrinterIcon,
        ContentCopyIcon,
        Loading
    },
    props: {
        ticket: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-comment-add' ],
    data() {
        return {
            loading: false,
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
        isUserBelongsToDepartment() {
            return this.$store.getters.userBelongsToDepartment(this.ticket.department_id)
        },
        iAmApproval() {
            return this.$store.getters['iAmApproval']
        },
        iAmObserver() {
            return this.$store.getters['iAmObserver']
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
    },
    methods: {
        copyTicket() {
            this.$store.commit('setCopyTicketData', this.ticket)
            this.$router.push({
                name: 'create_ticket'
            })
        },
        async deleteTicket() {
            const ok = await this.$refs.dialog.show({
                title: this.$i18n.t('Delete ticket'),
                message: this.$i18n.t('Are you sure you want to delete this ticket?'),
                okButton: this.$i18n.t('Delete')
            })
            if (ok) {
                await this.$store.dispatch('deleteTicket', this.ticket.id)
                this.$router.back(-1)
            }

        },
        async exportPdf() {
            window.open(
                `/user/tickets/${this.ticket.id}/export/pdf`,
                '_blank' // <- This is what makes it open in a new window.
            )
        },
        print() {
            window.print()
        },
        async getTicket() {
            this.loading = true
            if (this.isUserBelongsToDepartment) {
                await this.$store.dispatch('getTicket', this.ticket.id)
            } else if (this.user.id === this.ticket.user_id || this.iAmApproval || this.iAmObserver) {
                await this.$store.dispatch('getUserTicket', this.ticket.id)
            }
            await this.$store.dispatch('getThread', this.ticket.id)
            this.loading = false
        }
    }
}
</script>

<style scoped lang="scss">
.ticket-actions {
    position: sticky;
    height: var(--ticket-actions-heoght);
    top: 0;
    width: 100%;
    padding: 6px;
    background: var(--bs-light);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
    z-index: 10;

    .actions-header {
        font-weight: bold;
        position: absolute;
        top: 12px;
        left: 10px;
        z-index: 2;
        color: var(--bs-gray)
    }

    .btn {
        margin: 0 4px;
    }
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

@media print {
    .ticket-actions {
        display: none !important;
    }
}
</style>
