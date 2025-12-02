<template>
    <VApp>
        <VLayout class="fill-height">
            <VNavigationDrawer>
                <VTabs
                    density="compact"
                    direction="vertical">
                    <VTab
                        :to="{name:'offices'}"
                        prepend-icon="mdi-domain"
                        :text="$t('Offices') " />
                    <VTab
                        :to="{name:'adm_rooms'}"
                        prepend-icon="mdi-map-marker"
                        :text="$t('Rooms')" />
                    <VTab
                        prepend-icon="mdi-account-group"
                        :text="$t('Departments')"
                        to="/admin/management/departments" />
                    <VTab
                        prepend-icon="mdi-account-multiple"
                        :text="$t('Users')"
                        to="/admin/management/users" />
                    <VTab
                        prepend-icon="mdi-format-list-numbered"
                        :text="$t('Category management')"
                        to="/admin/management/categories" />
                    <VTab
                        prepend-icon="mdi-text-shadow"
                        :text="$t('Fields management')"
                        to="/admin/management/fields" />
                    <VTab
                        prepend-icon="mdi-newspaper"
                        :text="$t('News management')"
                        :to="{name:'news'}" />
                </VTabs>
            </VNavigationDrawer>
            <VMain
                class="fill-height">
                <VSheet class="pa-4 fill-height overflow-x-auto">
                    <router-view />
                </VSheet>
            </VMain>
        </VLayout>
    </VApp>
</template>

<script>
export default {
    name: 'Administration',
    components: {},
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
