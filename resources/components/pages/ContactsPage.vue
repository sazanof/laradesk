<script>
import debounce from '../../js/helpers/debounce.js'
import ContactCard from '../chunks/ContactCard.vue'

export default {
    name: 'ContactsPage',
    components: {
        ContactCard
    },
    data() {
        return {
            page: 1,
            term: null,
            contacts: [],
            debounceFn: debounce(this.getContacts, 500)
        }
    },
    async created() {
        await this.getContacts()
    },
    methods: {
        async getContacts() {
            this.contacts = await this.$store.dispatch('getContacts', {
                page: this.page,
                term: this.term
            })
        },
        async changePage(e) {
            this.page = e
            await this.getContacts()
        },
        async searchTerm(t) {
            this.page = 1
            await this.debounceFn()
        }
    }
}
</script>

<template>
    <VContainer>
        <VRow>
            <VCol cols="12">
                <VTextField
                    v-model="term"
                    clearable
                    :label="$t('Search text')"
                    prepend-inner-icon="mdi-magnify"
                    @update:model-value="searchTerm" />
            </VCol>
            <VCol
                v-for="contact in contacts.data"
                :key="contact.id"
                cols="12"
                md="4"
                sm="6">
                <ContactCard
                    :size="64"
                    :user="contact" />
            </VCol>
        </VRow>
        <VRow>
            <VCol cols="12">
                <VPagination
                    rounded="lg"
                    density="comfortable"
                    active-color="deep-purple"
                    :total-visible="11"
                    :length="contacts.last_page"
                    @update:model-value="changePage" />
            </VCol>
        </VRow>
    </VContainer>
</template>

<style scoped lang="scss">

</style>
