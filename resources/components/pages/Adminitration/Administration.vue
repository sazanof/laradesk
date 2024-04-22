<template>
    <div
        class="administration-wrapper"
        :class="{'is-mobile': isMobile}">
        <SubSidebar>
            <div class="list-group list-group-flush">
                <router-link
                    class="list-group-item"
                    :to="{name:'offices'}">
                    <DomainIcon :size="18" />
                    {{ $t('Offices') }}
                </router-link>
                <router-link
                    class="list-group-item"
                    :to="{name:'adm_rooms'}">
                    <MapMarkerIcon :size="18" />
                    {{ $t('Rooms') }}
                </router-link>
                <router-link
                    class="list-group-item"
                    to="/admin/management/departments">
                    <AccountGroupIcon :size="18" />
                    {{ $t('Departments') }}
                </router-link>
                <router-link
                    class="list-group-item"
                    to="/admin/management/users">
                    <AccountMultipleIcon :size="18" />
                    {{ $t('Users') }}
                </router-link>
                <router-link
                    class="list-group-item"
                    to="/admin/management">
                    <FormatListNumberedIcon :size="18" />
                    {{ $t('Category management') }}
                </router-link>
                <router-link
                    class="list-group-item"
                    to="/admin/management/fields">
                    <TextShadowIcon :size="18" />
                    {{ $t('Fields management') }}
                </router-link>
            </div>
        </SubSidebar>
        <SimpleBar
            class="administration-content">
            <router-view />
        </SimpleBar>
    </div>
</template>

<script>
import SimpleBar from 'simplebar-vue'
import MapMarkerIcon from 'vue-material-design-icons/MapMarker.vue'
import FormatListNumberedIcon from 'vue-material-design-icons/FormatListNumbered.vue'
import TextShadowIcon from 'vue-material-design-icons/TextShadow.vue'
import AccountGroupIcon from 'vue-material-design-icons/AccountGroup.vue'
import SubSidebar from '../../elements/SubSidebar.vue'
import AccountMultipleIcon from 'vue-material-design-icons/AccountMultiple.vue'
import DomainIcon from 'vue-material-design-icons/Domain.vue'

export default {
    name: 'Administration',
    components: {
        SimpleBar,
        SubSidebar,
        FormatListNumberedIcon,
        TextShadowIcon,
        AccountGroupIcon,
        DomainIcon,
        AccountMultipleIcon,
        MapMarkerIcon
    },
    computed: {
        isMobile() {
            return this.$store.getters['isMobile']
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
