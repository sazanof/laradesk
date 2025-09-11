<template>
    <VCard
        hover
        :style="`background-color: rgba(var(--rgb-color-${description}), 0.4)`">
        <template #prepend>
            <div
                class="icon"
                :style="`color: rgba(var(--rgb-color-${description}), 0.7)`">
                <slot
                    name="icon">
                    <CircleOutlineIcon :size="64" />
                </slot>
            </div>
        </template>
        <template #text>
            <div class="text">
                <div class="title">
                    {{ counter }}
                </div>
                <div class="description">
                    {{ desc }}
                </div>
            </div>
        </template>
    </VCard>
</template>

<script>
import stringToColor from '../../js/helpers/strinToColor.js'
import CircleOutlineIcon from 'vue-material-design-icons/CircleOutline.vue'

export default {
    name: 'DashboardCard',
    components: {
        CircleOutlineIcon
    },
    props: {
        description: {
            type: String,
            default: ''
        },
        counter: {
            type: Number,
            required: true
        }
    }, data() {
        return {
            number: 0
        }
    },
    computed: {
        desc() {
            return this.$t(`dashboard_${this.description}`)
        },
        color() {
            return stringToColor(this.description)
        }
    }
}
</script>

<style scoped lang="scss">


.text {
    text-align: right;
    position: absolute;
    z-index: 2;
    right: 24px;
    top: 10px;

    .title {
        font-size: 48px;
        font-weight: bold;
    }

    .description {
        color: var(--bs-gray);
        position: relative;
        top: -10px;
        padding-right: 5px;
    }
}

</style>
