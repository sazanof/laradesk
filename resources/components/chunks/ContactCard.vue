<script>
import Avatar from './Avatar.vue'

export default {
    name: 'ContactCard',
    components: {
        Avatar
    },
    props: {
        user: {
            type: Object,
            required: true
        }
    }
}
</script>

<template>
    <div class="text-center">
        <div class="pic">
            <Avatar
                :user="user"
                :size="164" />
        </div>
        <div class="info">
            <div
                class="my-4 text-h5 font-weight-bold text-uppercase">
                {{ user.firstname }} {{ user.lastname }}
            </div>
            <div
                class="opacity-70 font-weight-bold">
                {{ user.organization }}
            </div>
            <div
                class="position">
                {{ user.position }}, {{ user.department }}
            </div>
            <div class="mt-4">
                <VBtn
                    rounded="pill"
                    variant="tonal"
                    prepend-icon="mdi-email"
                    target="_blank"
                    :text="user.email"
                    :href="`mailto:${user.email}`" />
                <VBtn
                    rounded="pill"
                    variant="tonal"
                    class="ml-4"
                    prepend-icon="mdi-phone"
                    target="_blank"
                    :text="user.phone" />
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>
