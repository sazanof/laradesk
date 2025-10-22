<template>
    <VSheet
        class="mb-4 thread-item d-flex flex-wrap"
        :class="[{'my-thread-item': user.id === author.id,'flex-row-reverse': user.id === author.id}]">
        <VSheet
            color="transparent"
            :class="user.id === author.id?'ml-2':'mr-2'"
            width="56">
            <Avatar
                :size="56"
                :user="author" />
        </VSheet>
        <VCard
            class="author"
            style="width: calc(100% - 64px)">
            <VCardText
                class="pa-3">
                <VChip
                    class="mb-2"
                    rounded="pill"
                    size="small"
                    density="comfortable"
                    variant="tonal"
                    :color="iconAndColor.color"
                    :text="iconAndColor.text"
                    :prepend-icon="iconAndColor.icon" />
                <VSheet class="opacity-70">
                    {{ content }}
                </VSheet>
                <div
                    v-if="comment?.files && comment.files.length > 0"
                    class="files">
                    <VChip
                        class="my-2"
                        prepend-icon="mdi-paperclip"
                        rounded="pill"
                        size="x-small"
                        @click="showFiles = !showFiles">
                        {{ !showFiles ? $t('Show {count} files', {count: comment.files.length}) : $t('Show less') }}
                    </VChip>
                    <ModalDialog
                        v-model="showFiles"
                        :title="$t('Comment files')"
                        class="files-list">
                        <VSheet>
                            <VList slim>
                                <TicketThreadFile
                                    v-for="file in comment.files"
                                    :key="file.id"
                                    :file="file" />
                            </VList>
                        </VSheet>
                        <template #actions>
                            <VBtn
                                v-if="comment.files.length > 1"
                                prepend-icon="mdi-download"
                                variant="tonal"
                                target="_blank"
                                :href="`/user/tickets/thread/${comment.id}/files`">
                                {{ $t('Download all files') }}
                            </VBtn>
                        </template>
                    </ModalDialog>
                </div>

                <VSheet class="info d-flex">
                    <div class="date">
                        <ClockOutlineIcon :size="18" />
                        {{ createdAt }}
                    </div>
                    <div class="name">
                        <AccountCircleOutlineIcon :size="18" />
                        {{ author.firstname }} {{ author.lastname }}
                    </div>
                </VSheet>
            </VCardText>
        </VCard>
    </VSheet>
</template>

<script>
import { COMMENT } from '../../js/consts.js'
import TicketThreadFile from './TicketThreadFile.vue'
import DownloadIcon from 'vue-material-design-icons/Download.vue'
import ClockOutlineIcon from 'vue-material-design-icons/ClockOutline.vue'
import AccountCircleOutlineIcon from 'vue-material-design-icons/AccountCircleOutline.vue'
import Avatar from './Avatar.vue'
import ModalDialog from './ModalDialog.vue'

export default {
    name: 'TicketThreadItem',
    components: {
        ClockOutlineIcon,
        AccountCircleOutlineIcon,
        Avatar,
        TicketThreadFile,
        DownloadIcon,
        ModalDialog
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
        iconAndColor() {
            switch (this.comment.type) {
                case this.statuses.APPROVE_COMMENT:
                    return {
                        icon: 'mdi-thumb-up-outline',
                        color: 'info',
                        text: this.$t('Approved')
                    }
                case this.statuses.DECLINE_COMMENT:
                    return {
                        icon: 'mdi-thumb-down-outline',
                        color: 'warning',
                        text: this.$t('Declined')
                    }
                case this.statuses.CLOSE_COMMENT:
                    return {
                        icon: 'mdi-close',
                        color: 'error',
                        text: this.$t('Closed')
                    }
                case this.statuses.SOLVED_COMMENT:
                    return {
                        icon: 'mdi-check',
                        color: 'success',
                        text: this.$t('Solution')
                    }
                case this.statuses.REOPEN_COMMENT:
                    return {
                        icon: 'mdi-redo',
                        color: 'purple',
                        text: this.$t('Reopened')
                    }
                default:
                    return {
                        icon: 'mdi-comment',
                        color: 'grey',
                        text: this.$t('Comment')
                    }
            }
        },
        createdAt() {
            return this.comment.created_at
        },
        user() {
            return this.$store.getters['getUser']
        },
        author() {
            return this.comment.user
        },
        content() {
            return this.comment.content
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
