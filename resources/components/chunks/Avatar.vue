<template>
    <VueAvatar
        v-if="user.photo && user.photo !== ''"
        :border-radius="borderRadius"
        :size="size">
        <img
            v-if="user.photo !== ''"
            :alt="fullName"
            :src="user.photo">
    </VueAvatar>
    <VueAvatar
        v-else
        :border-radius="borderRadius"
        :size="size"
        :username="fullName" />
</template>

<script>
import VueAvatar from '@webzlodimir/vue-avatar'
import '@webzlodimir/vue-avatar/dist/style.css'

export default {
    name: 'Avatar',
    components: {
        VueAvatar
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        size: {
            type: Number,
            default: 64
        },
        borderRadius: {
            type: String,
            default: '6px'
        }
    },
    computed: {
        fullName() {
            return `${this.user.firstname} ${this.user.lastname}`
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
