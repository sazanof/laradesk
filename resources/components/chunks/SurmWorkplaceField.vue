<script>

export default {
    name: 'SurmWorkplaceField',
    props: {
        field: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-value-changed' ],
    data() {
        return {
            value: {
                workplace_id: null,
                common: [],
                entities: [],
                user: null,
                action: 1
            },
            workplaceActions: [
                {
                    title: this.$t('Create'),
                    value: 1
                },
                {
                    title: this.$t('Move'),
                    value: 2
                },
                {
                    title: this.$t('Dismount'),
                    value: 3
                }
            ],
            workplaces: [],
            selectedWorkplace: null
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        }
    },
    async created() {
        await this.getWorkplaces()
        this.value.user = this.user.username
    },
    methods: {
        async getWorkplaces() {
            this.workplaces = await this.$store.dispatch('getSurmWorkplacesByUsernameAndRoom')
        },
        onSelectWorkplace(e) {
            if (this.value.workplace_id === null) {
                this.selectedWorkplace = null
            } else {
                this.selectedWorkplace = this.workplaces.find(wp => wp.id === this.value.workplace_id)
            }
            this.$emit('on-value-changed', this.value)
        },
        onChangeCommon() {
            this.$emit('on-value-changed', this.value)
        },
        onChangeEntities() {
            this.$emit('on-value-changed', this.value)
        }
    }
}
</script>

<template>
    <div>
        <select
            v-model="value.workplace_id"
            class="form-select"
            @change="onSelectWorkplace()">
            <option :value="null">
                {{ $t('Select workplace') }}
            </option>
            <option
                v-for="wp in workplaces"
                :key="wp.id"
                :value="wp.id">
                #{{ wp.id }}: {{ wp.user?.full_name ? wp.user?.full_name : $t('Workplace noname') }}
            </option>
        </select>

        <select
            v-if="value.workplace_id"
            v-model="value.action"
            class=" mt-4 form-select">
            <option
                v-for="a in workplaceActions"
                :key="a.value"
                :value="a.value">
                {{ a.title }}
            </option>
        </select>

        <div
            v-if="selectedWorkplace !== null">
            <div class="alert alert-info mt-4">
                <b>{{ $t('Location') }}</b>: {{ selectedWorkplace.room?.office?.address }}, {{
                    selectedWorkplace.room.name
                }}, {{ selectedWorkplace.room.description }}
            </div>

            <div class="form-group">
                <label for="">{{ $t('Select entities') }}</label>
                <select
                    v-model="value.entities"
                    class="form-select"
                    multiple
                    @change="onChangeEntities(value.entities)">
                    <option
                        v-for="ent in selectedWorkplace.entities"
                        :key="ent.id"
                        :value="ent.id">
                        {{ ent.name }} {{ ent.inventory_number ? ` - ${ent.inventory_number}` : '' }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>{{ $t('Select common entities (optional)') }}</label>
                <select
                    v-model="value.common"
                    class="form-select"
                    multiple
                    @change="onChangeCommon(value.common)">
                    <option
                        v-for="ent in selectedWorkplace.room.common_entities"
                        :key="ent.id"
                        :value="ent.id">
                        {{ ent.name }} {{ ent.inventory_number ? ` - ${ent.inventory_number}` : '' }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>
