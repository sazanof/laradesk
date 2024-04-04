<template>
    <div class="form-field">
        <div
            class="field"
            :class="{required: field.required}">
            <div class="name">
                {{ field.name }}
                <div
                    v-if="field.required"
                    class="required">
                    <AsteriskIcon :size="12" />
                </div>
            </div>
            <div class="description">
                {{ field.description }}
            </div>
            <input
                v-if="type === types.TYPE_TEXT"
                v-model="value"
                type="text"
                class="form-control"
                @keyup="fieldChanged($event.target.value)">
            <input
                v-else-if="type === types.TYPE_FILE"
                ref="file"
                type="file"
                class="form-control"
                @change="fileAdded($event)">
            <textarea
                v-else-if="type === types.TYPE_TEXTAREA"
                v-model="value"
                class="form-control"
                @keyup="fieldChanged($event.target.value)" />
            <Editor
                v-else-if="type === types.TYPE_RICHTEXT"
                @on-update="fieldChanged" />
            <div v-else-if="type === types.TYPE_DROPDOWN">
                <select
                    v-model="value"
                    class="form-select"
                    @change="fieldChanged($event.target.value)">
                    <option
                        value=""
                        selected>
                        {{ $t('Choose an option') }}
                    </option>
                    <option
                        v-for="option in prepareOptions(field)"
                        :key="option"
                        :value="option">
                        {{ option === '?' ? $t('Other') : option }}
                    </option>
                </select>
                <div
                    v-if="showCustomVariant"
                    class="form-group custom-variant">
                    <label for="">{{ $t('Other') }}</label>
                    <input
                        v-model="customVariant"
                        type="text"
                        class="form-control"
                        @keyup="customVariantChanged($event.target.value)">
                </div>
            </div>

            <div
                v-else-if="type === types.TYPE_CHECKBOX"
                class="form-check">
                <input
                    :id="`checkboxID${field.id}`"
                    v-model="value"
                    class="form-check-input"
                    type="checkbox"
                    @change="fieldChanged($event.target.checked)">
                <label
                    class="form-check-label"
                    :for="`checkboxID${field.id}`">
                    {{ prepareCheckboxName() }}
                </label>
            </div>
            <div
                v-else-if="type === types.TYPE_RADIO"
                class="form-check-radios">
                <div
                    v-for="(option,index) in prepareOptions(field)"
                    :key="option"
                    class="form-check">
                    <input
                        :id="`radioID${field.id}_indID${index}`"
                        class="form-check-input"
                        type="radio"
                        name="flexRadioDefault"
                        @change="fieldChanged(option)">
                    <label
                        class="form-check-label"
                        :for="`radioID${field.id}_indID${index}`">
                        {{ option }}
                    </label>
                </div>
            </div>
            <VueDatePicker
                v-else-if="type===types.TYPE_DATE"
                v-model="value"
                :locale="$i18n.locale"
                :enable-time-picker="false"
                :cancel-text="$t('Cancel')"
                :select-text="$t('Save')"
                format="dd.MM.YYY"
                @update:model-value="dateChanged($event)">
                <template #input-icon>
                    <div class="icon">
                        <CalendarIcon :size="18" />
                    </div>
                </template>
            </VueDatePicker>
            <VueDatePicker
                v-else-if="type===types.TYPE_TIME"
                v-model="value"
                time-picker
                :locale="$i18n.locale"
                :cancel-text="$t('Cancel')"
                :select-text="$t('Save')"
                @update:model-value="timeChanged($event)">
                <template #input-icon>
                    <div class="icon">
                        <ClockIcon :size="18" />
                    </div>
                </template>
            </VueDatePicker>
            <VueDatePicker
                v-else-if="type===types.TYPE_DATETIME"
                v-model="value"
                :locale="$i18n.locale"
                :cancel-text="$t('Cancel')"
                :select-text="$t('Save')"
                format="dd.MM.YYY HH:mm"
                @update:model-value="dateChanged($event, true)">
                <template #input-icon>
                    <div class="icon">
                        <CalendarIcon :size="18" />
                    </div>
                </template>
            </VueDatePicker>
            <VueDatePicker
                v-else-if="type===types.TYPE_DATERANGE"
                v-model="value"
                range
                :locale="$i18n.locale"
                :enable-time-picker="false"
                :cancel-text="$t('Cancel')"
                :select-text="$t('Save')"
                format="dd.MM.YYY"
                @update:model-value="dateRangeChanged($event)">
                <template #input-icon>
                    <div class="icon">
                        <CalendarIcon :size="18" />
                    </div>
                </template>
            </VueDatePicker>
            <VueDatePicker
                v-else-if="type===types.TYPE_TIMERANGE"
                v-model="value"
                time-picker
                range
                :locale="$i18n.locale"
                :cancel-text="$t('Cancel')"
                :select-text="$t('Save')"
                @update:model-value="timeRangeChanged($event)">
                <template #input-icon>
                    <div class="icon">
                        <ClockIcon :size="18" />
                    </div>
                </template>
            </VueDatePicker>
            <VueDatePicker
                v-else-if="type===types.TYPE_DATETIMERANGE"
                v-model="value"
                range
                :locale="$i18n.locale"
                :cancel-text="$t('Cancel')"
                :select-text="$t('Save')"
                format="dd.MM.YYY HH:mm"
                @update:model-value="dateRangeChanged($event, true)">
                <template #input-icon>
                    <div class="icon">
                        <CalendarIcon :size="18" />
                    </div>
                </template>
            </VueDatePicker>
        </div>
    </div>
</template>

<script>
import { formatDate } from '../../js/helpers/moment.js'
import CalendarIcon from 'vue-material-design-icons/Calendar.vue'
import ClockIcon from 'vue-material-design-icons/Clock.vue'
import AsteriskIcon from 'vue-material-design-icons/Asterisk.vue'
import Editor from './Editor.vue'
import { TYPES } from '../../js/consts.js'

export default {
    name: 'DynamicField',
    components: {
        Editor,
        AsteriskIcon,
        ClockIcon,
        CalendarIcon
    },
    props: {
        field: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-update' ],
    data() {
        return {
            showCustomVariant: false,
            customVariant: null,
            types: TYPES,
            value: null
        }
    },
    computed: {
        options() {
            return this.field.options !== null ? JSON.parse(this.field.options) : null
        },
        type() {
            return this.field.type
        }
    },
    methods: {
        fieldChanged(val) {
            this.customVariant = null
            this.showCustomVariant = val === '?'
            this.value = val
            this.$emit('on-update', {
                field: this.field,
                value: this.value
            })
        },
        customVariantChanged(val) {
            this.$emit('on-update', {
                field: this.field,
                value: val
            })
        },
        dateChanged(val, time = false) {
            const v = val === null ? null : time ? formatDate(val, 'DD.MM.YYYY HH:mm') : formatDate(val, 'DD.MM.YYYY')
            this.value = val
            this.$emit('on-update', {
                field: this.field,
                value: v
            })
        },
        dateRangeChanged(val, time = false) {
            const values = val === null ? null : val.map(date => {
                return time ? formatDate(date, 'DD.MM.YYYY HH:mm') : formatDate(date, 'DD.MM.YYYY')
            })
            this.value = val
            this.$emit('on-update', {
                field: this.field,
                value: values === null ? null : values.join(' - ')
            })
        },
        timeChanged(val) {
            this.value = `${val?.hours}:${val?.minutes}`
            this.$emit('on-update', {
                field: this.field,
                value: this.value
            })
        },
        timeRangeChanged(val) {
            const values = val === null ? null : val.map(time => {
                return `${time?.hours}:${time?.minutes}`
            })
            this.value = val
            this.$emit('on-update', {
                field: this.field,
                value: values === null ? null : values.join(' - ')
            })
        },
        fileAdded() {
            this.$emit('on-update', {
                field: this.field,
                value: this.$refs.file.files[0]
            })
        },
        onOptionClick(option) {
            if (option === '?') {
                this.showCustomVariant = true
            } else {
                this.showCustomVariant = false
            }
        },
        prepareOptions(field) {
            return field.options.split(/\n|\r\n/)
        },
        prepareCheckboxName() {
            if (this.field.options) {
                return JSON.parse(this.field.options) ? JSON.parse(this.field.options).name : null
            }
            return null
        }
    }
}
</script>

<style lang="scss" scoped>
.form-field {
    margin-top: 16px;

    .field {
        margin-bottom: 16px;

        .form-check-radios {
            display: flex;
            align-items: center;

            .form-check {
                margin: 0 16px 16px 0;
            }
        }

        .name {
            font-weight: bold;
            position: relative;

            .required {
                position: relative;
                top: -5px;
                display: inline-block;
                color: var(--bs-danger);
            }
        }

        .icon {
            margin: 0 12px;
            position: relative;
            top: -2px
        }

        .description {
            margin: 4px 0 10px 0;
            font-style: italic;
            font-size: var(--font-small);
        }

        &.required {

        }
    }

    .custom-variant {
        margin-top: 10px;
        background: rgba(0, 0, 0, 0.05);
        padding: calc(var(--padding-box) / 2);
        border-radius: var(--border-radius);
    }
}
</style>
