<template>
    <VSelect
        v-model="model"
        :label="$t('Allowed mimes')"
        :hide-details="false"
        :hint="$t('Specify the types of files allowed for uploading')"
        persistent-hint
        multiple
        chips
        closable-chips
        :searchable="true"
        :items="extensions"
        @update:model-value="$emit('on-change',$event)" />
</template>

<script>

export default {
    name: 'MimesMultiselect',
    components: {},
    props: {
        value: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-change' ],
    data() {
        return {
            model: null,
            mimes: [
                'application/zip',
                'application/gzip',
                'application/xml',
                'image/png',
                'image/jpg',
                'image/jpeg',
                'image/gif',
                'text/plain',
                'application/vnd.oasis.opendocument.text', // OpenDocument[15]
                'application/vnd.oasis.opendocument.spreadsheet', // OpenDocument[16]
                'application/vnd.oasis.opendocument.presentation', // OpenDocument[17]
                'application/vnd.oasis.opendocument.graphics', //OpenDocument[18]
                'application/vnd.ms-excel', // Microsoft Excel
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Microsoft Excel 2007
                'application/vnd.ms-excel.sheet.macroEnabled.12', // Microsoft Excel 2007  c макросами .XLSM
                'application/vnd.ms-powerpoint', // Microsoft Powerpoint
                'application/vnd.openxmlformats-officedocument.presentationml.presentation', // Microsoft Powerpoint 2007
                'application/msword', // Microsoft Word
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Microsoft Word 2007
                'application/x-rar-compressed',
                'application/pdf'
            ],
            extensions: [
                'zip',
                'gzip',
                'xml',
                'png',
                'jpg',
                'jpeg',
                'gif',
                'txt',
                'odt',
                'ods',
                'odp',
                'xls',
                'xlsx',
                'xlsm',
                'ppt',
                'pptx',
                'doc',
                'docx',
                'pdf',
                'rar'
            ]
        }
    },
    created() {
        this.model = this.value
        this.extensions = this.extensions.sort((a, b) => {
            if (a < b) {
                return -1
            }
            if (a > b) {
                return 1
            }
            return 0
        })
    }
}
</script>

<style scoped>

</style>
