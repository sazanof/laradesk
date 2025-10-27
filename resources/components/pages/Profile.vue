<template>
    <VContainer>
        <VRow>
            <VCol
                cols="12"
                md="3">
                <Avatar
                    :user="user"
                    :size="260" />
                <VBtn
                    block
                    class="my-2"
                    prepend-icon="mdi-upload"
                    :text="$t('Change photo')"
                    @click="triggerInput" />
                <VBtn
                    size="small"
                    variant="text"
                    color="default"
                    prepend-icon="mdi-account-edit"
                    :text="$t('Request a data change') "
                    @click="$refs.updatesModal.open()" />
                <input
                    ref="avatar"
                    accept="image/jpeg,image/png"
                    class="d-none"
                    type="file"
                    name="avatar"
                    @change="showCropper">
            </VCol>
            <VCol
                cols="12"
                md="9">
                <VExpansionPanels
                    v-model="panel"
                    color="deep-purple">
                    <VExpansionPanel
                        value="profile"
                        :title="$t('Profile')">
                        <template #text>
                            <VTable>
                                <tbody>
                                    <tr>
                                        <td>{{ $t('Firstname') }}</td>
                                        <td>{{ user.firstname }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('Lastname') }}</td>
                                        <td>{{ user.lastname }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('Organization') }}</td>
                                        <td>{{ user.organization }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('Department') }}</td>
                                        <td>{{ user.department }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('Position') }}</td>
                                        <td>{{ user.position }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('Email') }}</td>
                                        <td>{{ user.email }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('Phone') }}</td>
                                        <td>{{ user.phone }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('Office') }}</td>
                                        <td>
                                            {{ user.office === null ? '--' : user.office?.address }}
                                            <VBtn
                                                prepend-icon="mdi-pencil"
                                                variant="plain"
                                                size="small"
                                                @click="editLocation">
                                                {{ $t('Edit') }}
                                            </VBtn>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ $t('Room') }}</td>
                                        <td>
                                            {{ user.room === null ? '--' : user.room?.name }}
                                            <button
                                                class="mx-2 btn btn-sm btn-purple"
                                                @click="editLocation">
                                                {{ $t('Edit') }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </VTable>
                        </template>
                    </VExpansionPanel>
                    <VExpansionPanel
                        value="notifications"
                        :title="$t('Notifications')">
                        <template #text>
                            <VSheet class="mt-4">
                                <VCheckbox
                                    v-model="noty.email"
                                    :label="$t('Email notifications')"
                                    @update:model-value="updateNotificationSettings" />
                                <VCheckbox
                                    v-model="noty.details.ticket"
                                    :label="$t('New ticket')"
                                    @update:model-value="updateNotificationSettings" />
                                <VCheckbox
                                    id="details_comment"
                                    v-model="noty.details.comment"
                                    :label="$t('New comment')"
                                    @update:model-value="updateNotificationSettings" />
                                <VCheckbox
                                    v-model="noty.details.approval"
                                    :label="$t('Added me as approval')"
                                    @update:model-value="updateNotificationSettings" />
                                <VCheckbox
                                    v-model="noty.details.assignee"
                                    :label="$t('Added me as assignee')"
                                    @update:model-value="updateNotificationSettings" />
                                <VCheckbox
                                    v-model="noty.details.observer"
                                    :label="$t('Added me as observer')"
                                    @update:model-value="updateNotificationSettings" />
                            </VSheet>
                        </template>
                    </VExpansionPanel>
                </VExpansionPanels>
            </VCol>
        </VRow>
        <ImageCropper
            v-if="avatar"
            ref="cropper"
            :file="avatar"
            @on-save="onCropperSave($event)" />
        <ModalDialog
            ref="updatesModal"
            size="big"
            :title="$t('Request a data change')">
            <VAlert
                color="info"
                variant="tonal"
                class="mb-2"
                density="compact">
                {{ $t('If yours require an update, you can request a data change from the administrator') }}
            </VAlert>
            <div class="form-group">
                <VTextarea
                    v-model="message"
                    :label="$t('Content')" />
            </div>
            <template #actions>
                <VBtn
                    :disabled="formDisabled"
                    :text="$t('Send')"
                    prepend-icon="mdi-send"
                    @click="requestUserInfoUpdates" />
            </template>
        </ModalDialog>
        <ModalDialog
            ref="locationModal"
            :title="$t('Edit location')"
            size="big">
            <ChangeLocationForm @on-save="$refs.locationModal.close()" />
        </ModalDialog>
    </VContainer>
</template>

<script>
import { useToast } from 'vue-toastification'
import ModalDialog from '../chunks/ModalDialog.vue'
import Avatar from '../chunks/Avatar.vue'
import ImageCropper from '../chunks/ImageCropper.vue'
import ChangeLocationForm from '../chunks/ChangeLocationForm.vue'
import { createSuccessNotification } from '@/js/helpers/notificationHelper.js'

const toast = useToast()

export default {
    name: 'Profile',
    components: {
        ChangeLocationForm,
        Avatar,
        ImageCropper,
        ModalDialog
    },
    data() {
        return {
            avatar: null,
            panel: 'profile',
            noty: {
                email: false,
                details: {
                    ticket: false,
                    comment: false,
                    approval: false,
                    observer: false,
                    assignee: false
                }
            },
            message: null
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        notificationsSettings() {
            return this.$store.state.systemNotifications
        },
        formDisabled() {
            return this.message === null || this.message.length < 3
        }
    },
    async created() {
        await this.$store.dispatch('getNotificationsSettings')
        this.noty = this.notificationsSettings
    },
    methods: {
        editLocation() {
            this.$refs.locationModal.open()
        },
        triggerInput() {
            this.$refs.avatar.click()
        },
        showCropper(event) {
            this.avatar = event.target.files[0]
            this.$nextTick(() => {
                this.$refs.cropper.open()
            })

        },
        async onCropperSave({ coordinates, canvas }) {
            await this.$store.dispatch('updateAvatar', {
                coordinates,
                avatar: this.avatar
            })
            this.$store.commit('updateAvatar', canvas.toDataURL())
        },
        async updateNotificationSettings() {
            const res = await this.$store.dispatch('updateNotificationsSettings', this.noty)
            if (res) {
                this.$store.commit('addNotification', createSuccessNotification(this.$t('Saved')))
            }
        },
        async requestUserInfoUpdates() {
            await this.$store.dispatch('requestUserInfoUpdates', { message: this.message })
            this.message = null
            this.$refs.updatesModal.close()
            this.$store.commit('addNotification', createSuccessNotification(this.$t('Message sent')))
        }
    }
}
</script>

<style lang="scss" scoped>
.profile {
    .inner {
        padding: var(--padding-box);
        display: flex;
        flex-wrap: wrap;

        .left {
            width: 260px;
        }

        .right {
            width: calc(100% - 260px);
            padding-left: var(--padding-box);
        }

        .notification_details {
            display: inline-block;
            padding: var(--padding-box);
            background: var(--bs-light);
            border-radius: var(--bs-border-radius);
            margin-top: 10px;
        }
    }
}
</style>
