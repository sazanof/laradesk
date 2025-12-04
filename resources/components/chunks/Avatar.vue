<template>
    <VAvatar
        v-if="user.photo && user.photo !== ''"
        :size="size"
        :image="user.photo" />
    <VAvatar
        v-else
        :size="size"
        :color="color">
        <template #default>
            <div :style="`font-size:${size*0.4}px`">
                {{ fullName }}
            </div>
        </template>
    </VAvatar>
</template>

<script>
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
            default: 40
        }
    },
    computed: {
        fullName() {
            return `${this.user.lastname[0]}${this.user.firstname[0]}`
        },
        color() {
            return stringToColor(`${this.user.lastname} ${this.user.firstname}`)
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
