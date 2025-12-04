<template>
    <div class="settings">
        <h2 class="text-h4">
            {{ $t('Settings') }}
        </h2>
        <h4 class="text-h6">
            {{ $t('Appearance') }}
        </h4>
        <VTextField
            v-model="name"
            :label="$t('App name')" />
        <VFileInput
            ref="inputLogo"
            class="mt-4"
            type="file"
            :label="$t('App logo')"
            accept="image/*"
            @update:model-value="onChangeLogoFile" />
        <div class="compare">
            <VImg
                v-if="appLogo !== null"
                :src="appLogo"
                class="preview-img" />
            <div
                v-if="logo"
                class="arrow">
                <VIcon
                    icon="mdi-arrow-right-bold"
                    :size="30" />
            </div>
            <VImg
                v-if="logo !== null"
                :src="logoBlobUrl"
                class="preview-img" />
        </div>
        <VFileInput
            ref="inputBg"
            :label="$t('App bg')"
            class="form-control"
            type="file"
            accept="image/*"
            @update:model-value="onChangeBgFile" />
        <div class="compare">
            <VImg
                v-if="appBg !== null"
                :src="appBg"
                class="preview-img" />
            <div
                v-if="bg"
                class="arrow">
                <VIcon
                    icon="mdi-arrow-right-bold"
                    :size="30" />
            </div>
            <VImg
                v-if="bg !== null"
                :src="bgBlobUrl"
                class="preview-img" />
        </div>
        <h4 class="text-h6">
            {{ $t('Files') }}
        </h4>
        <VTextField
            v-model.number="maxFileSize"
            class="mt-6"
            :label="$t('Max file size')"
            :hide-details="false"
            persistent-hint
            :hint="$t('Specify the maximum possible size of uploaded files to the system (kb)')" />
        <MimesMultiselect
            class="mt-6"
            :value="allowedMimes"
            @on-change="allowedMimes = $event" />
        <VBtn
            class="mt-6"
            prepend-icon="mdi-content-save"
            :text="$t('Save')"
            @click="saveSettings">
            <ContentSaveIcon :size="18" />
            {{ $t('Save') }}
        </VBtn>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import MimesMultiselect from '../../chunks/MimesMultiselect.vue'
import ContentSaveIcon from 'vue-material-design-icons/ContentSave.vue'
import ArrowRightBoldIcon from 'vue-material-design-icons/ArrowRightBold.vue'
import { createErrorNotification } from '@/js/helpers/notificationHelper.js'

export default {
    name: 'AdmSettings',
    components: {
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
                this.$store.commit('addNotification', createErrorNotification(e.response.data.message))
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
