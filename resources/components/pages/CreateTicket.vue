<template>
    <div class="ticket-form">
        <div class="main">
            <h3>{{ $t('New ticket') }}</h3>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ $t('Office') }}</label>
                        <MultiselectElement
                            v-model="selectedOffice"
                            :options="offices"
                            :object="true"
                            label="name"
                            value-prop="id"
                            track-by="id" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ $t('Room') }}</label>
                        <input
                            v-model="room"
                            type="number"
                            class="form-control">
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
                <Editor @on-update="content = $event" />
            </div>
            <button
                :disabled="disabled"
                class="btn btn-primary"
                @click="send">
                <SendIcon :size="18" />
                {{
                    approvals !== null && approvals.length > 0 ? $t('Create and submit for approval') : $t('Send ticket')
                }}
            </button>
        </div>
        <div class="right">
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
</template>
<script>
import Editor from '../ elements/Editor.vue'
import { useToast } from 'vue-toastification'
import UsersMultiselect from '../ elements/UsersMultiselect.vue'
import SendIcon from 'vue-material-design-icons/Send.vue'
import DynamicField from '../ elements/DynamicField.vue'
import MultiselectElement from '../ elements/MultiselectElement.vue'
import ToastMessages from '../chunks/ToastMessages.vue'

const toast = useToast()
export default {
    name: 'CreateTicket',
    components: {
        Editor,
        DynamicField,
        SendIcon,
        MultiselectElement,
        UsersMultiselect
    },
    data() {
        return {
            subject: '',
            content: '',
            categoryFields: [],
            fieldsData: [],
            selectedCategory: null,
            categoriesToList: [],
            category: null,
            selectedOffice: null,
            room: null,
            observers: null,
            approvals: null,
            loading: false
        }
    },
    computed: {
        disabled() {
            let failed = false
            if (this.subject.length < 3 || this.content < 10) {
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
        await this.$store.dispatch('getTicketCategories')
        this.selectedOffice = this.offices.find(o => o.id === this.user.office_id)
        this.room = this.user.room_id === -1 ? null : this.user.room_id
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
                content: this.content,
                user_id: this.userId,
                room_id: this.room,
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
            })
            if (res.id) {
                this.subject = ''
                this.content = ''
                this.loading = false
                this.$router.push(`/user/tickets/${res.id}`)
            }

        },
        updateObservers(observers) {
            this.observers = observers
        },

        updateApprovals(approvals) {
            this.approvals = approvals
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket-form {
    display: flex;
    width: 100%;

    .main {
        width: calc(100% - 320px);
        padding: var(--padding-box);
    }

    .right {
        padding: var(--padding-box);
        width: 320px;
        height: calc(100vh - var(--header-height));
        background: var(--bs-light);
    }
}
</style>
