<template>
    <Multiselect
        ref="multiselect"
        v-model="selectedUsers"
        :placeholder="$t('Type the text')"
        :no-options-text="$t('The list is empty')"
        :multiple-label="labelFN"
        :searchable="true"
        :options="users"
        :object="true"
        label="email"
        value-prop="id"
        track-by="id"
        :mode="mode"
        :filter-results="false"
        :close-on-select="false"
        @change="$emit('on-users-changed',$event)"
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
                class="multiselect-user-tag is-user"
                :class="{
                    'is-disabled': disabled
                }">
                <Avatar
                    :user="option"
                    :size="24" />
                <div class="name">
                    {{ option.firstname }} {{ option.lastname }}
                </div>
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
            users: null,
            selectedUsers: null
        }
    },
    watch: {
        value() {
            this.selectedUsers = this.value
        }
    },
    created() {
        this.selectedUsers = this.value
    },
    methods: {
        async getUsers(term) {
            if (term.length > 2) {
                this.users = await this.$store.dispatch('searchUsers', { term, department: this.department })
            }
        },
        clear() {
            this.selectedUsers = null
            this.$refs.multiselect.clear()
        },
        labelFN(val) {
            return this.$tc('{count} users', { count: val.length })
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

.multiselect-user-tag {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: var(--border-radius);
    background: rgba(0, 0, 0, 0.1);

    .name {
        margin: 0 4px;
    }
}
</style>
