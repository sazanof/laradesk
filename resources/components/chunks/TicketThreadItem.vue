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
            max-width="600"
            class="author"
            style="width: calc(100% - 64px)">
            <VCardText
                class="pa-0 mb-2 ">
                <VSheet color="grey-lighten-4 pa-2">
                    <VChip
                        class="mr-2"
                        rounded="pill"
                        size="small"
                        density="comfortable"
                        variant="tonal"
                        :color="iconAndColor.color"
                        :text="iconAndColor.text"
                        :prepend-icon="iconAndColor.icon" />
                    <VIcon
                        class="mr-2"
                        icon="mdi-chevron-right"
                        size="small" />
                    <VChip
                        prepend-icon="mdi-account"
                        class="mr-2"
                        size="small"
                        rounded="pill"
                        density="comfortable"
                        variant="text"
                        :text="author.full_name" />
                    <VIcon
                        class="mr-2"
                        icon="mdi-chevron-right"
                        size="small" />

                    <VChip
                        rounded="pill"
                        size="small"
                        density="comfortable"
                        class="opacity-70"
                        :text="createdAt"
                        variant="text"
                        prepend-icon="mdi-clock" />
                </VSheet>
                <VSheet class="opacity-70 pa-4 pb-0">
                    {{ content }}

                    <div
                        v-if="comment?.files && comment.files.length > 0">
                        <ModalDialog
                            v-model="showFiles"
                            :title="$t('Comment files')">
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
                    <div
                        v-if="comment.files?.length > 0"
                        class="text-right">
                        <VChip
                            class="mt-2"
                            prepend-icon="mdi-paperclip"
                            rounded="pill"
                            size="x-small"
                            @click="showFiles = !showFiles">
                            {{ !showFiles ? $t('Show {count} files', {count: comment.files.length}) : $t('Show less') }}
                        </VChip>
                    </div>
                </VSheet>
            </VCardText>
        </VCard>
    </VSheet>
</template>

<script>
import { COMMENT } from '../../js/consts.js'
import TicketThreadFile from './TicketThreadFile.vue'
import Avatar from './Avatar.vue'
import ModalDialog from './ModalDialog.vue'

export default {
    name: 'TicketThreadItem',
    components: {
        Avatar,
        TicketThreadFile,
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
