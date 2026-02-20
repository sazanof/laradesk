<template>
    <div
        v-if="category"
        class="form-management">
        <h3 class="text-h5 font-weight-bold">
            {{
                $t('Manage category {category}', {
                    category: category.name
                })
            }}
        </h3>
        <VAlert
            icon="mdi-lightbulb"
            color="info"
            class="mt-3">
            {{
                $t('You can select the required fields for the application form and change the order of their location. Please note that the default fields cannot be deleted.')
            }}
        </VAlert>
        <VContainer>
            <VRow>
                <VCol
                    cols="12"
                    md="6"
                    class="fields-available">
                    <div class="text-h6 mb-3 text-center">
                        {{ $t('Available fields') }}
                    </div>
                    <VTextField
                        v-model="term"
                        prepend-inner-icon="mdi-magnify" />
                    <VList class="fields-list">
                        <FieldItem
                            v-for="field in availableFieldsCalculated"
                            :key="field.id"
                            :disabled="clickedFieldId === field.id"
                            :field="field"
                            @on-field-click="addField" />
                    </VList>
                </VCol>
                <VCol
                    cols="12"
                    md="6"
                    class="fields-enabled">
                    <div class="text-h6 mb-3 text-center">
                        {{ $t('Form fields') }}
                    </div>
                    <div class="fields-list">
                        <Draggable
                            v-model="enabledFields"
                            :disabled="drag"
                            group="people"
                            item-key="id"
                            @start="onDragStart"
                            @sort="onSort"
                            @end="onDragEnd">
                            <template #item="{element}">
                                <FieldItem
                                    ref="sortingList"
                                    :data-category-field-id="element.category_field_id"
                                    :disabled="clickedFieldId === element.id"
                                    :field="element">
                                    <template #actions>
                                        <VBtn
                                            :color="element.required ? 'orange' : 'default'"
                                            variant="tonal"
                                            icon="mdi-star"
                                            @click="makeRequired(element)" />
                                        <VBtn
                                            icon="mdi-close"
                                            color="error"
                                            variant="tonal"
                                            :disabled="element.is_default || element.id === clickedFieldId"
                                            class="ml-2"
                                            @click="deleteField(element)" />
                                    </template>
                                </FieldItem>
                            </template>
                        </Draggable>
                    </div>
                </VCol>
            </VRow>
        </VContainer>
    </div>
    <div
        v-else
        class="alert alert-info">
        {{ $t('Loading, please wait...') }}
    </div>
</template>

<script>
import Draggable from 'vuedraggable'
import FieldItem from '../../chunks/FieldItem.vue'

export default {
    name: 'FormManagement',
    components: {
        FieldItem,
        Draggable
    },
    data() {
        return {
            drag: false,
            category: null,
            availableFields: [],
            enabledFields: [],
            clickedFieldId: null,
            term: null
        }
    },
    computed: {
        id() {
            return parseInt(this.$route.params.id)
        },
        fields() {
            return this.$store.getters['getFields']
        },
        categoryFieldIds() {
            return Object.values(this.category.fields).map(_f => {
                return _f.id
            })
        },
        availableFieldsCalculated() {
            return this.term !== null ? this.availableFields.filter(f => f.name.toLowerCase().includes(this.term.toLowerCase())) : this.availableFields
        }

    },
    async created() {
        await this.getFields()
        await this.getCategory()

        this.availableFields = this.fields.filter(_field => {
            return this.categoryFieldIds.indexOf(_field.id) === -1
        })

        const filteredEn = this.fields.filter(_field => {
            return this.categoryFieldIds.indexOf(_field.id) !== -1
        })
        this.enabledFields = this.category.fields.map(_field => {
            const fc = filteredEn.find(fc => _field.field_id === fc.id)
            return Object.assign(_field, {
                type: fc.type
            })
        })
    },
    methods: {
        async getFields() {
            if (this.fields.length === 0) {
                await this.$store.dispatch('getFields')
            }
        },
        async getCategory() {
            this.category = await this.$store.dispatch('getCategoryWithFields', this.id)
            //TODO order in draggable list
            // move enabledFields to category.fields
        },
        async addField(field) {
            this.clickedFieldId = field.id
            const data = {
                field_id: field.id,
                category_id: this.id,
                order: this.availableFields.length + 1
            }
            const res = await this.$store.dispatch('linkField', data).catch(() => {
                this.clickedFieldId = null
            })
            this.enabledFields.push(Object.assign(field, {
                category_field_id: res.id,
                order: data.order
            }))
            this.availableFields = this.sortByOrder(this.availableFields.filter(_f => _f.id !== field.id))
            this.clickedFieldId = null
        },
        async deleteField(field) {
            this.clickedFieldId = field.id
            delete field.order
            delete field.category_field_id
            const data = {
                field_id: field.id,
                category_id: this.id
            }
            await this.$store.dispatch('unlinkField', data).then(() => {
                this.availableFields.push(field)
                this.enabledFields = this.sortByOrder(this.enabledFields.filter(_f => _f.id !== field.id))
                this.clickedFieldId = null
            }).catch(() => {
                this.clickedFieldId = null
            })

        },
        sortByOrder(arrayOfFields, direction = 'asc') {
            return arrayOfFields.sort((a, b) => {
                return direction === 'asc' ? a.order < b.order : a.order < b.order
            })
        },
        onDragStart(e) {
        },
        onDragEnd(e) {

        },
        async onSort(e) {
            this.drag = true
            /* const newIndex = e.newIndex
             const id = parseInt(e.item.dataset.categoryFieldId)*/

            this.enabledFields.map(async (f, i) => {
                await this.$store.dispatch('changeFieldOrder', {
                    id: f.category_field_id,
                    order: i
                }).catch(e => {
                    alert(e.response.data.message)
                    this.drag = false
                })
                f.order = i
            })
            this.drag = false

        },
        async makeRequired(el) {
            console.log(el)
            const required = !el.required
            await this.$store.dispatch('makeFieldRequired', {
                id: el.category_field_id,
                required: !el.required
            })
            el.required = required
        }
    }
}
</script>

<style lang="scss" scoped>
.fields-draggable {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;

    .title {
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
    }

    .fields-available {
        width: 50%;
        padding: var(--padding-box) var(--padding-box) var(--padding-box) 0;
        border-right: 1px solid var(--bs-border-color);
    }

    .fields-enabled {
        width: 50%;
        padding: var(--padding-box) 0 var(--padding-box) var(--padding-box);
    }

    .btn {
        margin-left: 4px;
    }
}
</style>
