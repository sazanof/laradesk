<template>
    <div class="login-wrapper">
        <div class="login">
            <div class="appName">
                <h5>{{ appName }}</h5>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-text">
                        <AccountIcon :size="18" />
                    </span>
                    <input
                        v-model="username"
                        type="text"
                        class="form-control"
                        :placeholder="$t('Username')">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-text">
                        <KeyIcon :size="18" />
                    </span>
                    <input
                        v-model="password"
                        type="password"
                        class="form-control"
                        :placeholder="$t('Password')">
                </div>
            </div>
            <div class="form-group">
                <button
                    :disabled="disabled"
                    class="btn btn-primary w-100"
                    @click="logIn">
                    <LockIcon :size="18" />
                    {{ $t('Log in') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import AccountIcon from 'vue-material-design-icons/Account.vue'
import KeyIcon from 'vue-material-design-icons/Key.vue'
import LockIcon from 'vue-material-design-icons/Lock.vue'

export default {
    name: 'Login',
    components: {
        AccountIcon,
        KeyIcon,
        LockIcon
    },
    data() {
        return {
            username: '',
            password: '',
            disabled: false
        }
    },
    computed: {
        appName() {
            return this.$store.state.appName
        }
    },
    methods: {
        async logIn() {
            this.disabled = true
            await this.$store.dispatch('logIn', {
                username: this.username,
                password: this.password
            }).catch(e => {
                alert(e.response.data.message)
            })
            this.disabled = false
        }
    }
}
</script>

<style lang="scss" scoped>
.login-wrapper {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;

    .login {
        width: 340px;
        padding: calc(var(--padding-box) * 1.4);
        background: var(--background-white);
        border-radius: var(--border-radius);
        box-shadow: var(--bs-box-shadow);

        .appName {
            text-align: center;
            margin-bottom: 14px;
        }
    }
}

</style>
