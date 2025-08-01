<template>
    <div v-if="mini">
        <div class="row">
            <div class="col-md-4">
                <div class="name">
                    {{ field.field_name }}
                </div>
            </div>
            <div class="col-md-8">
                <div
                    v-if="isFile">
                    <a
                        target="_blank"
                        :href="`/user/tickets/file/${field.id}`"
                        class="download">
                        <DownLoadIcon :size="20" />
                        {{ fileName }}
                    </a>
                </div>
                <div
                    v-else-if="isCheckBox">
                    <button
                        class="btn btn-sm"
                        :class="field.content ? 'btn-success':'btn-danger'">
                        {{ field.content ? $t('Yes') : $t('No') }}
                    </button>
                </div>
                <div
                    v-else-if="isRichText"
                    v-html="field.content" />
                <div
                    v-else-if="isJson && fieldJsonOptions && fieldJsonContent">
                    <span
                        v-for="(th,i) in fieldJsonOptions.fields"
                        :key="th"
                        class="badge bg-primary me-2">
                        {{ th.title }}
                        <span
                            v-if="fieldJsonContent[i] !== undefined"
                            class="">
                            - {{ fieldJsonContent[i][i]?.value }}
                        </span>
                    </span>
                </div>
                <div
                    v-else>
                    {{ field.content }}
                </div>
            </div>
        </div>
    </div>
    <div
        v-else
        class="ticket-field">
        <div class="row">
            <div class="col-md-4">
                <div class="name">
                    {{ field.field_name }}
                </div>
            </div>
            <div class="col-md-8">
                <div
                    v-if="isFile"
                    class="content">
                    <a
                        target="_blank"
                        :href="`/user/tickets/file/${field.id}`"
                        class="download">
                        <DownLoadIcon :size="20" />
                        {{ fileName }}
                    </a>
                </div>
                <div
                    v-else-if="isCheckBox">
                    <button
                        class="btn"
                        :class="field.content ? 'btn-success':'btn-danger'">
                        {{ field.content ? $t('Yes') : $t('No') }}
                    </button>
                </div>
                <div
                    v-else-if="isRichText"
                    v-html="field.content" />
                <div
                    v-else-if="isJson && fieldJsonOptions && fieldJsonContent">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th
                                    v-for="th in fieldJsonOptions.fields"
                                    :key="th">
                                    {{ th.title }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(th,i) in fieldJsonOptions.fields"
                                :key="th">
                                <td
                                    v-for="item in fieldJsonContent[i]"
                                    :key="item">
                                    {{ item.value }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    v-else
                    class="content">
                    {{ field.content }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DownLoadIcon from 'vue-material-design-icons/Download.vue'
import { TYPES } from '../../js/consts.js'

export default {
    name: 'TicketField',
    components: {
        DownLoadIcon
    },
    props: {
        mini: {
            type: Boolean,
            default: false
        },
        field: {
            type: Object,
            required: true
        }
    },
    computed: {
        isCheckBox() {
            return this.field.field_type === TYPES.TYPE_CHECKBOX
        },
        isFile() {
            return this.field.field_type === TYPES.TYPE_FILE
        },
        isRichText() {
            return this.field.field_type === TYPES.TYPE_RICHTEXT
        },
        isJson() {
            return this.field.field_type === TYPES.TYPE_MULTI_JSON
        },
        fileName() {
            return this.field.content.replace('/', '')
        },
        fieldJsonContent() {
            return this.isJson ? JSON.parse(this.field.content) : this.field.content
        },
        fieldJsonOptions() {
            return this.isJson ? JSON.parse(this.field.field_options) : null
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket-field {
    margin-bottom: 10px;

    .name {
        font-weight: bold;
    }

    .content {
        a.download {
            text-decoration: none;
        }
    }
}
</style>
