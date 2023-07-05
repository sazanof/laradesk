<template>
    <div class="administration-wrapper">
        <SubSidebar>
            <div class="list-group list-group-flush">
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
        <div
            class="administration-content"
            data-simplebar>
            <router-view />
        </div>
    </div>
</template>

<script>
import FormatListNumberedIcon from 'vue-material-design-icons/FormatListNumbered.vue'
import TextShadowIcon from 'vue-material-design-icons/TextShadow.vue'
import SubSidebar from '../../elements/SubSidebar.vue'

export default {
    name: 'Administration',
    components: {
        SubSidebar,
        FormatListNumberedIcon,
        TextShadowIcon
    }
}
</script>

<style lang="scss" scoped>
.administration-wrapper {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    flex-wrap: wrap;
    height: calc(100vh - var(--header-height));

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
