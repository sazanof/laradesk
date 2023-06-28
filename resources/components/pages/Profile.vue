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
                <a href="#">{{ $t('Request a data change') }}</a>
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
    </div>
</template>

<script>
import UploadIcon from 'vue-material-design-icons/Upload.vue'
import Avatar from '../chunks/Avatar.vue'
import ImageCropper from '../chunks/ImageCropper.vue'

export default {
    name: 'Profile',
    components: {
        Avatar,
        UploadIcon,
        ImageCropper
    },
    data() {
        return {
            avatar: null,
            noty: {
                email: false
            }
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        notificationsSettings() {
            return this.$store.state.notifications
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
