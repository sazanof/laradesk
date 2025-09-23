<template>
    <VAutocomplete
        v-model="selectedUsers"
        multiple
        chips
        closable-chips
        :no-data-text="$t('The list is empty')"
        :items="users"
        item-value="id"
        return-object
        no-filter
        @update:model-value="$emit('on-users-changed',$event)"
        @update:search="getUsers">
        <template #item="{ props,item }">
            <VListItem
                density="compact"
                lines="three"
                v-bind="props"
                :title="item.raw.full_name">
                <template #prepend>
                    <Avatar
                        :user="item.raw" />
                </template>
                <template #subtitle>
                    {{ item.raw.department }}<br>{{ item.raw.position }}
                </template>
            </VListItem>
        </template>
        <template #chip="{ props,item }">
            <VChip
                v-bind="props"
                :text="item.raw.full_name">
                <template #prepend>
                    <Avatar
                        class="ml-n2 mr-2"
                        :user="item.raw" />
                </template>
            </VChip>
        </template>
    </VAutocomplete>
</template>

<script>
import Avatar from '../chunks/Avatar.vue'

export default {
    name: 'UsersMultiselect',
    components: {
        Avatar
    },
    props: {
        value: {
            type: Object,
            default: null
        },
        mode: {
            type: String,
            default: 'tags'
        },
        department: {
            type: Number,
            default: null
        }
    },
    emits: [ 'on-users-changed' ],
    data() {
        return {
            users: [],
            selectedUsers: [],
            searchKey: 0
        }
    },
    watch: {
        value() {
            this.selectedUsers = this.value
        },
        async department() {
            this.users = await this.$store.dispatch('searchUsers', { term: null, department: this.department })
        }
    },
    async created() {
        this.selectedUsers = this.value
    },
    methods: {
        async getUsers(term) {
            if (term.length > 2) {
                this.users = await this.$store.dispatch('searchUsers', { term, department: this.department })
            }
        },
        clear() {
            this.selectedUsers = []
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
