<template>
    <div class="filter">
        <div class="basic">
            <div class="name">
                <span>{{ filterEnabled ? $t('Filter results') : $t('All results') }}</span>
                &nbsp;
                <a
                    v-if="filterEnabled"
                    href="javascript:void(0)"
                    @click.prevent="resetFilter">{{ $t('reset') }}</a>
            </div>
            <div class="buttons">
                <button
                    class="btn btn-purple btn-sm"
                    @click="$refs.filterModal.open()">
                    <FilterIcon :size="14" />
                    {{ $t('Filter') }}
                </button>
                <button
                    class="btn btn-success btn-sm"
                    @click="exportExcel">
                    <MicrosoftExcelIcon :size="14" />
                    {{ $t('XLSX') }}
                </button>
            </div>
        </div>
        <Modal
            ref="filterModal"
            size="medium"
            :footer="true"
            :title="$t('Filter')">
            <div class="form-group">
                <label for="">{{ $t('Search text') }}</label>
                <input
                    v-model="query.text"
                    type="text"
                    class="form-control">
            </div>
            <div class="form-group">
                <label for="">{{ $t('Category') }}</label>
                <MultiselectElement
                    :groups="true"
                    :options="allCategories"
                    :object="true"
                    label="name"
                    value-prop="id"
                    track-by="id"
                    @select="query.category_id = $event.id"
                    @clear="query.category_id = null" />
            </div>
            <div
                v-if="open"
                class="more">
                <div class="form-group">
                    <label for="">{{ $t('Requester') }}</label>
                    <div class="form-group">
                        <UsersMultiselect @on-users-changed="participantsToNums($event,'requesters')" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{ $t('Approvals') }}</label>
                    <div class="form-group">
                        <UsersMultiselect @on-users-changed="participantsToNums($event,'approvals')" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{ $t('Observers') }}</label>
                    <div class="form-group">
                        <UsersMultiselect @on-users-changed="participantsToNums($event,'observers')" />
                    </div>
                </div>
            </div>
            <template #footer-actions>
                <button
                    class="btn btn-link"
                    @click="open = !open">
                    {{ open ? $t('Less parameters') : $t('More parameters') }}
                </button>
                <button
                    class="btn btn-purple"
                    @click="applyFilter">
                    <FilterIcon :size="20" />
                    {{ $t('Apply filter') }}
                </button>
            </template>
        </Modal>
    </div>
</template>

<script>
import UsersMultiselect from '../elements/UsersMultiselect.vue'
import FilterIcon from 'vue-material-design-icons/Filter.vue'
import Modal from '../elements/Modal.vue'
import MultiselectElement from '../elements/MultiselectElement.vue'
import MicrosoftExcelIcon from 'vue-material-design-icons/MicrosoftExcel.vue'

export default {
    name: 'TicketsFilter',
    components: {
        MultiselectElement,
        Modal,
        FilterIcon,
        UsersMultiselect,
        MicrosoftExcelIcon
    },
    props: {
        filter: {
            type: Object,
            required: true
        }
    },
    emits: [ 'apply-filter', 'export-click' ],
    data() {
        return {
            open: false,
            categoriesToList: [],
            selectedCategory: null,
            query: {
                category_id: null,
                text: null,
                participants: {}
            }
        }
    },
    computed: {
        filterEnabled() {
            return this.query.category_id !== null
                || (this.query.text !== null
                    && this.query.text !== '')
                || Object.keys(this.query.participants).length > 0
        },
        categories() {
            return this.$store.getters['getCategories']
        },
        departments() {
            return this.$store.getters['getDepartments']
        },
        userDepartments() {
            return this.$store.getters['getUserDepartments']
        },
        activeDepartment() {
            return this.$store.getters['getActiveDepartment']
        },
        allCategories() {
            if (this.departments) {
                this.departments.map(department => {
                    this.categoriesToList.push({
                        label: department.name,
                        options: department.categories
                    })
                })
            }


            return this.categoriesToList
        }
    },
    async mounted() {
        if (this.activeDepartment !== null) {
            this.query.department = this.activeDepartment?.id
        }
    },
    methods: {
        addToCategoryList(parentCategory, parentName = '') {
            parentCategory.children.map(cat => {
                const pName = parentName === '' ? cat.name : `${parentName} / ${cat.name}`
                this.categoriesToList.push({
                    id: cat.id,
                    parent: cat.parent,
                    name: pName
                })
                if (cat.children) {
                    this.addToCategoryList(cat, pName)
                }
            })
        },
        applyFilter() {
            this.$emit('apply-filter', this.query)
            this.$refs.filterModal.close()
        },
        resetFilter() {
            this.query = {
                category_id: null,
                text: null,
                participants: {}
            }
            this.$refs.filterModal.close()
            this.$emit('apply-filter', this.query)
        },
        participantsToNums(event, type) {
            this.query.participants[type] = event.map(p => p.id)
            if (this.query.participants[type].length === 0) {
                delete this.query.participants[type]
            }
        },
        exportExcel() {
            this.$emit('export-click', this.query)
            console.log('start queued task')
        }
    }
}
</script>

<style lang="scss" scoped>
.filter {
    position: sticky;
    background: var(--bs-white);
    top: 0;
    z-index: 20;
    border-bottom: 1px solid var(--bs-border-color);
    padding: 4px 10px;
    background: var(--bs-light);

    .buttons {
        .btn {
            margin-left: 8px;
        }
    }

    .basic {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 40px;

        .name {
            cursor: pointer;
            margin-right: 6px;
            font-weight: bold;
            color: #777;

            &.title {
                text-transform: uppercase;
                color: var(--color-text)
            }
        }

        .category {
            width: 390px;
            display: flex;
            align-items: center;
        }
    }
}
</style>
