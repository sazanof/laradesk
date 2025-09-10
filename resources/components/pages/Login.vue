<template>
    <VSheet
        class="login-wrapper fill-height overflow-hidden"
        color="grey-lighten-2">
        <div
            class="bg fill-height"
            :class="{'blur': blur}"
            :style="`background: url('${appBg}') center center; background-size:cover`" />
        <VSheet
            class="overlay"
            color="deep-purple" />
        <VCard
            color="white"
            width="400"
            class="position-relative"
            style="z-index:100"
            @mouseenter="blur=true"
            @mouseleave="blur = false">
            <template #prepend>
                <VAvatar
                    class="inline-block"
                    :image="appLogo" />
            </template>
            <template #title>
                {{ appName }}
            </template>
            <template #text>
                <VTextField
                    v-model="username"
                    class="mt-2"
                    type="text"
                    prepend-inner-icon="mdi-account"
                    :label="$t('Username')" />
                <VTextField
                    v-model="password"
                    type="password"
                    prepend-inner-icon="mdi-key"
                    class="mt-4"
                    :label="$t('Password')" />
            </template>
            <template #actions>
                <VBtn
                    block
                    variant="flat"
                    color="deep-purple"
                    :disabled="disabled"
                    :loading="disabled"
                    prepend-icon="mdi-send"
                    :text="$t('Log in')"
                    @click="logIn" />
            </template>
        </VCard>
    </VSheet>
</template>

<script>
import { createErrorNotification } from '@/js/helpers/notificationHelper.js'

export default {
    name: 'Login',
    data() {
        return {
            username: '',
            password: '',
            disabled: false,
            blur: false
        }
    },
    computed: {
        appName() {
            return this.$store.state.appName
        },
        appLogo() {
            return this.$store.state.appLogo
        },
        appBg() {
            return this.$store.getters['getAppBg']
        }
    },
    methods: {
        async logIn() {
            this.disabled = true
            await this.$store.dispatch('logIn', {
                username: this.username,
                password: this.password
            }).catch(e => {
                this.$store.commit('addNotification', createErrorNotification(e.response.data.message))
            })
            this.disabled = false
        }
    }
}
</script>

<style lang="scss" scoped>
.login-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    height: 100%;


}

.bg {
    opacity: 0.6;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: calc(100% + 20px);
    height: calc(100% + 20px);
    transition: 0.5s;
    overflow: hidden;

    &.blur {
        filter: blur(5px);
    }
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100;
    opacity: 0.3;
}

</style>
