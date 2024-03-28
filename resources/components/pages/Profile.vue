<template>
    <div class="profile">
        <div class="inner">
            <div class="left">
                <Avatar
                    :user="user"
                    :size="260" />
                <button
                    class="btn btn-purple w-100 mt-3"
                    @click="triggerInput">
                    <UploadIcon :size="18" />
                    {{ $t('Change photo') }}
                </button>
                <button
                    class="btn btn-transparent mt-2 btn-sm w-100"
                    @click="$refs.updatesModal.open()">
                    <AccountEditIcon :size="18" />
                    {{ $t('Request a data change') }}
                </button>
                <input
                    ref="avatar"
                    accept="image/jpeg,image/png"
                    class="d-none"
                    type="file"
                    name="avatar"
                    @change="showCropper">
            </div>
            <div class="right">
                <h2>{{ $t('Profile') }}</h2>
                <table class="table table-responsive table-striped">
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
                            <td>{{ $t('Room') }}</td>
                            <td>{{ user.room_id === -1 ? '--' : user.room_id }}</td>
                        </tr>
                    </tbody>
                </table>
                <h2>{{ $t('Notifications') }}</h2>
                <div class="form-check form-switch">
                    <input
                        id="noty_email"
                        v-model="noty.email"
                        class="form-check-input"
                        type="checkbox"
                        @change="updateNotificationSettings">
                    <label
                        class="form-check-label"
                        for="noty_email">{{ $t('Email notifications') }}</label>
                </div>
            </div>
        </div>
        <ImageCropper
            v-if="avatar"
            ref="cropper"
            :file="avatar"
            @on-save="onCropperSave($event)" />
        <Modal
            ref="updatesModal"
            size="big"
            :title="$t('Request a data change')">
            <div class="alert alert-info">
                {{ $t('If yours require an update, you can request a data change from the administrator') }}
            </div>
            <div class="form-group">
                <label for="">{{ $t('Content') }}</label>
                <textarea
                    v-model="message"
                    cols="30"
                    rows="4"
                    class="form-control" />
            </div>
            <div class="form-groupl">
                <button
                    :disabled="formDisabled"
                    class="btn btn-purple"
                    @click="requestUserInfoUpdates">
                    <SendIcon :size="20" />
                    {{ $t('Send') }}
                </button>
            </div>
        </Modal>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import Modal from '../elements/Modal.vue'
import AccountEditIcon from 'vue-material-design-icons/AccountEdit.vue'
import SendIcon from 'vue-material-design-icons/Send.vue'
import UploadIcon from 'vue-material-design-icons/Upload.vue'
import Avatar from '../chunks/Avatar.vue'
import ImageCropper from '../chunks/ImageCropper.vue'

const toast = useToast()

export default {
    name: 'Profile',
    components: {
        Avatar,
        UploadIcon,
        AccountEditIcon,
        ImageCropper,
        Modal,
        SendIcon
    },
    data() {
        return {
            avatar: null,
            noty: {
                email: false
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
            await this.$store.dispatch('updateNotificationsSettings', this.noty)
        },
        async requestUserInfoUpdates() {
            await this.$store.dispatch('requestUserInfoUpdates', { message: this.message })
            this.message = null
            this.$refs.updatesModal.close()
            toast.success(this.$t('Message sent'))
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
    }
}
</style>
