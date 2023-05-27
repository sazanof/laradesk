<template>
    <div class="form-field">
        <div class="content">
            <div class="order">
                {{ field.order }}
            </div>
            <div class="name">
                {{ field.name }}
            </div>
            <slot name="description" />
            <div class="type">
                {{ type.name }}
            </div>
        </div>
        <div class="actions">
            <slot name="actions">
                <button
                    class="btn btn-purple"
                    @click="$emit('on-field-click', field)">
                    <ChevronDoubleRightIcon :size="18" />
                </button>
            </slot>
        </div>
    </div>
</template>

<script>
import ChevronDoubleRightIcon from 'vue-material-design-icons/ChevronDoubleRight.vue'

export default {
    name: 'FieldItem',
    components: {
        ChevronDoubleRightIcon
    },
    props: {
        field: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-field-click' ],
    data() {
        return {
            disabled: false
        }
    },
    computed: {
        type() {
            return {
                name: this.$t(this.field.type),
                value: this.field.type
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.form-field {
    padding: calc(var(--padding-box) / 2);
    border-radius: var(--border-radius);
    border: 1px solid var(--bs-border-color);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;

    &:hover {
        background: var(--bs-light);
    }

    .name {
        font-weight: bold;
    }
}
</style>
