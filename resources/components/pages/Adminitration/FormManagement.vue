<template>
    <div
        v-if="category"
        class="form-management">
        <h3>
            {{
                $t('Manage category {category}', {
                    category: category.name
                })
            }}
        </h3>
        <TipElement class="mt-3">
            {{
                $t('You can select the required fields for the application form and change the order of their location. Please note that the default fields cannot be deleted.')
            }}
        </TipElement>
        <div class="fields-draggable">
            <div class="fields-available">
                <div class="title">
                    {{ $t('Available fields') }}
                </div>
                <div class="fields-list">
                    {{ fields }}
                </div>
            </div>
            <div class="fields-enabled">
                <div class="title">
                    {{ $t('Form fields') }}
                </div>
                <div class="fields-list">
                    {{ fields }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TipElement from '../../ elements/TipElement.vue'

export default {
    name: 'FormManagement',
    components: {
        TipElement
    },
    data() {
        return {
            category: null,
            availableFields: [],
            enabledFields: []
        }
    },
    computed: {
        id() {
            return parseInt(this.$route.params.id)
        },
        fields() {
            return this.$store.getters['getFields']
        }
    },
    created() {
        this.getFields()
        this.getCategory()
    },
    methods: {
        getFields() {
            if (this.fields.length === 0) {
                this.$store.dispatch('getFields')
            }
        },
        async getCategory() {
            this.category = await this.$store.dispatch('getCategoryWithFields', this.id)
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
        padding: var(--padding-box);
        border-right: 1px solid var(--bs-border-color);
    }

    .fields-enabled {
        width: 50%;
        padding: var(--padding-box);
    }
}
</style>
