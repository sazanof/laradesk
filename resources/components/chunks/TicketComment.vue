<template>
    <div
        v-if="opened"
        class="ticket-comment">
        <textarea
            v-model="text"
            class="form-control" />
        <div class="button-send">
            <button
                :disabled="loading || disabled"
                class="btn btn-purple"
                @click="addComment">
                <Loading
                    v-if="loading"
                    :size="18" />
                <SendIcon
                    v-else
                    :size="18" />
                {{ commentText }}
            </button>
        </div>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import Loading from '../elements/Loading.vue'
import SendIcon from 'vue-material-design-icons/Send.vue'
import { COMMENT, STATUSES } from '../../js/consts.js'

const toast = useToast()

export default {
    name: 'TicketComment',
    components: {
        Loading,
        SendIcon
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
            type: null,
            opened: false,
            text: null,
            loading: false
        }
    },
    computed: {
        disabled() {
            return this.text === null || this.text === ''
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
    methods: {
        open(type) {
            this.text = null
            if (type !== this.type) {
                this.opened = true
            } else {
                this.opened = !this.opened
            }
            this.type = type

        },
        close() {
            this.text = ''
            this.opened = false
        },
        async addComment() {
            const data = {
                ticket_id: this.ticket.id,
                type: this.type,
                content: this.text
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
            } catch (e) {
                toast.error(this.$t('Error on adding a comment'))
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
    box-shadow: var(--bs-box-shadow);
    padding: var(--padding-box);
    border: 1px solid var(--bs-border-color);
    background: var(--bs-light);
    border-radius: var(--border-radius);
    position: absolute;
    width: 500px;
    bottom: 100px;
    z-index: 10;

    .button-send {
        margin-top: 16px;
        text-align: center;
    }

    textarea {
        min-height: 100px;
    }
}
</style>
