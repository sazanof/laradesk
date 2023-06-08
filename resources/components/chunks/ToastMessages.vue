<template>
    <div class="messages">
        <strong>{{ $t('Errors detected') }}</strong>
        <div
            v-for="msg in messages"
            :key="msg"
            class="message">
            <div
                v-for="m in msg"
                :key="m"
                class="msg">
                {{ m }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ToastMessages',
    props: {
        messages: {
            type: Object,
            required: true
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
