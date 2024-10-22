<template>
    <div
        v-if="activeDepartment && showForm"
        class="ticket-form"
        :class="{'is-mobile': isMobile, 'small':appWidth < 600}"
        @keyup.esc="onKeyUp"
        @click="onKeyUp">
        <SimpleBar class="main">
            <div class="badge text-bg-primary">
                {{ activeDepartment.name }}
            </div>
            <h3>{{ $t('New ticket') }}</h3>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ $t('Office') }}</label>
                        <OfficesMultiselect @on-select="onOfficeSelect($event)" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ showCustomLocation ? $t('Custom location') : $t('Room') }}</label>
                        <input
                            v-show="showCustomLocation"
                            v-model="location"
                            type="text"
                            class="form-control">
                        <RoomsMultiselect
                            v-show="!showCustomLocation"
                            @on-select="room = $event.id" />
                        <div
                            class="small"
                            @click="toggleLocation">
                            {{
                                showCustomLocation ? $t('Switch to room select') : $t('Is the location missing from the list?')
                            }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <label for="">{{ $t('Select category') }}</label>
                <MultiselectElement
                    v-model="selectedCategory"
                    :object="true"
                    label="name"
                    value-prop="id"
                    track-by="id"
                    :options="allCategories"
                    @change="loadFields" />
            </div>
            <div
                v-if="selectedCategory"
                class="form-group mt-3">
                <label for="">{{ $t('Subject') }}</label>

                <div
                    class="input-group input-group-sm">
                    <input
                        v-model="subject"
                        type="text"
                        required
                        class="form-control">
                    <VDropdown
                        v-if="similar && similar?.data?.length > 0"
                        :auto-hide="true"
                        placement="auto">
                        <template #popper>
                            <SimilarTickets :tickets="similar" />
                        </template>
                        <button

                            class="btn btn-secondary similar-btn">
                            {{ $t('{count} similar tickets', {count: similar.data.length}) }}
                        </button>
                    </VDropdown>
                </div>
            </div>
            <div
                v-if="isMobile && selectedCategory"
                class="form-group mt-3">
                <label for="">{{ $t('Observers') }}</label>
                <UsersMultiselect @on-users-changed="updateObservers($event)" />
            </div>
            <div
                v-if="isMobile && selectedCategory"
                class="form-group mt-3">
                <label for="">{{ $t('Approvals') }}</label>
                <UsersMultiselect @on-users-changed="updateApprovals($event)" />
            </div>
            <div
                v-if="categoryFields"
                class="custom-fields">
                <DynamicField
                    v-for="field in categoryFields"
                    :key="field.id"
                    :field="field"
                    :start-value="findStartValue(field)"
                    @on-clear="onClearField"
                    @on-update="onUpdateField" />
            </div>

            <div
                v-if="selectedCategory"
                class="form-group mt-3">
                <label for="">{{ $t('Content') }}</label>
                <Editor
                    ref="editor"
                    @on-update="contentText = $event" />
            </div>
            <div
                v-if="selectedCategory"
                class="form-group mt-3">
                <FileUploader @on-files-changed="files = $event" />
            </div>
            <button
                v-if="selectedCategory"
                :disabled="disabled || loading"
                class="btn btn-primary"
                @click="send">
                <Loading v-if="loading" />
                <SendIcon
                    v-else
                    :size="18" />
                {{
                    approvals !== null && approvals.length > 0 ? $t('Create and submit for approval') : $t('Send ticket')
                }}
            </button>
            <div
                v-show="draft.show_alert"
                class="draft-saved">
                <FountainPenTipIcon :size="18" />
                {{ $t('Draft saved at {date}', {date: draft.saved_at}) }}
            </div>
        </SimpleBar>
        <div
            v-if="!isMobile"
            class="right">
            <h3>{{ $t('Participants') }}</h3>
            <div class="form-group">
                <label for="">{{ $t('Observers') }}</label>
                <UsersMultiselect @on-users-changed="updateObservers($event)" />
            </div>
            <div class="form-group">
                <label for="">{{ $t('Approvals') }}</label>
                <UsersMultiselect @on-users-changed="updateApprovals($event)" />
            </div>
        </div>
    </div>
    <div
        v-else
        :class="{'is-mobile': isMobile, 'small':appWidth < 600}"
        class="ticket-departments text-center">
        <!--        <h3>{{ $t('Choose department') }}</h3>-->
        <div class="departments-list">
            <div
                v-for="department in departments"
                :key="department.id"
                class="department"
                :class="{active:department?.id === activeDepartment?.id}"
                @click="selectDepartment(department)">
                <div class="name">
                    {{ department.name }}
                </div>
                <div class="description">
                    {{ department.description }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import SimilarTickets from '../chunks/SimilarTickets.vue'
import SimpleBar from 'simplebar-vue'
import Loading from '../elements/Loading.vue'
import Editor from '../elements/Editor.vue'
import { useToast } from 'vue-toastification'
import RoomsMultiselect from '../elements/RoomsMultiselect.vue'
import OfficesMultiselect from '../elements/OfficesMultiselect.vue'
import UsersMultiselect from '../elements/UsersMultiselect.vue'
import SendIcon from 'vue-material-design-icons/Send.vue'
import DynamicField from '../elements/DynamicField.vue'
import MultiselectElement from '../elements/MultiselectElement.vue'
import ToastMessages from '../chunks/ToastMessages.vue'
import FountainPenTipIcon from 'vue-material-design-icons/FountainPenTip.vue'
import FileUploader from '../chunks/FileUploader.vue'

import debounce from '../../js/helpers/debounce.js'

const toast = useToast()
export default {
    name: 'CreateTicket',
    components: {
        FileUploader,
        SimpleBar,
        Editor,
        DynamicField,
        SendIcon,
        MultiselectElement,
        UsersMultiselect,
        Loading,
        RoomsMultiselect,
        OfficesMultiselect,
        FountainPenTipIcon,
        SimilarTickets
    },
    data() {
        return {
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
            similar: null

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
            if (this.selectedCategory === null) {
                failed = true
            }
            if (this.subject.length < 3 || this.contentText < 3) {
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
            this.categoriesToList = []
            this.categories.map(cat => {
                this.categoriesToList.push({
                    id: cat.id,
                    parent: cat.parent,
                    name: cat.name
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
            this.selectedCategory = this.categories.find(c => c.id === this.copyTicketData.category_id)
            this.loadFields(this.selectedCategory)
            this.$nextTick(() => {
                this.subject = this.copyTicketData.subject
                this.contentText = this.copyTicketData.content
                this.$refs.editor.setContent(this.copyTicketData.content)
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
                toast.error({
                    component: ToastMessages,
                    props: {
                        messages: e.response.data.errors
                    }
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
            height: auto;
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
