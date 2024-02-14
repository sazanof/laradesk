<template>
    <div
        class="dashboard-card">
        <div class="inner">
            <div
                class="delimiter"
                :style="`background-color: var(--ticket-color-${description})`" />
            <div
                class="icon"
                :style="`color: var(--ticket-color-${description})`">
                <slot
                    name="icon">
                    <CircleOutlineIcon :size="64" />
                </slot>
            </div>
            <div class="text">
                <div class="title">
                    {{ counter }}
                </div>
                <div class="description">
                    {{ desc }}
                </div>
            </div>
        </div>
    </div>
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
.dashboard-card {
    display: inline-block;
    min-width: 250px;
    height: 120px;
    padding: 10px;

    .inner {
        position: relative;
        height: 100px;
        padding-right: 16px;
        border-radius: var(--border-radius);
        border: var(--bs-border-width) solid var(--bs-border-color);
        overflow: hidden;

        .icon {
            position: absolute;
            top: 16px;
            left: 16px;
            opacity: 0.3;
        }

        .delimiter {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 6px;
        }

        .text {
            text-align: right;
            position: absolute;
            z-index: 2;
            right: 14px;
            top: 10px;

            .title {
                font-size: 38px;
                font-weight: bold;
            }

            .description {
                color: var(--bs-gray);
                padding-right: 5px;
            }
        }
    }
}
</style>
