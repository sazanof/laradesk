<template>
    <thead>
        <tr>
            <th
                class="clickable"
                @click="clickRow('id')">
                <VIcon icon="mdi-pound" />
                {{ $t('Number') }}
                <div
                    v-if="field === 'id'"
                    class="sort">
                    <VIcon
                        v-if="dir === 'desc'"
                        icon="mdi-sort-variant" />
                    <VIcon
                        v-else
                        icon="mdi-sort-reverse-variant" />
                </div>
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('subject')">
                <VIcon
                    icon="mdi-label-variant" />
                {{ $t('Subject') }}
                <div
                    v-if="field === 'subject'"
                    class="sort">
                    <VIcon
                        v-if="dir === 'desc'"
                        icon="mdi-sort-variant" />
                    <VIcon
                        v-else
                        icon="mdi-sort-reverse-variant" />
                </div>
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('category_id')">
                <VIcon
                    icon="mdi-format-list-group"
                    :size="18" />
                {{ $t('Category') }}
                <div
                    v-if="field === 'category_id'"
                    class="sort">
                    <VIcon
                        v-if="dir === 'desc'"
                        icon="mdi-sort-variant" />
                    <VIcon
                        v-else
                        icon="mdi-sort-reverse-variant" />
                </div>
            </th>
            <th scope="col">
                <VIcon icon="mdi-account-multiple" />
                {{ $t('Participants') }}
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('created_at')">
                <VIcon icon="mdi-clock" />
                {{ $t('Created at') }}
                <div
                    v-if="field === 'created_at'"
                    class="sort">
                    <VIcon
                        v-if="dir === 'desc'"
                        icon="mdi-sort-variant" />
                    <VIcon
                        v-else
                        icon="mdi-sort-reverse-variant" />
                </div>
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('solved_at')">
                <VIcon icon="mdi-clock" />
                {{ $t('Solved at') }}
                <div
                    v-if="field === 'solved_at'"
                    class="sort">
                    <VIcon
                        v-if="dir === 'desc'"
                        icon="mdi-sort-variant" />
                    <VIcon
                        v-else
                        icon="mdi-sort-reverse-variant" />
                </div>
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('closed_at')">
                <VIcon icon="mdi-clock" />
                {{ $t('Closed at') }}
                <div
                    v-if="field === 'closed_at'"
                    class="sort">
                    <VIcon
                        v-if="dir === 'desc'"
                        icon="mdi-sort-variant" />
                    <VIcon
                        v-else
                        icon="mdi-sort-reverse-variant" />
                </div>
            </th>
        </tr>
    </thead>
</template>

<script>
import PoundIcon from 'vue-material-design-icons/Pound.vue'
import FormatListGroupIcon from 'vue-material-design-icons/FormatListGroup.vue'
import AccountMultipleIcon from 'vue-material-design-icons/AccountMultiple.vue'
import SortVariantIcon from 'vue-material-design-icons/SortVariant.vue'
import SortReverseVariantIcon from 'vue-material-design-icons/SortReverseVariant.vue'
import ClockIcon from 'vue-material-design-icons/Clock.vue'

export default {
    name: 'TicketsHeader',
    components: {
        SortVariantIcon,
        SortReverseVariantIcon,
        PoundIcon,
        FormatListGroupIcon,
        AccountMultipleIcon,
        ClockIcon
    },
    props: {
        filter: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-row-click' ],
    data() {
        return {
            field: this.filter.field,
            dir: this.filter.dir
        }
    },
    methods: {
        clickRow(field) {
            this.field = field
            this.dir = this.dir === 'asc' ? 'desc' : 'asc'
            this.$emit('on-row-click', {
                field: this.field,
                dir: this.dir
            })
        }
    }
}
</script>

<style lang="scss" scoped>
thead {

    ::v-deep(.material-design-icon) {
        position: relative;
        padding: 0;
        top: -1px
    }

    background: var(--bs-white);

    .clickable {
        cursor: pointer;
        position: relative;

        .sort {
            position: absolute;
            right: 10px;
            top: 16px
        }
    }
}
</style>
