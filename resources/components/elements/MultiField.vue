<script>
export default {
    name: 'MultiField',
    components: {},
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
        },
        canUpdateValue(f, v) {
            let success = false
            if (f?.min !== undefined && f?.min !== null) {
                success = v.value.length >= f.min
            } else {
                success = true
            }
            if (success) {
                this.$emit('on-update-value', this.values)
            } else {
                this.$emit('on-update-value', [])
            }
            v.err = !success
        }
    }
}
</script>

<template>
    <VSheet>
        <VCard
            v-for="(val, num) in values"
            :key="val"
            hover
            variant="tonal"
            :class="isHorizontal? 'horizontal' : 'vertical'">
            <template
                #text>
                <VContainer class="pa-0">
                    <VRow>
                        <VCol
                            v-for="(f,i) in questions"
                            :key="f"
                            class="multi-fields-field"
                            :cols="f?.cols ? f.cols : 12">
                            <div>
                                <label for="">{{ f.title }}</label>
                                <VTextField
                                    v-model="values[num][i].value"
                                    :minlength="f?.min ? f.min : 0"
                                    :maxlength="f?.max ? f.max : 255"
                                    class="form-control"
                                    :color="values[num][i].err?'error':'default'"
                                    type="text"
                                    :hide-details="false"
                                    @keyup="canUpdateValue(f,values[num][i])">
                                    <template #details>
                                        <div>
                                            {{ f.comment }}
                                        </div>
                                    </template>
                                </VTextField>
                            </div>
                        </VCol>
                    </VRow>
                </VContainer>
            </template>
            <template #append>
                <VBtn
                    variant="flat"
                    size="small"
                    density="comfortable"
                    icon="mdi-plus"
                    :class="canAddMore ? '' : 'disabled'"
                    @click="addItem(num)" />
                <VBtn
                    class="ml-2"
                    variant="flat"
                    size="small"
                    density="comfortable"
                    icon="mdi-minus"
                    :class="canDelete ? '' : 'disabled'"
                    @click="deleteItem(values[num])" />
            </template>
        </VCard>
    </VSheet>
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
