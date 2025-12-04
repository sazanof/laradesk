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
        },
        size: {
            type: Number,
            default: 134
        }
    }
}
</script>

<template>
    <VCard
        variant="tonal"
        class="fill-height pa-4 position-relative"
        rounded="lg"
        :disabled="user.deleted_at !== null">
        <VSheet
            :class="{'text-center': $vuetify.display.mobile, 'd-flex': !$vuetify.display.mobile}">
            <VSheet>
                <Avatar
                    class="mr-4"
                    :user="user"
                    :size="size" />
            </VSheet>
            <VSheet color="transparent">
                <div
                    class="mb-4 text-h6 font-weight-bold text-uppercase">
                    {{ user.lastname }} {{ user.firstname }}
                </div>
                <div
                    class="opacity-70 font-weight-bold">
                    {{ user.organization }}
                </div>
                <div
                    class="position">
                    {{ user.position }}, {{ user.department }}
                </div>
                <VBtn
                    class="mt-4"
                    size="small"
                    density="comfortable"
                    rounded="pill"
                    variant="tonal"
                    prepend-icon="mdi-email"
                    target="_blank"
                    :text="user.email"
                    :href="`mailto:${user.email}`" />
                <VSpacer />
                <VBtn
                    density="comfortable"
                    size="small"
                    rounded="pill"
                    variant="tonal"
                    class="mt-4"
                    prepend-icon="mdi-phone"
                    target="_blank"
                    :text="user.phone" />
            </VSheet>
        </VSheet>
    </VCard>
</template>

<style scoped lang="scss">

</style>
