<template>
    <div class="filter">
        <div class="basic">
            <div class="name">
                <div
                    v-if="title"
                    class="pagetitle btn btn-purple"
                    @click="applyFilter">
                    <Loading
                        v-if="loading"
                        :size="20" />
                    <RefreshIcon
                        v-else
                        :size="20" />
                    {{ title }}
                </div>
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
            <div class="mb-2">
                <button
                    class="btn"
                    :class="!searchByNumber ? 'btn-purple' : 'btn-link'"
                    @click="deleteSearchByNumber">
                    {{ $t('Extended search') }}
                </button>
                <button
                    class="btn ms-2"
                    :class="searchByNumber ? 'btn-purple' : 'btn-link'"
                    @click="searchByNumber = true">
                    {{ $t('By number') }}
                </button>
            </div>
            <div
                v-if="searchByNumber"
                class="by_number">
                <div class="form-group">
                    <label>{{ $t('Search by number') }}</label>
                    <input
                        v-model="query.number"
                        type="number"
                        class="form-control">
                </div>
            </div>
            <div
                v-else
                class="by_others">
                <div class="form-group">
                    <label for="">{{ $t('Date range') }}</label>
                    <div class="row">
                        <div class="col-md-6">
                            <VueDatePicker
                                v-model="query.start"
                                auto-apply
                                :enable-time-picker="false"
                                :locale="$i18n.locale"
                                format="dd.MM.yyyy" />
                        </div>
                        <div class="col-md-6">
                            <VueDatePicker
                                v-model="query.end"
                                auto-apply
                                :enable-time-picker="false"
                                :locale="$i18n.locale"
                                format="dd.MM.yyyy"
                                :min-date="query.start" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex">
                            <div class="form-check form-switch me-3">
                                <input
                                    id="date_created_at"
                                    v-model="query.dateSearchField"
                                    class="form-check-input"
                                    type="checkbox"
                                    value="created_at">
                                <label
                                    class="form-check-label"
                                    for="date_created_at">{{ $t('Created at') }}</label>
                            </div>

                            <div class="form-check form-switch me-3">
                                <input
                                    id="date_solved_at"
                                    v-model="query.dateSearchField"
                                    class="form-check-input"
                                    type="checkbox"
                                    value="solved_at">
                                <label
                                    class="form-check-label me-3"
                                    for="date_solved_at">{{ $t('Solved at') }}</label>
                            </div>

                            <div class="form-check form-switch">
                                <input
                                    id="date_closed_at"
                                    v-model="query.dateSearchField"
                                    class="form-check-input"
                                    type="checkbox"
                                    value="closed_at">
                                <label
                                    class="form-check-label"
                                    for="date_closed_at">{{ $t('Closed at') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">{{ $t('Search text') }}</label>
                    <input
                        v-model="query.text"
                        type="text"
                        class="form-control">
                </div>
                <div
                    v-if="activeDepartment"
                    class="form-group">
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
                    <div
                        v-if="filter['criteria'] === 'sent' || (admin && filter['criteria'] === 'all' )"
                        class="form-group sub">
                        <label
                            for=""
                            class="w-100">{{ $t('Status') }}</label>
                        <div
                            v-for="cr in subCriteria"
                            :key="cr"
                            class="sub-criteria">
                            <label
                                :for="`ch_cr_${cr}`"
                                class="fw-normal">
                                <input
                                    :id="`ch_cr_${cr}`"
                                    v-model="query.subCriteria"
                                    type="checkbox"
                                    :value="cr"> {{ $t(`dashboard_${cr}`) }}
                            </label>
                        </div>
                    </div>
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
            </div>

            <template #footer-actions>
                <button
                    v-if="!searchByNumber"
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

import VueDatePicker from '@vuepic/vue-datepicker'
import UsersMultiselect from '../elements/UsersMultiselect.vue'
import FilterIcon from 'vue-material-design-icons/Filter.vue'
import RefreshIcon from 'vue-material-design-icons/Refresh.vue'
import Loading from '../elements/Loading.vue'
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
        MicrosoftExcelIcon,
        VueDatePicker,
        Loading,
        RefreshIcon
    },
    props: {
        title: {
            type: String,
            default: null
        },
        filter: {
            type: Object,
            required: true
        },
        loading: {
            type: Boolean,
            default: false
        },
        admin: {
            type: Boolean,
            default: false
        }
    },
    emits: [ 'apply-filter', 'export-click' ],
    data() {
        return {
            searchByNumber: false,
            open: false,
            categoriesToList: [],
            selectedCategory: null,
            query: {
                number: null,
                category_id: null,
                text: null,
                participants: {},
                start: null,
                end: null,
                subCriteria: [],
                dateSearchField: [
                    'created_at'
                ]
            },
            subCriteriaAdmin: [
                'new',
                'in-work',
                'waiting',
                'solved',
                'closed',
                'in-approval',
                'approved',
                'i-am-approval',
                'my'
            ],
            subCriteriaUser: [
                'waiting',
                'solved',
                'closed',
                'in-approval',
                'i-am-approval',
                'approved'
            ]
        }
    },
    computed: {
        subCriteria() {
            return this.admin ? this.subCriteriaAdmin : this.subCriteriaUser
        },
        additionalCriteria() {
            return this.$store.getters['getAdditionalCriteria']
        },
        filterEnabled() {
            return this.query.category_id !== null
                || (this.query.number !== null && this.searchByNumber)
                || this.query.subCriteria.length > 0
                || this.query.dateSearchField.indexOf('closed_at') !== -1
                || this.query.dateSearchField.indexOf('solved_at') !== -1
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
                    if (department.id === this.activeDepartment.id) {
                        this.categoriesToList.push({
                            label: department.name,
                            options: department.categories
                        })
                    }
                })
            }
            return this.categoriesToList
        }
    },
    watch: {
        activeDepartment() {
            this.categoriesToList = []
        }
    },
    async mounted() {
        if (this.activeDepartment !== null) {
            this.query.department = this.activeDepartment?.id
        }

        this.emitter.on('on-reset-filter', () => {
            this.resetFilter()
        })

        this.query.subCriteria = [ null, 'sent', 'all' ].indexOf(this.additionalCriteria) === -1
            ? [ this.additionalCriteria ]
            : []

        this.emitter.on('after-department-changed', async (d) => {
            this.query.department = d.id
            this.applyFilter()
        })

        this.applyFilter()


    },
    unmounted() {
        this.emitter.off('after-department-changed')
        this.emitter.off('on-reset-filter')
    },
    methods: {
        deleteSearchByNumber() {
            this.searchByNumber = false
            this.query.number = null
        },
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
            if (this.searchByNumber === true && this.number !== null) {
                this.$emit('apply-filter', {
                    number: this.query.number
                })
            } else {
                this.$emit('apply-filter', this.query)
            }

            this.$refs.filterModal.close()
        },
        resetFilter() {
            this.query = {
                number: null,
                category_id: null,
                text: null,
                participants: {},
                start: null,
                end: null,
                subCriteria: [],
                dateSearchField: [
                    'created_at'
                ]
            }
            this.searchByNumber = false
            this.$refs.filterModal.close()
            this.$emit('apply-filter', this.query)
            this.$store.commit('setAdditionalCriteria', null)
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

    .pagetitle {
        background: var(--bs-purple);
        color: var(--bs-white);
        opacity: 0.7;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 40px;
        padding: 4px var(--padding-box);
        margin-right: 16px;
        position: relative;
    }

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
            display: flex;
            align-items: center;
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

.sub {
    .sub-criteria {
        display: inline-block;
        width: 50%;
    }
}
</style>
