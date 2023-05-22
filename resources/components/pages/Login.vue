<template>
    <div class="login">
        <div class="form-group">
            <label>{{ $t('Username') }}</label>
            <input
                v-model="username"
                type="text"
                class="form-control">
        </div>
        <div class="form-group">
            <label>{{ $t('Password') }}</label>
            <input
                v-model="password"
                type="password"
                class="form-control">
        </div>
        <div class="form-group">
            <button class="btn btn-primary">
                {{ $t('Log in') }}
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Login',
    data() {
        return {
            username: '',
            password: ''
        }
    }
}
</script>

<style lang="scss" scoped>
.login {
    width: 450px;
    padding: var(--padding-box);
    background: var(--background-white);
    border-radius: var(--border-radius);
}
</style>
