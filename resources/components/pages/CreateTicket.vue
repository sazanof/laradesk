<template>
    <VSheet
        v-if="activeDepartment && showForm"
        max-width="1200"
        class="fill-height mx-auto pa-4"
        elevation="4"
        @keyup.esc="onKeyUp"
        @click="onKeyUp">
        <VChip
            color="primary"
            rounded="pill"
            prepend-icon="mdi-domain"
            class="badge text-bg-primary">
            {{ activeDepartment.name }}
        </VChip>
        <div class="text-h5 font-weight-bold mt-4">
            {{ $t('New ticket') }}
        </div>
        <VDivider class="my-4" />
        <VContainer
            fluid
            class="pa-0">
            <VRow>
                <VCol
                    cols="12"
                    md="8">
                    <VContainer
                        fluid
                        class="pa-0">
                        <VRow>
                            <VCol
                                cols="12"
                                md="6">
                                <OfficesMultiselect
                                    :label="$t('Office')"
                                    @on-select="onOfficeSelect($event)" />
                            </VCol>
                            <VCol
                                cols="12"
                                md="6">
                                <VTextField
                                    v-if="selectedOffice !== null"
                                    v-show="showCustomLocation"
                                    v-model="location"
                                    :label="$t('Custom location')" />
                                <RoomsMultiselect
                                    v-if="selectedOffice !== null"
                                    v-show="!showCustomLocation"
                                    v-model="room"
                                    :label="$t('Room')"
                                    @on-select="room = $event.id" />
                                <VBtn
                                    v-if="selectedOffice !== null"
                                    size="x-small"
                                    class="small"
                                    @click="toggleLocation">
                                    {{
                                        showCustomLocation ? $t('Switch to room select') : $t('Is the location missing from the list?')
                                    }}
                                </VBtn>
                            </VCol>
                        </VRow>

                        <VRow v-if="selectedOffice !== null">
                            <VCol cols="12">
                                <VSelect
                                    v-model="selectedCategory"
                                    :return-object="true"
                                    item-value="id"
                                    :label="$t('Select category')"
                                    :items="allCategories"
                                    @update:model-value="loadFields">
                                    <template #selection="{item}">
                                        {{ item.raw.name }}
                                    </template>
                                    <template #item="{item, props}">
                                        <VListItem
                                            v-bind="props"
                                            :title="item.raw.name"
                                            :subtitle="item.raw.name !== item.raw.description ? item.raw.description : null" />
                                    </template>
                                </VSelect>
                            </VCol>
                            <VCol
                                v-if="selectedCategory"
                                cols="12">
                                <div
                                    class="input-group input-group-sm">
                                    <VTextField
                                        v-model="subject"
                                        :label="$t('Subject')"
                                        required>
                                        <template #append-inner>
                                            <VIcon
                                                icon="mdi-asterisk"
                                                color="error"
                                                size="small" />
                                        </template>
                                    </VTextField>
                                    <VMenu
                                        v-if="similar && similar?.data?.length > 0"
                                        v-model="similarOpened"
                                        max-height="300">
                                        <template #activator="{props}">
                                            <VBtn
                                                size="small"
                                                density="comfortable"
                                                v-bind="props"
                                                variant="tonal"
                                                class="btn btn-secondary similar-btn">
                                                {{ $t('{count} similar tickets', {count: similar.data.length}) }}
                                            </VBtn>
                                        </template>
                                        <SimilarTickets :tickets="similar" />
                                    </VMenu>
                                </div>
                            </VCol>
                            <VCol
                                v-if="isMobile && selectedCategory"
                                cols="12">
                                <label for="">{{ $t('Observers') }}</label>
                                <UsersMultiselect @on-users-changed="updateObservers($event)" />
                            </VCol>
                            <VCol
                                v-if="isMobile && selectedCategory"
                                cols="12">
                                <label for="">{{ $t('Approvals') }}</label>
                                <UsersMultiselect @on-users-changed="updateApprovals($event)" />
                            </VCol>
                            <VCol
                                v-if="categoryFields"
                                cols="12">
                                <DynamicField
                                    v-for="field in categoryFields"
                                    :key="field.id"
                                    :field="field"
                                    :start-value="findStartValue(field)"
                                    @on-clear="onClearField"
                                    @on-update="onUpdateField" />
                            </VCol>

                            <VCol
                                v-if="selectedCategory"
                                cols="12">
                                <div class="text-subtitle-1 font-weight-bold mb-2">
                                    {{ $t('Content') }}
                                    <VIcon
                                        icon="mdi-asterisk"
                                        color="red" />
                                </div>
                                <div
                                    v-if="quillText === null || quillText?.trim()?.length <=0"
                                    class="text-subtitle-2 mb-1 opacity-70 text-error"
                                    variant="text"
                                    color="red">
                                    {{ $t('Content field is required') }}
                                </div>
                                <Editor
                                    ref="editor"
                                    @on-update="onUpdateContent($event)" />
                            </VCol>
                            <VCol
                                v-if="selectedCategory"
                                cols="12">
                                <FileUploader @on-files-changed="files = $event" />
                            </VCol>
                            <VCol cols="12">
                                <VBtn
                                    v-if="selectedCategory"
                                    prepend-icon="mdi-send"
                                    :loading="loading"
                                    :disabled="disabled || loading"
                                    class="btn btn-primary"
                                    @click="send">
                                    {{
                                        approvals !== null && approvals.length > 0 ? $t('Create and submit for approval') : $t('Send ticket')
                                    }}
                                </VBtn>
                            </VCol>
                            <div
                                v-show="draft.show_alert"
                                class="draft-saved">
                                {{ $t('Draft saved at {date}', {date: draft.saved_at}) }}
                            </div>
                        </VRow>
                    </VContainer>
                </VCol>
                <VCol
                    cols="12"
                    md="4">
                    <VSheet
                        v-if="!isMobile"
                        class="right">
                        <div class="text-h6 font-weight-bold mb-4">
                            {{ $t('Participants') }}
                        </div>
                        <div>
                            <UsersMultiselect
                                :label="$t('Observers')"
                                @on-users-changed="updateObservers($event)" />
                        </div>
                        <div class="mt-4">
                            <UsersMultiselect
                                :label="$t('Approvals')"
                                @on-users-changed="updateApprovals($event)" />
                        </div>
                    </VSheet>
                </VCol>
            </VRow>
        </VContainer>
    </VSheet>
    <VContainer
        v-else
        class="text-center fill-height">
        <VRow>
            <VCol cols="12">
                <div class="text-h4 font-weight-bold mb-4">
                    {{ $t('How can we help?') }}
                </div>
            </VCol>
            <VCol
                v-for="department in departments"
                :key="department.id"
                cols="12"
                md="4"
                sm="6"
                class="pa-4">
                <VCard
                    variant="tonal"
                    hover
                    class="fill-height"
                    :class="{active:department?.id === activeDepartment?.id}"
                    @click="selectDepartment(department)">
                    <VCardText class="fill-height justify-center flex-column align-center d-flex text-wrap">
                        <div class="text-h6 font-weight-bold">
                            {{ department.name }}
                        </div>
                        <div class="text-subtitle-2 opacity-70">
                            {{ department.description }}
                        </div>
                    </VCardText>
                </VCard>
            </VCol>
        </VRow>
    </VContainer>
</template>
<script>
import SimilarTickets from '../chunks/SimilarTickets.vue'
import Editor from '../elements/Editor.vue'
import RoomsMultiselect from '../elements/RoomsMultiselect.vue'
import OfficesMultiselect from '../elements/OfficesMultiselect.vue'
import UsersMultiselect from '../elements/UsersMultiselect.vue'
import DynamicField from '../elements/DynamicField.vue'
import FileUploader from '../chunks/FileUploader.vue'

import debounce from '../../js/helpers/debounce.js'
import { createErrorNotification } from '../../js/helpers/notificationHelper.js'

export default {
    name: 'CreateTicket',
    components: {
        FileUploader,
        Editor,
        DynamicField,
        UsersMultiselect,
        RoomsMultiselect,
        OfficesMultiselect,
        SimilarTickets
    },
    data() {
        return {
            quillText: null,
            draft: {
                show_alert: false,
                saved_at: null,
                data: null
            },
            timer: null,
            subject: '',
            contentText: '',
            categoryFields: [],
            fieldsData: [],
            selectedCategory: null,
            categoriesToList: [],
            category: null,
            location: null,
            selectedOffice: null,
            room: null,
            observers: null,
            approvals: null,
            loading: false,
            activeDepartment: null,
            showForm: false,
            showCustomLocation: false,
            files: null,
            debounceSimilar: debounce(this.getSimilar, 500),
            similar: null,
            similarOpened: false

        }
    },
    computed: {
        copyTicketData() {
            return this.$store.getters['getCopyTicketData']
        },
        isMobile() {
            return this.$store.getters['isMobile']
        },
        appWidth() {
            return this.$store.getters['getAppWidth']
        },
        disabled() {
            let failed = false
            // if (this.room === null) {
            //     failed = true
            // }
            if (this.selectedCategory === null) {
                failed = true
            }
            if (this.subject.length < 3 || this.contentText < 3 || this.quillText?.trim()?.length < 3) {
                failed = true
            }
            return failed
        },
        departments() {
            return this.$store.getters['getActiveDepartments']
        },
        user() {
            return this.$store.getters['getUser']
        },
        userId() {
            return this.user.id
        },
        categories() {
            return this.$store.getters['getCategories']
        },
        offices() {
            return this.$store.getters['getOffices']
        },
        allCategories() {
            // eslint-disable-next-line vue/no-side-effects-in-computed-properties
            this.categoriesToList = []
            this.categories.map(cat => {
                this.categoriesToList.push({
                    id: cat.id,
                    parent: cat.parent,
                    name: cat.name,
                    description: cat.description
                })
                if (cat.children) {
                    this.addToCategoryList(cat, cat.name)
                }
            })
            return this.categoriesToList
        },
        ticketData() {
            return {
                subject: this.subject,
                content: this.contentText,
                user_id: this.userId,
                room_id: this.room,
                custom_location: this.location,
                department_id: this.activeDepartment.id,
                approvals: this.approvals !== null ? this.approvals.map(o => o.id) : null,
                observers: this.observers !== null ? this.observers.map(o => o.id) : null,
                category_id: this.selectedCategory?.id,
                office_id: this.selectedOffice?.id,
                formData: this.fieldsData,
                files: this.files
            }
        }
    },
    watch: {

        selectedCategory() {
            this.subject = ''
            this.contentText = ''
            this.approvals = null
            this.observers = null
            this.fieldsData = []
            this.files = null
        },
        subject() {
            if (this.subject.length >= 3) {
                this.debounceSimilar()
            } else {
                this.similar = null
            }
        }
    },
    async created() {
        await this.getOffices()
        // Fill with copy data
        if (this.copyTicketData !== null) {
            this.activeDepartment = this.departments.find(d => d.id === this.copyTicketData.department_id)
            await this.openTicketForm()
            console.log(this.categories, this.copyTicketData.category_id)
            // TODO find category recursive
            this.selectedCategory = this.findCategoryRecursive(this.copyTicketData.category_id, this.categories)
            console.log(this.selectedCategory)
            //await this.loadFields(this.selectedCategory)
            this.subject = this.copyTicketData.subject
            this.contentText = this.copyTicketData.content
            this.$nextTick(() => {
                //this.$refs.editor.setContent(this.copyTicketData.content)
            })
        }
        this.emitter.on('on-create-ticket-navigate', () => {
            this.selectedCategory = null
            this.activeDepartment = null
            this.showForm = false
        })
    },
    unmounted() {
        clearTimeout(this.timer)
    },
    methods: {
        onUpdateContent(e) {
            this.contentText = e
            this.quillText = this.$refs?.editor?.getContent()
        },
        findCategoryRecursive(id, items) {
            const cat = items.find(c => {
                console.log('find with id ' + c.id)
                if (c.id === id) {
                    console.log('founded id ' + c.id)
                    return true
                } else if (c.children?.length > 0) {
                    return this.findCategoryRecursive(id, c.children)
                }
                return false
            })
            console.log(cat)
            return cat
        },
        async getSimilar() {
            this.similar = await this.$store.dispatch('getSimilarTickets', {
                subject: this.subject,
                userId: this.userId
            })
            console.log(this.subject, this.userId)
        },
        onKeyUp() {
            this.emitter.emit('on.ticket.form.click')
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
        async loadFields(category) {
            // clear form
            this.draft.show_alert = false
            if (category !== null && category?.hasOwnProperty('id')) {
                this.categoryFields = await this.$store.dispatch('getCategoryFields', category?.id)
                //TODO get draft
            } else {
                this.categoryFields = []
                clearInterval(this.timer)
                //TODO delete draft
            }
        },
        getOffices() {
            let offices = this.$store.getters['getOffices']
            if (offices === null) {
                this.$store.dispatch('getOffices').then(() => {
                    offices = this.$store.getters['getOffices']
                })

            }
            return offices
        },
        findStartValue(field) {
            // console.log(field)
            return this.copyTicketData?.fields.find(cf => cf.field_id === field.field_id)?.content
        },
        onClearField(data) {
            this.fieldsData = this.fieldsData.filter(f => f.name !== `field_${data.field.id}`)
        },
        onUpdateField(data) {
            const field = data.field
            const value = data.value
            const existing = this.fieldsData.find(_field => _field.category_field_id === field.category_field_id)
            if (data.value === null) {
                this.fieldsData = this.fieldsData.filter(f => {
                    return f !== existing
                })
                return false
            }
            if (existing === undefined) {
                this.fieldsData.push({
                    name: `field_${field.id}`,
                    category_field_id: field.category_field_id,
                    value: value,
                    required: field.required
                })
            } else {
                existing.value = value
            }
        },
        async send() {
            this.loading = true
            const data = this.ticketData
            const res = await this.$store.dispatch('sendTicket', data).catch(e => {
                Object.values(e.response.data.errors)?.map(errs => {
                    console.log(errs)
                    errs.map(e => {
                        this.$store.commit('addNotification', createErrorNotification(e))
                    })
                })
            }).finally(() => {
                this.loading = false
            })
            if (res.id) {
                this.subject = ''
                this.contentText = ''
                this.loading = false
                this.activeDepartment = null
                this.$router.push(`/user/tickets/${res.id}`)
            }

        },
        updateObservers(observers) {
            this.observers = observers
        },

        updateApprovals(approvals) {
            this.approvals = approvals
        },

        toggleLocation() {
            this.showCustomLocation = !this.showCustomLocation
            this.room = null
            this.location = null
            this.emitter.emit('clear-room-value')
        },

        selectDepartment(d) {
            if (this.activeDepartment !== null && this.activeDepartment.id === d.id) {
                this.activeDepartment = null
            } else {
                this.activeDepartment = d
                this.openTicketForm()
            }
        },
        onOfficeSelect(event) {
            this.selectedOffice = event
            this.room = null
            this.emitter.emit('clear-room-value')
        },
        async openTicketForm() {
            await this.$store.dispatch('getTicketCategories', this.activeDepartment.id)
            this.showForm = true

            this.selectedOffice = this.offices.find(o => o.id === this.user.office_id)
            this.room = this.user.room_id === -1 ? null : this.user.room_id
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket-form {
    display: flex;
    width: 100%;
    background: var(--bs-light);
    justify-content: center;

    .small {
        font-size: var(--font-small);
        color: var(--bs-purple);
        padding: 4px 0;
        cursor: pointer;
    }

    .main {
        height: calc(100vh - var(--header-height));
        width: calc(100% - 320px - 4px);
        padding: var(--padding-box);
        background: var(--bs-white);
        max-width: 680px;
        box-shadow: -20px 0 20px rgba(0, 0, 0, 0.1);
    }

    .right {
        padding: calc(var(--padding-box) * 1) var(--padding-box) var(--padding-box) var(--padding-box);
        width: 320px;
        height: calc(100vh - var(--header-height));
        background: var(--bs-white);
        box-shadow: 20px 0 20px rgba(0, 0, 0, 0.1);
        border-left: 4px solid var(--bs-light);
    }

    &.is-mobile {
        flex-direction: column;

        .main, .right {
            width: 100%;
            box-shadow: none;
            padding: var(--padding-box);
        }
    }
}

.ticket-departments {
    h3 {
        margin: 30px 0;
        text-align: center;
    }

    .departments-list {
        height: calc(100vh - var(--header-height));
        display: grid;
        grid-template-columns: repeat(5, 1fr);


        .department {
            outline: 1px solid var(--bs-border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--padding-box);
            background: var(--bs-white);
            transition: var(--transition-duration);
            cursor: pointer;
            flex-direction: column;

            &:hover, &.active {
                background: var(--bs-light);
            }

            .name {
                font-weight: bold;
                text-transform: uppercase;
            }
        }
    }

    &.is-mobile {
        .departments-list {
            grid-template-columns: repeat(3, 1fr);
        }

        &.small {
            .departments-list {
                grid-template-columns: repeat(1, 1fr);
            }
        }
    }
}

.draft-saved {
    display: inline-block;
    margin-left: 16px;
    color: var(--bs-gray);
    font-style: italic;
}

.similar-btn {
    border-radius: 0 var(--bs-border-radius) var(--bs-border-radius) 0;
    padding: 3px 8px;
}


</style>
