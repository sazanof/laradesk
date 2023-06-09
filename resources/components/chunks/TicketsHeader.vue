<template>
    <thead>
        <tr>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('id')">
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
                {{ $t('Requester') }}
            </th>
            <th scope="col">
                {{ $t('Assignees') }}
            </th>
            <th scope="col">
                {{ $t('Approvals') }}
            </th>
            <th scope="col">
                {{ $t('Observers') }}
            </th>
            <th
                scope="col"
                class="clickable"
                @click="clickRow('created_at')">
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
        </tr>
    </thead>
</template>

<script>
import SortVariantIcon from 'vue-material-design-icons/SortVariant.vue'
import SortReverseVariantIcon from 'vue-material-design-icons/SortReverseVariant.vue'

export default {
    name: 'TicketsHeader',
    components: {
        SortVariantIcon,
        SortReverseVariantIcon
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
    /*position: sticky;
    z-index: 20;
    top: 40px;*/
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
