<script>
import { formatDate, toDate } from '../../js/helpers/moment.js'
import { VDateInput } from 'vuetify/labs/VDateInput'
import ClockIcon from 'vue-material-design-icons/Clock.vue'
import Editor from './Editor.vue'
import { TYPES } from '../../js/consts.js'

import debounce from '../../js/helpers/debounce.js'

import MultiField from './MultiField.vue'
import TimePicker from '../chunks/TimePicker.vue'
import CheckBoxList from '../chunks/form/CheckBoxList.vue'

export default {
    name: 'DynamicField',
    components: {
        MultiField,
        Editor,
        ClockIcon,
        TimePicker,
        VDateInput,
        CheckBoxList
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
            startTime: null,
            endTime: null,
            autocompleteSuccess: false,
            autocompleteValues: [],
            debounceFn: debounce(this.searchAutocompleteFieldValue, 500),
            time: null,
            date: null
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
                    this.dateChanged(toDate(this.startValue), this.type === this.types.TYPE_DATETIME)
                    break
                case this.types.TYPE_DATETIME:
                    this.date = formatDate(this.startValue, 'DD.MM.YYYY')
                    this.time = formatDate(this.startValue, 'HH:mm')
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
                    break
                case this.types.TYPE_DATERANGE:
                    const dates = this.startValue.split(' - ')
                    this.start = formatDate(dates[0], 'DD.MM.YYYY')
                    this.end = formatDate(dates[1], 'DD.MM.YYYY')
                    break
                case this.types.TYPE_DATETIMERANGE:
                    const datetimes = this.startValue.split(' - ')
                    this.start = formatDate(datetimes[0], 'DD.MM.YYYY')
                    this.end = formatDate(datetimes[1], 'DD.MM.YYYY')
                    this.startTime = formatDate(datetimes[0], 'HH:mm')
                    this.endTime = formatDate(datetimes[1], 'HH:mm')
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
        prepareDateTime(date, time) {
            if (date !== null && time !== null) {
                return `${formatDate(date, 'DD.MM.YYYY')} ${time}`
            }
            return null
        },
        startChanged() {
            const start = this.prepareDateTime(this.start, this.startTime)
            const end = this.prepareDateTime(this.end, this.endTime)
            if (start !== null && end !== null) {
                const datetime = `${start} - ${end}`
                this.value = datetime
                this.$emit('on-update', {
                    field: this.field,
                    value: datetime
                })
            } else {
                this.value = null
                this.$emit('on-update', {
                    field: this.field,
                    value: null
                })
            }
        },
        endChanged() {
            const start = this.prepareDateTime(this.start, this.startTime)
            const end = this.prepareDateTime(this.end, this.endTime)
            if (start !== null && end !== null) {
                const datetime = `${start} - ${end}`
                this.value = datetime
                this.$emit('on-update', {
                    field: this.field,
                    value: datetime
                })
            } else {
                this.value = null
                this.$emit('on-update', {
                    field: this.field,
                    value: null
                })
            }
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
        dateTimeChanged(date, time) {
            if (date !== null && time !== null) {
                const datetime = `${formatDate(date, 'DD.MM.YYYY')} ${time}`
                this.value = datetime
                this.$emit('on-update', {
                    field: this.field,
                    value: datetime
                })
            }
        },
        timeChanged(val) {
            this.value = val
            this.$emit('on-update', {
                field: this.field,
                value: val
            })
        },
        fileAdded() {
            this.$emit('on-update', {
                field: this.field,
                value: this.$refs.file.files[0]
            })
        },
        prepareOptions(field) {
            return field.options?.split(/\n|\r\n/)
        },
        prepareCheckboxName() {
            if (this.field.options) {
                if (this.options.hasOwnProperty('link') && this.options.hasOwnProperty('title')) {
                    const url = `<a href="${this.options.link}" target="_blank">${this.options.title}</a>`
                    return this.options.name.replace('{replace}', url)
                }
                return this.options.name ?? null
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
<template>
    <div class="form-field">
        <div
            class="field"
            :class="{required: field.required}">
            <div class="name">
                {{ field.name }}
                <VIcon
                    v-if="field.required"
                    size="small"
                    color="red"
                    icon="mdi-asterisk" />
            </div>
            <div class="description">
                {{ field.description }}
            </div>

            <div
                v-if="type === types.TYPE_TEXT"
                class="input-group input-group-sm autocomplete">
                <VTextField
                    v-model="value"
                    type="text"
                    class="form-control"
                    @click.stop="debounceFn"
                    @paste="fieldChanged($event.target.value)"
                    @keyup="fieldChanged($event.target.value)">
                    <template #append-inner>
                        <VMenu
                            v-if="autocompleteValues.length > 0 && !loading">
                            <VList>
                                <VListSubheader>
                                    {{ $t('Best matches') }}
                                </VListSubheader>

                                <VListItem
                                    v-for="a in autocompleteValues"
                                    :key="a.id"
                                    class="autocomplete-value list-group-item"
                                    @click="setValueThroughAutocomplete(a.value)">
                                    {{ a.value }}
                                    <template #append>
                                        <VBtn
                                            icon="mdi-minus"
                                            color="red"
                                            variant="tonal"
                                            size="small"
                                            density="comfortable"
                                            rounded="pill"
                                            @click.stop="deleteAutocompleteValue(a)" />
                                    </template>
                                </VListItem>
                            </VList>

                            <template #activator="{props}">
                                <VBtn
                                    density="comfortable"
                                    color="default"
                                    variant="plain"
                                    v-bind="props"
                                    icon="mdi-chevron-down" />
                            </template>
                        </VMenu>

                        <VBtn
                            v-tooltip="$t('Add to favorites')"
                            size="small"
                            density="comfortable"
                            color="default"
                            variant="plain"
                            :loading="loading"
                            icon
                            :disabled="value == null || value.length < 1"
                            @click="addToAutocomplete">
                            <template #default>
                                <VIcon
                                    v-if="!loading && autocompleteSuccess"
                                    icon="mdi-check" />
                                <VIcon
                                    v-if="!loading && !autocompleteSuccess"
                                    icon="mdi-plus" />
                            </template>
                        </VBtn>
                    </template>
                </VTextField>
            </div>


            <VFileInput
                v-else-if="type === types.TYPE_FILE"
                ref="file"
                class="form-control"
                @change="fileAdded($event)" />
            <VTextarea
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
                <VSelect
                    v-model="value"
                    :items="prepareOptions(field)"
                    class="form-select"
                    @update:model-value="fieldChanged($event)">
                    <template #item="{item, props}">
                        <VListItem v-bind="props">
                            <template #title>
                                {{ item.raw === '?' ? $t('Other') : item.raw }}
                            </template>
                        </VListItem>
                    </template>
                    <template #selection="{item}">
                        {{ item.raw === '?' ? $t('Other') : item.raw }}
                    </template>
                </VSelect>
                <VCard
                    v-if="showCustomVariant"
                    variant="tonal"
                    class="mt-2"
                    :subtitle="$t('Other')">
                    <template #text>
                        <VTextField
                            v-model="customVariant"
                            prepend-icon="mdi-text"
                            density="compact"
                            type="text"
                            class="form-control"
                            @keyup="customVariantChanged($event.target.value)" />
                    </template>
                </VCard>
            </div>

            <div
                v-else-if="type === types.TYPE_CHECKBOX"
                class="form-check">
                <VCheckboxBtn
                    :id="`checkboxID${field.id}`"
                    v-model="value"
                    type="checkbox"
                    @change="fieldChanged($event.target.checked)">
                    <template #label>
                        <span v-html="prepareCheckboxName()" />
                    </template>
                </VCheckboxBtn>
            </div>
            <div
                v-else-if="type === types.TYPE_CHECKBOX_LIST"
                class="form-check">
                <CheckBoxList
                    :field="field"
                    @update:model-value="fieldChanged($event)" />
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
            <VDateInput
                v-else-if="type===types.TYPE_DATE"
                ref="dp_date"
                v-model="value"
                format="dd.MM.YYY"
                :placeholder="$t('DD.MM.YYYY')"
                @update:model-value="dateChanged($event)" />
            <TimePicker
                v-else-if="type===types.TYPE_TIME"
                v-model="value"
                time-picker
                @update:model-value="timeChanged($event)" />
            <VDateInput
                v-else-if="type===types.TYPE_DATETIME"
                ref="dp_date"
                v-model="date"
                :placeholder="$t('DD.MM.YYYY')"
                format="dd.MM.YYYY"
                @update:model-value="dateTimeChanged(date, time)">
                <template #append-inner>
                    <TimePicker
                        v-model="time"
                        @update:model-value="dateTimeChanged(date, time)">
                        <template #trigger="{props}">
                            <VBtn
                                density="comfortable"
                                variant="tonal"
                                :color="time !== null ? 'primary' : 'default'"
                                prepend-icon="mdi-clock"
                                :text="time ?? '00:00'"
                                v-bind="props" />
                        </template>
                    </TimePicker>
                </template>
            </VDateInput>
            <div
                v-else-if="type===types.TYPE_DATERANGE"
                class="range">
                <VContainer class="pa-0">
                    <VRow>
                        <VCol cols="6">
                            <VDateInput
                                v-model="start"
                                clearable
                                input-format="dd.mm.yyyy"
                                color="default"
                                :placeholder="$t('DD.MM.YYYY')"
                                :label="$t('From')"
                                @update:model-value="startChanged($event, true, false)" />
                        </VCol>
                        <VCol cols="6">
                            <VDateInput
                                ref="dp_end"
                                v-model="end"
                                clearable
                                input-format="dd.mm.yyyy"
                                color="default"
                                :placeholder="$t('DD.MM.YYYY')"
                                :label="$t('To')"
                                @update:model-value="endChanged($event, true, false)" />
                        </VCol>
                    </VRow>
                </VContainer>
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
                v-else-if="type===types.TYPE_DATETIMERANGE">
                <VContainer class="pa-0">
                    <VRow>
                        <VCol cols="6">
                            <VDateInput
                                v-model="start"
                                clearable
                                :placeholder="$t('DD.MM.YYYY')"
                                :label="$t('From')"
                                @update:model-value="startChanged">
                                <template #append-inner>
                                    <TimePicker
                                        v-model="startTime"
                                        @update:model-value="startChanged">
                                        <template #trigger="{props}">
                                            <VBtn
                                                v-bind="props"
                                                density="comfortable"
                                                prepend-icon="mdi-clock"
                                                :text="startTime ?? '00:00'" />
                                        </template>
                                    </TimePicker>
                                </template>
                            </VDateInput>
                        </VCol>
                        <VCol>
                            <VDateInput
                                v-model="end"
                                clearable
                                :placeholder="$t('DD.MM.YYYY')"
                                :label="$t('To')"
                                @update:model-value="endChanged">
                                <template #append-inner>
                                    <TimePicker
                                        v-model="endTime"
                                        @update:model-value="endChanged">
                                        <template #trigger="{props}">
                                            <VBtn
                                                v-bind="props"
                                                density="comfortable"
                                                :text="endTime ?? '00:00'"
                                                prepend-icon="mdi-clock" />
                                        </template>
                                    </TimePicker>
                                </template>
                            </VDateInput>
                        </VCol>
                    </VRow>
                </VContainer>
            </div>
            <MultiField
                v-else-if="type === types.TYPE_MULTI_JSON"
                :field="field"
                @on-update-value="fieldChanged($event)" />

            <!--            <SurmWorkplaceField-->
            <!--                v-else-if="type === types.TYPE_SURM_WORKPLACE"-->
            <!--                :field="field"-->
            <!--                @on-value-changed="fieldChanged($event)" />-->
        </div>
    </div>
</template>


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
