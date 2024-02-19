<template>
    <div class="settings">
        <h2>{{ $t('Settings') }}</h2>
        <h4>{{ $t('Appearance') }}</h4>
        <div class="form-group">
            <label for="">{{ $t('App name') }}</label>
            <input
                v-model="name"
                class="form-control"
                type="text">
        </div>
        <div class="form-group">
            <label for="">{{ $t('App logo') }}</label>
            <input
                ref="inputLogo"
                class="form-control"
                type="file"
                accept="image/*"
                @change="onChangeLogoFile">
            <div class="compare">
                <img
                    v-if="appLogo !== null"
                    :src="appLogo"
                    class="preview-img">
                <div
                    v-if="logo"
                    class="arrow">
                    <ArrowRightBoldIcon :size="30" />
                </div>
                <img
                    v-if="logo !== null"
                    :src="logoBlobUrl"
                    class="preview-img">
            </div>
        </div>
        <div class="form-group">
            <label for="">{{ $t('App bg') }}</label>
            <input
                ref="inputBg"
                class="form-control"
                type="file"
                accept="image/*"
                @change="onChangeBgFile">
            <div class="compare">
                <img
                    v-if="appBg !== null"
                    :src="appBg"
                    class="preview-img">
                <div
                    v-if="bg"
                    class="arrow">
                    <ArrowRightBoldIcon :size="30" />
                </div>
                <img
                    v-if="bg !== null"
                    :src="bgBlobUrl"
                    class="preview-img">
            </div>
        </div>
        <h4>{{ $t('Files') }}</h4>
        <div class="form-group">
            <label for="">{{ $t('Max file size') }}</label>
            <div class="form-group-info">
                {{ $t('Specify the maximum possible size of uploaded files to the system (kb)') }}
            </div>
            <input
                v-model="maxFileSize"
                class="form-control"
                type="number">
        </div>
        <div class="form-group">
            <label for="">{{ $t('Allowed mimes') }}</label>
            <div class="form-group-info">
                {{ $t('Specify the types of files allowed for uploading') }}
            </div>
            <div
                v-if="allowedMimes.length > 0"
                class="mimes-list">
                <div
                    v-for="m in allowedMimes"
                    :key="m"
                    class="badge bg-success">
                    {{ m }}
                </div>
            </div>
            <MimesMultiselect
                :value="allowedMimes"
                @on-change="allowedMimes = $event" />
        </div>
        <button
            class="btn btn-purple"
            @click="saveSettings">
            <ContentSaveIcon :size="18" />
            {{ $t('Save') }}
        </button>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import MimesMultiselect from '../../chunks/MimesMultiselect.vue'
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import ArrowRightBoldIcon from 'vue-material-design-icons/ArrowRightBold.vue'

const toast = useToast()

export default {
    name: 'AdmSettings',
    components: {
        ArrowRightBoldIcon,
        ContentSaveIcon,
        MimesMultiselect
    },
    data() {
        return {
            name: null,
            logo: null,
            logoBlobUrl: null,
            bg: null,
            bgBlobUrl: null,
            maxFileSize: 1000,
            allowedMimes: []
        }
    },
    computed: {
        appName() {
            return this.$store.getters['getAppName']
        },
        appLogo() {
            return this.$store.getters['getAppLogo']
        },
        appBg() {
            return this.$store.getters['getAppBg']
        },
        configMaxFileSize() {
            return this.$store.getters['getMaxFileSize']
        },
        configAllowedMimes() {
            return this.$store.getters['getAllowedMimes']
        }
    },
    created() {
        this.name = this.appName
        this.maxFileSize = this.configMaxFileSize
        this.allowedMimes = this.configAllowedMimes
    },
    methods: {
        onChangeLogoFile(e) {
            this.logo = this.$refs.inputLogo.files[0]
            this.logoBlobUrl = URL.createObjectURL(this.logo)
        },
        onChangeBgFile(e) {
            this.bg = this.$refs.inputBg.files[0]
            this.bgBlobUrl = URL.createObjectURL(this.bg)
        },
        async saveSettings() {
            const data = {
                name: this.name,
                maxFileSize: this.maxFileSize,
                mimes: this.allowedMimes,
                bg: this.bg,
                logo: this.logo

            }
            await this.$store.dispatch('saveSettings', data).then(() => {
                document.location.reload()
            }).catch(e => {
                toast.error(e.response.data.message)
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.settings {
    margin: 0 auto;
    padding: var(--padding-box);
    max-width: 500px;

    h4 {
        color: var(--bs-gray);
        margin: 16px 0;
    }

    .mimes-list {
        margin: 6px 0;

        .badge {
            margin-right: 6px;
        }
    }

    .compare {
        display: flex;
        align-items: center;
        justify-content: start;
        margin: 10px 0;
        height: 100px;

        .arrow {
            padding: 0 20px;
        }

        .preview-img {
            height: 100px;
        }
    }


}
</style>
