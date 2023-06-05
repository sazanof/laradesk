<template>
    <Multiselect
        ref="multiselect"
        v-model="selectedUsers"
        :searchable="true"
        :options="users"
        :object="true"
        label="email"
        value-prop="id"
        track-by="id"
        mode="tags"
        :filter-results="false"
        :close-on-select="false"
        @search-change="getUsers">
        <template #option="{ option }">
            <div class="user-option">
                <div class="pic">
                    <Avatar
                        :user="option"
                        :size="32" />
                </div>
                <div class="name">
                    {{ option.firstname }} {{ option.lastname }}
                    <div class="position">
                        {{ option.department }}, {{ option.position }}
                    </div>
                </div>
            </div>
        </template>
        <template #tag="{ option, handleTagRemove, disabled }">
            <div
                class="multiselect-tag is-user"
                :class="{
                    'is-disabled': disabled
                }">
                <Avatar
                    :user="option"
                    :size="24" />
                {{ option.firstname }} {{ option.lastname }}
                <span
                    class="multiselect-tag-remove"
                    @click="handleTagRemove(option, $event)">
                    <span class="multiselect-tag-remove-icon" />
                </span>
            </div>
        </template>
    </Multiselect>
</template>

<script>
import Avatar from '../chunks/Avatar.vue'
import Multiselect from '@vueform/multiselect'

export default {
    name: 'UsersMultiselect',
    components: {
        Avatar,
        Multiselect
    },
    props: {
        value: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            users: null,
            selectedUsers: null
        }
    },
    methods: {
        async getUsers(term) {
            if (term.length > 2) {
                this.users = await this.$store.dispatch('searchUsers', term)
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.user-option {
    display: flex;

    .pic {
        width: 32px;
    }

    .name {
        margin-left: 6px;

        .position {
            font-size: var(--font-small);
            margin-top: 4px;
        }
    }
}
</style>
