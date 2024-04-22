<template>
    <div class="rooms">
        <div class="alert alert-info">
            {{
                $t('The uploaded file must be in csv format (comma delimiters) and have columns group, level, title, description.')
            }}
        </div>
        <div class="form-group">
            <label for="">{{ $t('Upload file') }}</label>
            <input
                ref="file"
                type="file"
                accept="text/csv"
                class="form-control"
                @change="onFileChange">
        </div>
        <div
            v-if="groups.length > 0"
            class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ $t('Office') }}</label>
                    <OfficesMultiselect
                        @on-select="onSelectOffice($event)"
                        @on-clear="onClearOffice($event)" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ $t('Group filter') }}</label>
                    <MultiselectElement
                        v-model="officeFilter"
                        :options="groups"
                        @change="onGroupFilterChange" />
                </div>
            </div>
        </div>
        <div class="list-group mt-4">
            <div
                v-for="item in data"
                :key="item"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div>
                    <div class="title">
                        {{ item.title }}
                    </div>
                    <div class="description">
                        {{ item.description }}
                    </div>
                </div>
                <div class="level badge bg-success">
                    {{ item.level }}
                </div>
            </div>
        </div>
        <div
            v-if="loaded && data.length > 0">
            <div class="form-group mt-2">
                <input
                    v-model="clearPrevious"
                    type="checkbox"> {{ $t('Clear previous data') }}
            </div>
            <div class="form-group mt-2">
                <button
                    class="btn btn-purple "
                    @click="uploadData">
                    <UploadIcon :size="18" />
                    {{ $t('Upload') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import MultiselectElement from '../../elements/MultiselectElement.vue'
import OfficesMultiselect from '../../elements/OfficesMultiselect.vue'
import UploadIcon from 'vue-material-design-icons/Upload.vue'

export default {
    name: 'AdmRoomManagement',
    components: {
        OfficesMultiselect,
        UploadIcon,
        MultiselectElement
    },
    data() {
        return {
            loaded: false,
            file: null,
            contents: [],
            data: [],
            office: null,
            officeFilter: null,
            clearPrevious: null
        }
    },
    computed: {
        offices() {
            return this.$store.getters['getOffices']
        },
        groups() {
            let ar = []
            this.contents.map(c => {
                if (ar.indexOf(c.group) === -1) {
                    ar.push(c.group)
                }
                return c
            })
            return ar
        }
    },
    methods: {
        async onFileChange() {
            this.contents = []
            this.data = []
            this.officeFilter = null
            this.loaded = false
            this.file = this.$refs.file.files[0]
            console.log(this.file)
            const res = await this.$store.dispatch('onUploadCsv', { file: this.file })
            if (res) {
                this.loaded = true
                this.contents = res
            }
        },
        onSelectOffice(o) {
            this.data = []
            this.officeFilter = null
            this.office = o
        },
        onClearOffice(o) {
            this.data = []
            this.officeFilter = null
            this.office = null
        },
        onGroupFilterChange(o) {
            this.data = Object.assign(this.contents.filter(item => item.group === o))
        },
        async uploadData() {
            const data = {
                officeId: this.office.id,
                clearPrevious: this.clearPrevious,
                data: this.data
            }
            await this.$store.dispatch('uploadCsvRoomData', data)
            this.data = []
            this.contents = []
            this.officeFilter = null
            this.file = null
        }
    }
}
</script>

<style lang="scss" scoped>
.title {
    font-weight: bold;
}

.description {
    font-size: var(--font-small);
}
</style>
