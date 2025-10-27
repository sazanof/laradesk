<template>
    <VSheet>
        <VCard
            rounded="0"
            :color="$vuetify.theme.name === 'light' ? 'grey-lighten-3' : 'grey-darken-3'"
            variant="flat">
            <template #title>
                {{ filterEnabled ? $t('Filter results') : $t('All results') }}
            </template>
            <template #prepend>
                <VBtn
                    v-if="title"
                    size="small"
                    class="mr-4"
                    rounded="pill"
                    color="deep-orange"
                    :loading="loading"
                    prepend-icon="mdi-refresh"
                    :text="title"
                    @click="applyFilter" />
            </template>
            <template #append>
                <VBtn
                    v-if="filterEnabled"
                    size="small"
                    class="ml-2"
                    color="red"
                    rounded="pill"
                    variant="text"
                    prepend-icon="mdi-restore"
                    @click.prevent="resetFilter">
                    {{ $t('reset') }}
                </VBtn>
                <VBtn
                    size="small"
                    rounded="pill"
                    variant="text"
                    density="comfortable"
                    class="mr-2"
                    icon="mdi-code-json"
                    @click="showDiag = !showDiag" />
                <VBtn
                    size="small"
                    rounded="pill"
                    prepend-icon="mdi-filter"
                    :text="$t('Filter')"
                    @click="$refs.filterModal.open()" />

                <VBtn
                    size="small"
                    rounded="pill"
                    class="ml-2"
                    prepend-icon="mdi-microsoft-excel"
                    :text="$t('XLSX')"
                    color="green"
                    @click="exportExcel" />
            </template>
        </VCard>
        <VCard
            v-if="showDiag"
            :title="$t('Debug data')"
            class="pa-4 p-2">
            <VCardText>
                <code>
                    {{ query }}
                </code>
            </VCardText>
        </VCard>
        <ModalDialog
            ref="filterModal"
            :title="$t('Filter')">
            <VTabs
                v-model="tab"
                density="compact"
                color="deep-purple"
                class="mb-2"
                align-tabs="center">
                <VTab
                    prepend-icon="mdi-magnify"
                    value="extended"
                    @click="deleteSearchByNumber">
                    {{ $t('Extended search') }}
                </VTab>
                <VTab
                    prepend-icon="mdi-pound-box"
                    value="number"
                    @click="searchByNumber = true">
                    {{ $t('By number') }}
                </VTab>
            </VTabs>
            <VTabsWindow v-model="tab">
                <VTabsWindowItem value="number">
                    <VSheet class="py-4">
                        <VTextField
                            v-model.number="query.number"
                            prepend-inner-icon="mdi-pound-box"
                            type="number"
                            :label="$t('Search by number')" />
                    </VSheet>
                </VTabsWindowItem>
                <VTabsWindowItem
                    value="extended">
                    <VSheet>
                        <VContainer class="pa-0">
                            <VRow>
                                <VCol cols="12">
                                    <VSheet class="text-h6">
                                        {{ $t('Date range') }}
                                    </VSheet>
                                </VCol>
                                <VCol
                                    cols="12"
                                    md="6">
                                    <VDateInput
                                        v-model="query.start"
                                        clearable
                                        input-format="dd.mm.yyyy"
                                        color="default"
                                        :placeholder="$t('DD.MM.YYYY')"
                                        :label="$t('From')" />
                                </VCol>
                                <VCol
                                    cols="12"
                                    md="6">
                                    <VDateInput
                                        v-model="query.end"
                                        clearable
                                        color="default"
                                        :placeholder="$t('DD.MM.YYYY')"
                                        :label="$t('To')" />
                                </VCol>
                            </VRow>
                            <VRow>
                                <VCol>
                                    <VSheet>
                                        <VCheckboxBtn
                                            id="date_created_at"
                                            v-model="query.dateSearchField"
                                            class="mr-4"
                                            inline
                                            value="created_at"
                                            :label="$t('Created at')" />
                                        <VCheckboxBtn
                                            id="date_solved_at"
                                            v-model="query.dateSearchField"
                                            class="mr-4"
                                            inline
                                            :label="$t('Solved at')"
                                            value="solved_at" />
                                        <VCheckboxBtn
                                            id="date_closed_at"
                                            v-model="query.dateSearchField"
                                            class="mr-4"
                                            inline
                                            :label="$t('Closed at')"
                                            value="closed_at" />
                                    </VSheet>
                                </VCol>
                            </VRow>


                            <VRow>
                                <VCol>
                                    <VTextField
                                        v-model="query.text"
                                        clearable
                                        prepend-inner-icon="mdi-text"
                                        :label="$t('Search text')" />
                                </VCol>
                            </VRow>
                        </VContainer>
                        <div
                            v-if="activeDepartment">
                            <VSelect
                                v-model="category"
                                prepend-inner-icon="mdi-tag"
                                class="mt-4"
                                clearable
                                :label="$t('Category')"
                                :items="allCategories"
                                return-object
                                item-title="name"
                                @update:model-value="query.category_id = $event?.id" />
                        </div>
                        <VSelect
                            v-if="selectedCategory"
                            v-model="query.fields"
                            class="mt-4"
                            :label="$t('Fields')"
                            multiple
                            chips
                            closable-chips
                            :items="selectedCategory.fields_only"
                            item-title="name"
                            item-value="field_id" />

                        <VSheet
                            v-if="open">
                            <VSheet
                                v-if="filter['criteria'] === 'sent' || (admin && filter['criteria'] === 'all' )">
                                <div
                                    class="text-subtitle mt-2">
                                    {{ $t('Status') }}
                                </div>
                                <VCheckboxBtn
                                    v-for="cr in subCriteria"
                                    :key="cr"
                                    v-model="query.subCriteria"
                                    density="comfortable"
                                    class="mr-2"
                                    inline
                                    :label="$t(`dashboard_${cr}`)"
                                    :value="cr" />
                            </VSheet>
                            <UsersMultiselect
                                class="mb-4"
                                :label="$t('Requester')"
                                @on-users-changed="participantsToNums($event,'requesters')" />

                            <UsersMultiselect
                                class="mb-4"
                                :label="$t('Approvals')"
                                @on-users-changed="participantsToNums($event,'approvals')" />

                            <UsersMultiselect
                                class="mb-4"
                                :label="$t('Observers')"
                                @on-users-changed="participantsToNums($event,'observers')" />
                        </VSheet>
                    </VSheet>
                </VTabsWindowItem>
            </VTabsWindow>

            <template #actions>
                <VBtn
                    v-if="!searchByNumber"
                    :text="open ? $t('Less parameters') : $t('More parameters')"
                    :prepend-icon="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                    @click="open = !open" />
                <VBtn
                    prepend-icon="mdi-filter-check"
                    :text="$t('Apply filter')"
                    @click="applyFilter" />
            </template>
        </ModalDialog>
    </VSheet>
</template>

<script>
import { VDateInput } from 'vuetify/labs/VDateInput'

import UsersMultiselect from '../elements/UsersMultiselect.vue'
import ModalDialog from '../chunks/ModalDialog.vue'

export default {
    name: 'TicketsFilter',
    components: {
        ModalDialog,
        UsersMultiselect,
        VDateInput
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
            tab: 'extended',
            showDiag: false,
            category: null,
            searchByNumber: false,
            open: false,
            categoriesToList: [],
            query: {
                fields: null,
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
                || this.query?.start !== null
                || this.query?.end !== null
                || this.query?.number !== null
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
        activeDepartment() {
            return this.$store.getters['getActiveDepartment']
        },
        allCategories() {
            if (this.departments) {
                this.departments.map(department => {
                    if (department.id === this.activeDepartment.id) {
                        this.categoriesToList = department.categories
                    }
                })
            }
            return this.categoriesToList
        },
        selectedCategory() {
            return this.activeDepartment?.categories?.find(c => this.query.category_id === c.id)
        }
    },
    watch: {
        activeDepartment() {
            this.categoriesToList = []
        },
        filter: {
            deep: true,
            handler(v) {
                if (this.query?.criteria !== v.criteria) {
                    this.query = v
                    this.restoreTicketsFilterFromLocalStorage()
                    this.category = this.selectedCategory
                    this.$emit('apply-filter', this.query)
                    console.log(this.query.criteria, this.query.text)
                }
            }
        }
    },
    async mounted() {
        this.query = { ...this.query, ...this.filter }
        this.restoreTicketsFilterFromLocalStorage()
        this.category = this.selectedCategory
        if (this.activeDepartment !== null) {
            this.query.department = this.activeDepartment?.id
        }

        this.emitter.on('on-reset-filter', () => {
            this.resetFilter()
        })

        this.emitter.on('on-menu-click', () => {
            //this.resetFilter()
            //this.setTicketsFilterToLocalStorage()
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
        setTicketsFilterToLocalStorage() {
            localStorage.setItem(`tickets_filter_${this.query.criteria}`, JSON.stringify(this.query ?? {}))
        },
        restoreTicketsFilterFromLocalStorage() {
            try {
                if (localStorage.hasOwnProperty(`tickets_filter_${this.query.criteria}`)) {
                    const filter = localStorage.getItem(`tickets_filter_${this.query.criteria}`)
                    this.query = JSON.parse(filter)
                    console.log('Filter restoring success.', `tickets_filter_${this.query.criteria}`, this.filter)
                }

            } catch (e) {
                console.log('Error parsing filter! Skipping...')
            }
        },
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
            this.setTicketsFilterToLocalStorage()

            this.$refs.filterModal.close()
        },
        resetFilter() {
            this.query = {
                ...{ criteria: this.query.criteria },
                ...{
                    page: 1,
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
            }
            this.searchByNumber = false
            this.category = null
            this.$refs.filterModal?.close()
            this.$store.commit('setAdditionalCriteria', null)
            this.setTicketsFilterToLocalStorage()
            this.applyFilter()

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
</style>
