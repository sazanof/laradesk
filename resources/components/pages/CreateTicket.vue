<template>
    <div
        v-if="activeDepartment && showForm"
        class="ticket-form"
        :class="{'is-mobile': isMobile, 'small':appWidth < 600}">
        <SimpleBar class="main">
            <div class="badge text-bg-primary">
                {{ activeDepartment.name }}
            </div>
            <h3>{{ $t('New ticket') }}</h3>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ $t('Office') }}</label>
                        <OfficesMultiselect />
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
                            @on-select="room === $event.id" />
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
            <div class="form-group mt-3">
                <label for="">{{ $t('Subject') }}</label>
                <input
                    v-model="subject"
                    type="text"
                    required
                    class="form-control">
            </div>
            <div
                v-if="isMobile"
                class="form-group mt-3">
                <label for="">{{ $t('Observers') }}</label>
                <UsersMultiselect @on-users-changed="updateObservers($event)" />
            </div>
            <div
                v-if="isMobile"
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
                    @on-update="onUpdateField" />
            </div>

            <div class="form-group mt-3">
                <label for="">{{ $t('Content') }}</label>
                <Editor @on-update="contentText = $event" />
            </div>
            <button
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

const toast = useToast()
export default {
    name: 'CreateTicket',
    components: {
        SimpleBar,
        Editor,
        DynamicField,
        SendIcon,
        MultiselectElement,
        UsersMultiselect,
        Loading,
        RoomsMultiselect,
        OfficesMultiselect
    },
    data() {
        return {
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
            showCustomLocation: false
        }
    },
    computed: {
        isMobile() {
            return this.$store.getters['isMobile']
        },
        appWidth() {
            return this.$store.getters['getAppWidth']
        },
        disabled() {
            let failed = false
            if (this.subject.length < 3 || this.contentText < 10) {
                return true
            }
            const errorFields = this.categoryFields.filter(f => f.required === 1)
            errorFields.map(err => {
                const existing = this.fieldsData.find(_f => _f.category_field_id === err.category_field_id)
                if (existing === undefined) {
                    failed = true
                } else if (existing.value === '' || existing.value === null) {
                    failed = true
                }
            })
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
        }
    },
    async created() {
        await this.getOffices()
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
        async loadFields(category) {
            this.fieldsData = []
            this.categoryFields = await this.$store.dispatch('getCategoryFields', category.id)
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
            const data = {
                subject: this.subject,
                content: this.contentText,
                user_id: this.userId,
                room_id: this.room,
                custom_location: this.location,
                department_id: this.activeDepartment.id,
                approvals: this.approvals !== null ? this.approvals.map(o => o.id) : null,
                observers: this.observers !== null ? this.observers.map(o => o.id) : null,
                category_id: this.selectedCategory.id,
                office_id: this.selectedOffice.id,
                formData: this.fieldsData
            }
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


</style>
