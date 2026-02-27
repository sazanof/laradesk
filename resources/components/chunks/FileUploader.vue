<template>
    <VFileUpload
        :model-value="model"
        class="pa-2"
        hide-browse
        multiple
        show-size
        density="compact"
        clearable
        title=""
        :divider-text="$t('You can upload up to 5 files at a time. The size of each file should not exceed {size} kb', {size: maxFileSize})"
        @update:model-value="updateFiles">
        <template #item="{ file, props }">
            <VFileUploadItem
                v-bind="props"
                lines="one"
                nav>
                <template #clear="{props: clearProps }">
                    <VBtn
                        size="small"
                        variant="tonal"
                        rounded="pill"
                        color="deep-purple"
                        icon="mdi-close"
                        @click="removeFile(file)" />
                </template>
            </VFileUploadItem>
        </template>
        <template #title>
            <div class="text-subtitle-2  mt-1">
                {{ $t('Drag and drop files or choose from computer') }}
                <VMenu
                    v-model="open"
                    max-width="400"
                    open-on-hover>
                    <template #activator="{props}">
                        <VIcon
                            size="small"
                            color="primary"
                            icon="mdi-information"
                            v-bind="props" />
                    </template>
                    <VCard>
                        <VCardSubtitle class="mt-4">
                            {{ $t('Allowed mimes') }}
                        </VCardSubtitle>
                        <VCardText class="py-2">
                            <VChip
                                v-for="mime in allowedMimes"
                                :key="mime"
                                size="small"
                                color="primary"
                                class="mr-2 mb-2"
                                :text="mime" />
                        </VCardText>
                        <VCardSubtitle class="mt-4">
                            {{ $t('Max file size') }}
                        </VCardSubtitle>
                        <VCardText class="py-2">
                            <VChip
                                size="small"
                                color="error"
                                :text="`${maxFileSize} Kb`" />
                        </VCardText>
                    </VCard>
                </VMenu>
            </div>
        </template>
        <template #icon>
            <VIcon
                icon="mdi-upload"
                :size="24" />
        </template>
    </VFileUpload>
</template>

<script>
import { VFileUpload, VFileUploadItem } from 'vuetify/labs/VFileUpload'


export default {
    name: 'FileUploader',
    components: {
        VFileUpload,
        VFileUploadItem
    },
    emits: [ 'on-files-changed' ],
    data() {
        return {
            open: false,
            drag: false,
            model: [],
            files: []

        }
    },
    computed: {
        allowedMimes() {
            return this.$store.getters['getAllowedMimes']
        },
        maxFileSize() {
            return this.$store.getters['getMaxFileSize']
        }
    },
    methods: {
        updateFiles(files) {
            this.model = [ ...this.model, ...files ]
            this.$emit('on-files-changed', this.model)
        },
        removeFile(file) {
            this.model = this.model.filter(f => f.name !== file.name)
            this.$emit('on-files-changed', this.model)
        },
        reset() {
            this.model = []
        }
    }
}
</script>

<style lang="scss" scoped>
.files {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: var(--padding-box);
    border: 2px dashed var(--bs-border-color);
    background: var(--bs-light);
    cursor: pointer;

    &.draggable {
        border-color: var(--bs-purple);
        background: var(--bs-purple-o2);
    }

    .helper {
        text-align: center;

        .mimes {
            display: block;
            font-size: var(--font-small);
            margin-top: 16px;
        }
    }

    .file-list {
        margin-top: 8px;

        .uploaded-file {
            color: var(--bs-gray);
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            font-style: italic;
            margin: 4px;

            .del {
                cursor: pointer;
                color: var(--bs-danger);
                margin: -2px 3px 0;
            }

            .help {
                font-size: var(--font-small);
                font-style: italic;
                color: var(--bs-gray)
            }
        }
    }
}

</style>
