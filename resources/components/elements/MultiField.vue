<script>
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import MinusIcon from 'vue-material-design-icons/Minus.vue'

export default {
    name: 'MultiField',
    components: {
        PlusIcon,
        MinusIcon
    },
    props: {
        field: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-update-value' ],
    data() {
        return {
            values: [],
            questions: []
        }
    },
    computed: {
        options() {
            return this.field.options !== null ? JSON.parse(this.field.options) : null
        },
        multiOptions() {
            return this.options?.options
        },
        multiFields() {
            return this.options?.fields
        },
        hasMany() {
            return this.multiOptions?.max > 1
        },
        isHorizontal() {
            return this.multiOptions?.horizontal
        },
        multiLength() {
            return this.multiOptions?.max
        },
        canAddMore() {
            return this.values.length < this.multiLength
        },
        canDelete() {
            return this.values.length > 1
        }
    },
    created() {
        this.questions = this.multiFields
        this.values = [
            this.questions.map((v, i) => {
                return {
                    index: i,
                    value: null
                }
            })
        ]
    },
    methods: {
        deleteItem(item) {
            if (this.canDelete) {
                this.values = this.values.filter(value => value !== item)
            }
        },
        addItem(index) {
            if (this.canAddMore) {
                const newVal = this.questions.map((v, i) => {
                    return {
                        index: i,
                        value: null
                    }
                })
                this.values.splice(index + 1, 0, newVal)
            }
        }
    }
}
</script>

<template>
    <div
        class="multi-fields"
        :class="isHorizontal? 'horizontal' : 'vertical'">
        <div
            v-for="(val, num) in values"
            :key="val"
            class="multi-field-wrapper row">
            <div
                v-for="(f,i) in questions"
                :key="f"
                class="multi-fields-field"
                :class="f?.class ? f.class : 'col-md-12'">
                <div class="form-group">
                    <label for="">{{ f.title }}</label>
                    <div class="note">
                        {{ f.comment }}
                    </div>
                    <input
                        v-model="values[num][i].value"
                        class="form-control"
                        type="text"
                        @keyup="$emit('on-update-value', values)">
                </div>
                <div class="actions">
                    <button
                        class="btn me-2 btn-success btn-sm btn-icon"
                        :class="canAddMore ? '' : 'disabled'"
                        @click="addItem(num)">
                        <PlusIcon :size="20" />
                    </button>
                    <button
                        class="btn btn-danger btn-sm btn-icon"
                        :class="canDelete ? '' : 'disabled'"
                        @click="deleteItem(values[num])">
                        <MinusIcon :size="20" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.multi-fields {
    padding: 10px;

    .multi-field-wrapper {
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        margin-bottom: 20px;
        transition: var(--transition-duration);
        border-radius: var(--border-radius);
        padding: var(--padding-box) 0;
        background: rgba(0, 0, 0, 0.05);

        &:hover {
            .actions {
                display: flex;
            }
        }

        .actions {
            display: none;
            position: absolute;
            top: -16px;
            right: 10px;
            width: auto;

            .btn {
                padding: 4px;
            }
        }


        &:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }


        .multi-fields-field {

            .note {
                font-size: var(--font-small);
                opacity: 0.5
            }
        }

        &.horizontal {
            flex-direction: row;
        }

        &.vertical {
            flex-direction: column;
        }

    }


}
</style>
