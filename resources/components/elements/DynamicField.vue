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

            <div
                v-if="type === types.TYPE_TEXT"
                class="input-group input-group-sm autocomplete">
                <input
                    v-model="value"
                    type="text"
                    class="form-control"
                    @click.stop="debounceFn"
                    @paste="fieldChanged($event.target.value)"
                    @keyup="fieldChanged($event.target.value)">
                <VDropdown
                    v-if="autocompleteValues.length > 0"
                    :triggers="[]"
                    :shown="showPopper"
                    :auto-hide="false"
                    placement="auto">
                    <button
                        class="btn btn-purple rounded-0 py-1"
                        @click="showPopper = !showPopper">
                        <ChevronDownIcon :size="16" />
                    </button>
                    <template #popper>
                        <div
                            class="autocompletes">
                            <button
                                class="close-ac btn btn-transparent"
                                @click="closePopper">
                                <CloseIcon :size="20" />
                            </button>
                            <h5 class="pb-1 pt-2 px-3">
                                {{ $t('Best matches') }}
                            </h5>
                            <div
                                v-if="!loading"
                                class="list-group">
                                <div
                                    v-for="a in autocompleteValues"
                                    :key="a.id"
                                    class="autocomplete-value list-group-item"
                                    @click="setValueThroughAutocomplete(a.value)">
                                    {{ a.value }}
                                    <button
                                        class="btn btn-sm btn-link text-danger"
                                        @click.stop="deleteAutocompleteValue(a)">
                                        <MinusIcon :size="14" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </VDropdown>
                <button
                    v-tooltip="$t('Add to favorites')"
                    :disabled="value == null || value.length < 1"
                    class="btn btn-purple"
                    @click="addToAutocomplete">
                    <Loading
                        v-if="loading"
                        :size="16" />
                    <CheckIcon
                        v-if="!loading && autocompleteSuccess"
                        :size="16" />
                    <PlusIcon
                        v-if="!loading && !autocompleteSuccess"
                        :size="16" />
                </button>
                <button class="btn btn-outline-secondary">
                    <HelpComment>
                        {{ $t('Add to favorites') }}
                        <template #trigger>
                            <HelpIcon :size="14" />
                        </template>
                    </HelpComment>
                </button>
            </div>


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
                @paste="fieldChanged($event.target.value)"
                @keyup="fieldChanged($event.target.value)" />
            <Editor
                v-else-if="type === types.TYPE_RICHTEXT"
                ref="editor"
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
                        :checked="option===value"
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
                ref="dp_date"
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
                ref="dp_date"
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
            <div
                v-else-if="type===types.TYPE_DATERANGE"
                class="range">
                <VueDatePicker
                    ref="dp_start"
                    v-model="start"
                    :placeholder="$t('From')"
                    class="range-input"
                    :locale="$i18n.locale"
                    :enable-time-picker="false"
                    :cancel-text="$t('Cancel')"
                    :select-text="$t('Save')"
                    format="dd.MM.YYY"
                    @update:model-value="startChanged($event, true, false)">
                    <template #input-icon>
                        <div class="icon">
                            <CalendarIcon :size="18" />
                        </div>
                    </template>
                </VueDatePicker>
                <VueDatePicker
                    ref="dp_end"
                    v-model="end"
                    :placeholder="$t('To')"
                    class="range-input"
                    :locale="$i18n.locale"
                    :enable-time-picker="false"
                    :cancel-text="$t('Cancel')"
                    :select-text="$t('Save')"
                    format="dd.MM.YYY"
                    @update:model-value="endChanged($event, true, false)">
                    <template #input-icon>
                        <div class="icon">
                            <CalendarIcon :size="18" />
                        </div>
                    </template>
                </VueDatePicker>
            </div>
            <div
                v-else-if="type===types.TYPE_TIMERANGE"
                class="range">
                <VueDatePicker
                    v-model="start"
                    :placeholder="$t('From')"
                    class="range-input"
                    time-picker
                    :locale="$i18n.locale"
                    :cancel-text="$t('Cancel')"
                    :select-text="$t('Save')"
                    @update:model-value="startChanged($event, false, true)">
                    <template #input-icon>
                        <div class="icon">
                            <ClockIcon :size="18" />
                        </div>
                    </template>
                </VueDatePicker>
                <VueDatePicker
                    v-model="end"
                    :placeholder="$t('To')"
                    class="range-input"
                    time-picker
                    :locale="$i18n.locale"
                    :cancel-text="$t('Cancel')"
                    :select-text="$t('Save')"
                    @update:model-value="endChanged($event, false, true)">
                    <template #input-icon>
                        <div class="icon">
                            <ClockIcon :size="18" />
                        </div>
                    </template>
                </VueDatePicker>
            </div>

            <div
                v-else-if="type===types.TYPE_DATETIMERANGE"
                class="range">
                <VueDatePicker
                    v-model="start"
                    :placeholder="$t('From')"
                    class="range-input"
                    :locale="$i18n.locale"
                    :cancel-text="$t('Cancel')"
                    :select-text="$t('Save')"
                    format="dd.MM.YYY HH:mm"
                    @update:model-value="startChanged($event, true, true)">
                    <template #input-icon>
                        <div class="icon">
                            <CalendarIcon :size="18" />
                        </div>
                    </template>
                </VueDatePicker>
                <VueDatePicker
                    v-model="end"
                    :placeholder="$t('To')"
                    class="range-input"
                    :locale="$i18n.locale"
                    :cancel-text="$t('Cancel')"
                    :select-text="$t('Save')"
                    format="dd.MM.YYY HH:mm"
                    @update:model-value="endChanged($event, true, true)">
                    <template #input-icon>
                        <div class="icon">
                            <CalendarIcon :size="18" />
                        </div>
                    </template>
                </VueDatePicker>
            </div>
            <MultiField
                v-else-if="type === types.TYPE_MULTI_JSON"
                :field="field"
                @on-update-value="fieldChanged($event)" />
        </div>
    </div>
</template>

<script>
import { formatDate, toDate } from '../../js/helpers/moment.js'

import HelpComment from '../chunks/HelpComment.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import MinusIcon from 'vue-material-design-icons/Minus.vue'
import CheckIcon from 'vue-material-design-icons/Check.vue'
import HelpIcon from 'vue-material-design-icons/Help.vue'
import CalendarIcon from 'vue-material-design-icons/Calendar.vue'
import ClockIcon from 'vue-material-design-icons/Clock.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'
import AsteriskIcon from 'vue-material-design-icons/Asterisk.vue'
import ChevronDownIcon from 'vue-material-design-icons/ChevronDown.vue'
import Loading from './Loading.vue'
import Editor from './Editor.vue'
import { TYPES } from '../../js/consts.js'

import debounce from '../../js/helpers/debounce.js'

import { useToast } from 'vue-toastification'
import MultiField from './MultiField.vue'

const toast = useToast()

export default {
    name: 'DynamicField',
    components: {
        MultiField,
        Loading,
        Editor,
        AsteriskIcon,
        ChevronDownIcon,
        ClockIcon,
        CalendarIcon,
        PlusIcon,
        HelpComment,
        HelpIcon,
        CloseIcon,
        CheckIcon,
        MinusIcon
    },
    props: {
        field: {
            type: Object,
            required: true
        },
        startValue: {
            type: String,
            default: null
        }
    },
    emits: [ 'on-update', 'on-clear' ],
    data() {
        return {
            showPopper: false,
            loading: false,
            showCustomVariant: false,
            customVariant: null,
            types: TYPES,
            value: null,
            start: null,
            end: null,
            autocompleteSuccess: false,
            autocompleteValues: [],
            debounceFn: debounce(this.searchAutocompleteFieldValue, 500)
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
    mounted() {
        this.emitter.on('on.ticket.form.click', () => {
            //this.autocompleteValues = []
        })
        if (this.startValue !== null) {
            //this.value = this.startValue
            switch (this.type) {
                case this.types.TYPE_TEXT:
                case this.types.TYPE_TEXTAREA:
                case this.types.TYPE_DROPDOWN:
                case this.types.TYPE_RADIO:
                case this.types.TYPE_CHECKBOX:
                    this.fieldChanged(this.startValue)
                    break
                case this.types.TYPE_DATE:
                case this.types.TYPE_DATETIME:
                    this.dateChanged(toDate(this.startValue), this.type === this.types.TYPE_DATETIME)
                    break
                case this.types.TYPE_TIME:
                    const value = this.startValue.split(':')
                    this.value = {
                        hours: value[0],
                        minutes: value[1],
                        seconds: 0
                    }
                    this.$emit('on-update', {
                        field: this.field,
                        value: this.startValue
                    })
                    break
                case this.types.TYPE_TIMERANGE:
                    const times = this.startValue.split(' - ')
                    const startTime = times[0].split(':')
                    const endTime = times[1].split(':')
                    this.start = {
                        hours: startTime[0],
                        minutes: startTime[1],
                        seconds: 0
                    }
                    this.end = {
                        hours: endTime[0],
                        minutes: endTime[1],
                        seconds: 0
                    }
                    this.value = this.startValue
                    // this.$emit('on-update', {
                    //     field: this.field,
                    //     value: this.value
                    // })
                    break
                case this.types.TYPE_DATERANGE:
                case this.types.TYPE_DATETIMERANGE:
                    let _date = true
                    let _time = false
                    const dates = this.startValue.split(' - ')
                    this.start = toDate(dates[0])
                    this.end = toDate(dates[1])
                    if (this.type === this.types.TYPE_DATETIMERANGE) {
                        _time = true
                    }
                    if (this.type === this.types.TYPE_TIMERANGE) {
                        _date = false
                        _time = true
                    }
                    this.rangeChanged(this.startValue, _date, _time)
                    break
                case this.types.TYPE_RICHTEXT:
                    this.$refs?.editor.setContent(this.startValue)
                    break
            }
        }
    },
    unmounted() {
        this.emitter.off('on.ticket.form.click')
    },
    methods: {
        startChanged(s, date = true, time = true) {
            this.start = s
            this.rangeChanged(s, date, time)
        },
        endChanged(e, date = true, time = true) {
            this.end = e
            this.rangeChanged(e, date, time)
        },
        async fieldChanged(val, e, v) {

            this.customVariant = null
            this.showCustomVariant = val === '?'
            this.value = val

            this.$emit('on-update', {
                field: this.field,
                value: this.value
            })

            await this.debounceFn()
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
        rangeChanged(val, date = true, time = false) {
            let format = []
            if (date) {
                format.push('DD.MM.YYYY')
            }
            if (time) {
                format.push('HH:mm')
            }
            const formatStr = format.join(' ')
            let start
            let end
            if (!date && time) {
                start = this.start
                end = this.end
            } else {
                start = this.start === null ? null : formatDate(this.start, formatStr)
                end = this.end === null ? null : formatDate(this.end, formatStr)
            }

            if (
                this.type === this.types.TYPE_DATETIMERANGE ||
                this.type === this.types.TYPE_DATERANGE ||
                this.type === this.types.TYPE_TIMERANGE
            ) {
                if (this.type === this.types.TYPE_TIMERANGE) {
                    this.value = `${start?.hours?.toString().padStart(2, '0')}:${start?.minutes?.toString().padStart(2, '0')} - ${end?.hours?.toString().padStart(2, '0')}:${end?.minutes?.toString().padStart(2, '0')}`
                } else {
                    this.value = `${start} - ${end}`
                }

            } else {
                this.value = val
            }
            if (this.start === null || this.end === null) {
                toast.error(this.$t('It is necessary to fill in the date intervals correctly'))
                this.$emit('on-clear', {
                    field: this.field,
                    value: null
                })
            } else {
                this.$emit('on-update', {
                    field: this.field,
                    value: this.value
                })
            }

        },
        timeChanged(val) {
            this.value = val
            this.$emit('on-update', {
                field: this.field,
                value: `${val?.hours?.toString().padStart(2, '0')}:${val?.minutes?.toString().padStart(2, '0')}`
            })
        },
        fileAdded() {
            this.$emit('on-update', {
                field: this.field,
                value: this.$refs.file.files[0]
            })
        },
        prepareOptions(field) {

            return field.options.split(/\n|\r\n/)
            //return field.options
        },
        prepareCheckboxName() {
            if (this.field.options) {
                const opts = JSON.parse(this.field.options)
                return opts ? opts.name : null
            }
            return null
        },
        /**
         * AUTOCOMPLETES
         */
        async addToAutocomplete() {
            if (this.type === TYPES.TYPE_TEXT) {
                const data = {
                    field_id: this.field.id,
                    value: this.value
                }
                await this.$store.dispatch('addAutocompleteFieldValue', data).then(() => {
                    this.autocompleteSuccess = true
                }).catch(() => {
                    this.autocompleteSuccess = false
                })
            }
        },
        async searchAutocompleteFieldValue() {
            if (this.type === TYPES.TYPE_TEXT) {
                this.loading = true
                this.autocompleteValues = await this.$store.dispatch('getAutocompleteFieldValues', {
                    field_id: this.field.id,
                    term: this.value
                })
                this.loading = false
            }
        },
        setValueThroughAutocomplete(value) {
            this.fieldChanged(value)
            this.closePopper()
        },
        async deleteAutocompleteValue(a) {
            await this.$store.dispatch('removeAutocompleteFieldValue', a.id)
            this.autocompleteValues = this.autocompleteValues.filter(v => v.id !== a.id)
        },
        closePopper() {
            this.showPopper = false
        }
    }
}
</script>

<style lang="scss" scoped>
.autocompletes {
    background: var(--bs-light);
    width: 100%;
    min-width: 300px;
    box-shadow: 0 10px 10px rgba(0, 0, 0, 0.2);

    .close-ac {
        position: absolute;
        right: 0;
        top: 4px
    }

    .list-group {
        max-height: 300px;
        overflow: auto;

        .list-group-item {
            position: relative;

            .text-danger {
                position: absolute;
                right: 0;
                top: 1px
            }
        }

    }

    .autocomplete-actions {
        padding: 6px;
    }

    .autocomplete-value {
        font-size: var(--font-small);

        &:hover {
            cursor: pointer;
            background: var(--bs-light);
        }
    }
}

.form-field {
    margin-top: 16px;

    .range {
        display: flex;
        align-items: center;
        justify-content: space-between;

        .range-input {
            width: calc(50% - 5px);
        }

    }

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
