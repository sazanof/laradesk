<template>
    <VAvatar
        v-if="user.photo && user.photo !== ''"
        :image="user.photo" />
    <VAvatar
        v-else
        :color="color"
        :text="fullName" />
</template>

<script>
import '@webzlodimir/vue-avatar/dist/style.css'
import stringToColor from '../../js/helpers/strinToColor.js'

export default {
    name: 'Avatar',
    props: {
        user: {
            type: Object,
            required: true
        },
        size: {
            type: Number,
            default: 64
        }
    },
    computed: {
        fullName() {
            return `${this.user.firstname[0]}${this.user.lastname[0]}`
        },
        color() {
            return stringToColor(`${this.user.firstname} ${this.user.lastname}`)
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
