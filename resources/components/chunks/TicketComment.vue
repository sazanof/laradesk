<template>
    <ModalDialog
        ref="commentModal"
        :title="title"
        size="big">
        <VTextarea
            v-model="text"
            prepend-inner-icon="mdi-text"
            :label="$t('Comment')" />
        <FileUploader
            ref="threadFiles"
            class="mt-4"
            @on-files-changed="files = $event" />
        <template #actions>
            <VBtn
                variant="flat"
                color="deep-purple"
                block
                prepend-icon="mdi-send"
                :loading="loading"
                :disabled="loading"
                :text="commentText"
                @click="addComment" />
        </template>
    </ModalDialog>
</template>

<script>
import { useToast } from 'vue-toastification'
import FileUploader from './FileUploader.vue'
import { COMMENT, STATUSES } from '../../js/consts.js'
import ModalDialog from '../chunks/ModalDialog.vue'
import { createErrorNotification } from '@/js/helpers/notificationHelper.js'


const toast = useToast()

export default {
    name: 'TicketComment',
    components: {
        FileUploader,
        ModalDialog
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
            title: '',
            type: null,
            text: null,
            loading: false,
            files: []
        }
    },
    computed: {
        disabled() {
            return this.text === null || this.text.length <= 3
        },

        commentText() {
            switch (this.type) {
                case COMMENT.CLOSE_COMMENT:
                    return this.$t('Comment and close')
                case COMMENT.DECLINE_COMMENT:
                    return this.$t('Decline')
                case COMMENT.SOLVED_COMMENT:
                    return this.$t('Add solution')
                case COMMENT.APPROVE_COMMENT:
                    return this.$t('Approve')
                case COMMENT.REOPEN_COMMENT:
                    return this.$t('Reopen')
                default:
                    return this.$t('Add comment')

            }
        }
    },
    watch: {
        type() {
            switch (this.type) {
                case COMMENT.CLOSE_COMMENT:
                    this.title = this.$t('Close ticket')
                    break
                case COMMENT.DECLINE_COMMENT:
                    this.title = this.$t('Add refuse')
                    break
                case COMMENT.SOLVED_COMMENT:
                    this.title = this.$t('Add solution')
                    break
                case COMMENT.APPROVE_COMMENT:
                    this.title = this.$t('Add approval')
                    break
                case COMMENT.REOPEN_COMMENT:
                    this.title = this.$t('Reopen ticket')
                    break
                default:
                    this.title = this.$t('Add comment')
                    break
            }
        }

    },
    methods: {
        open(type) {
            this.text = null
            this.$refs.commentModal.open()
            this.type = type

        },
        close() {
            this.text = ''
            this.$refs.commentModal.close()
        },
        async addComment() {
            const data = {
                ticket_id: this.ticket.id,
                type: this.type,
                content: this.text,
                files: this.files
            }
            let res
            let status = this.ticket.status
            this.loading = true
            try {
                switch (this.type) {
                    case COMMENT.CLOSE_COMMENT:
                        res = await this.$store.dispatch('addCloseComment', data)
                        status = STATUSES.CLOSED
                        break
                    case COMMENT.SOLVED_COMMENT:
                        res = await this.$store.dispatch('addSolutionComment', data)
                        status = STATUSES.SOLVED
                        break
                    case COMMENT.DECLINE_COMMENT:
                        res = await this.$store.dispatch('addDeclineComment', data)
                        this.$store.commit('updateApprovalStatus', 0)
                        break
                    case COMMENT.APPROVE_COMMENT:
                        res = await this.$store.dispatch('addApproveComment', data)
                        this.$store.commit('updateApprovalStatus', 1)
                        break
                    case COMMENT.REOPEN_COMMENT:
                        res = await this.$store.dispatch('addReopenComment', data)
                        status = STATUSES.IN_WORK
                        break
                    case COMMENT.COMMENT:
                        res = await this.$store.dispatch('addComment', data)
                        break
                }
                this.$store.commit('updateTicket', {
                    status
                })
                this.close()
                this.$refs.threadFiles.reset()
                this.files = []
            } catch (e) {
                this.$store.commit('addNotification', createErrorNotification(this.$t('Error on adding a comment')))
            } finally {
                this.loading = false

                this.$emit('on-comment-add')
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket-comment {
    .button-send {
        margin-top: 16px;
        text-align: center;
    }

    textarea {
        min-height: 150px;
    }
}
</style>
