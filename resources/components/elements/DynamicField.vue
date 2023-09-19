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
                v-if="type === types.TYPE_FILE"
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
        </div>
    </div>
</template>

<script>
import AsteriskIcon from 'vue-material-design-icons/Asterisk.vue'
import Editor from './Editor.vue'
import { TYPES } from '../../js/consts.js'

export default {
    name: 'DynamicField',
    components: {
        Editor,
        AsteriskIcon
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
            this.value = val
            this.$emit('on-update', {
                field: this.field,
                value: this.value
            })
        },
        fileAdded() {
            this.$emit('on-update', {
                field: this.field,
                value: this.$refs.file.files[0]
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.form-field {
    margin-top: 16px;

    .field {
        margin-bottom: 16px;

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

        .description {
            margin: 4px 0 10px 0;
            font-style: italic;
            font-size: var(--font-small);
        }

        &.required {

        }
    }
}
</style>
