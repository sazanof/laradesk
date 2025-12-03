<template>
    <VApp>
        <VLayout class="fill-height">
            <VNavigationDrawer v-model="opened">
                <VList slim>
                    <VListItem
                        :to="{name:'offices'}"
                        prepend-icon="mdi-domain"
                        :title="$t('Offices') " />
                    <VListItem
                        :to="{name:'adm_rooms'}"
                        prepend-icon="mdi-map-marker"
                        :title="$t('Rooms')" />
                    <VListItem
                        prepend-icon="mdi-account-group"
                        :title="$t('Departments')"
                        to="/admin/management/departments" />
                    <VListItem
                        prepend-icon="mdi-account-multiple"
                        :title="$t('Users')"
                        to="/admin/management/users" />
                    <VListItem
                        prepend-icon="mdi-format-list-numbered"
                        :title="$t('Category management')"
                        to="/admin/management/categories" />
                    <VListItem
                        prepend-icon="mdi-text-shadow"
                        :title="$t('Fields management')"
                        to="/admin/management/fields" />
                    <VListItem
                        prepend-icon="mdi-newspaper"
                        :title="$t('News management')"
                        :to="{name:'news'}" />
                </VList>
            </VNavigationDrawer>
            <VMain
                class="fill-height">
                <VSheet class="pa-4 fill-height overflow-x-auto position-relative">
                    <VBtn
                        class="mb-2"
                        density="compact"
                        :icon="!opened ? 'mdi-chevron-right' : 'mdi-chevron-left'"
                        @click="opened = !opened" />
                    <router-view />
                </VSheet>
            </VMain>
        </VLayout>
    </VApp>
</template>

<script>
export default {
    name: 'Administration',
    data() {
        return {
            opened: true
        }
    },
    created() {
        if (this.$route.path === '/admin/management') {
            this.$router.push('/admin/management/categories')
        }
    }
}
</script>

<style lang="scss" scoped>
.administration-wrapper {
    display: flex;
    align-items: stretch;
    justify-content: start;
    flex-wrap: wrap;
    height: calc(100vh - var(--header-height));

    &.is-mobile {
        .list-group {
            width: 46px;
            font-size: 0;
        }
    }

    a {
        color: var(--color-text);
        text-decoration: none;
        display: block;

        .material-design-icon {
            position: relative;
            top: -1px;
            margin-right: 4px;
        }

        &.router-link-exact-active {
            background-color: var(--bs-light);
        }
    }


    .administration-content {
        width: calc(100% - 300px);
        padding: var(--padding-box);
        height: inherit;
    }
}
</style>
