<script>
export default {
    name: 'SurmWorkplaceField',
    props: {
        field: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            value: {
                workplaceId: null,
                common: [],
                user: []
            },
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
    },
    methods: {
        async getWorkplaces() {
            this.workplaces = await this.$store.dispatch('getSurmWorkplacesByUsernameAndRoom')
        },
        onSelectWorkplace(e) {
            if (this.value.workplaceId === null) {
                this.selectedWorkplace = null
            } else {
                this.selectedWorkplace = this.workplaces.find(wp => wp.id === this.value.workplaceId)
            }
        }
    }
}
</script>

<template>
    <div>
        <select
            v-model="value.workplaceId"
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
                    v-model="value.user"
                    class="form-select"
                    name=""
                    multiple>
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
                    name=""
                    multiple>
                    <option
                        v-for="ent in selectedWorkplace.room.common_entities"
                        :key="ent.id"
                        :value="ent.id">
                        {{ ent.name }} {{ ent.inventory_number ? ` - ${ent.inventory_number}` : '' }}
                    </option>
                </select>
            </div>
        </div>

        <code>{{ value }}</code>
        <!--        <pre>-->
        <!--        {{ workplaces }}-->
        <!--    </pre>-->
    </div>
</template>

<style scoped lang="scss">

</style>
