<template>
    <thead>
        <tr>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('id')">
                <PoundIcon :size="18" />
                {{ $t('Number') }}
                <div
                    v-if="field === 'id'"
                    class="sort">
                    <SortVariantIcon
                        v-if="dir === 'desc'"
                        :size="18" />
                    <SortReverseVariantIcon
                        v-else
                        :size="18" />
                </div>
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('subject')">
                <LabelVariantIcon :size="18" />
                {{ $t('Subject') }}
                <div
                    v-if="field === 'subject'"
                    class="sort">
                    <SortVariantIcon
                        v-if="dir === 'desc'"
                        :size="18" />
                    <SortReverseVariantIcon
                        v-else
                        :size="18" />
                </div>
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('category_id')">
                <FormatListGroupIcon :size="18" />
                {{ $t('Category') }}
                <div
                    v-if="field === 'category_id'"
                    class="sort">
                    <SortVariantIcon
                        v-if="dir === 'desc'"
                        :size="18" />
                    <SortReverseVariantIcon
                        v-else
                        :size="18" />
                </div>
            </th>
            <th scope="col">
                <AccountMultipleIcon :size="18" />
                {{ $t('Participants') }}
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('created_at')">
                <ClockIcon :size="18" />
                {{ $t('Created at') }}
                <div
                    v-if="field === 'created_at'"
                    class="sort">
                    <SortVariantIcon
                        v-if="dir === 'desc'"
                        :size="18" />
                    <SortReverseVariantIcon
                        v-else
                        :size="18" />
                </div>
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('solved_at')">
                <ClockIcon :size="18" />
                {{ $t('Solved at') }}
                <div
                    v-if="field === 'solved_at'"
                    class="sort">
                    <SortVariantIcon
                        v-if="dir === 'desc'"
                        :size="18" />
                    <SortReverseVariantIcon
                        v-else
                        :size="18" />
                </div>
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('closed_at')">
                <ClockIcon :size="18" />
                {{ $t('Closed at') }}
                <div
                    v-if="field === 'closed_at'"
                    class="sort">
                    <SortVariantIcon
                        v-if="dir === 'desc'"
                        :size="18" />
                    <SortReverseVariantIcon
                        v-else
                        :size="18" />
                </div>
            </th>
        </tr>
    </thead>
</template>

<script>
import PoundIcon from 'vue-material-design-icons/Pound.vue'
import FormatListGroupIcon from 'vue-material-design-icons/FormatListGroup.vue'
import AccountMultipleIcon from 'vue-material-design-icons/AccountMultiple.vue'
import LabelVariantIcon from 'vue-material-design-icons/LabelVariant.vue'
import SortVariantIcon from 'vue-material-design-icons/SortVariant.vue'
import SortReverseVariantIcon from 'vue-material-design-icons/SortReverseVariant.vue'
import ClockIcon from 'vue-material-design-icons/Clock.vue'

export default {
    name: 'TicketsHeader',
    components: {
        SortVariantIcon,
        SortReverseVariantIcon,
        LabelVariantIcon,
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
            top: 8px
        }
    }
}
</style>
