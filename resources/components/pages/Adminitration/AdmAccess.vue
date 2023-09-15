<template>
    <div
        v-if="isSuperAdmin"
        class="access-management">
        <div class="alert alert-light">
            {{
                $t('In this section, you, as a super-administrator, can assign other system administrators access to departments')
            }}
        </div>
        <div
            v-if="administrators"
            class="administrators">
            <div
                v-for="administrator in administrators"
                :key="administrator.id"
                class="administrator"
                :class="{selected: administrator.id === activeUser?.id}"
                @click="loadAccess(administrator)">
                <Avatar :user="administrator" />
                <div class="info">
                    <div class="name">
                        {{ administrator.firstname }} {{ administrator.lasstname }}
                    </div>
                    <div class="email">
                        {{ administrator.email }}
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="departments"
            class="departments">
            <div
                v-for="department in departments"
                :key="department"
                class="department"
                :class="{selected: isSelected(department)}"
                @click="setAccess(department)">
                <div class="name">
                    {{ department.name }}
                </div>
                <div class="description">
                    {{ department.description }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Avatar from '../../chunks/Avatar.vue'

export default {
    name: 'AdmAccess',
    components: {
        Avatar
    },
    data() {
        return {
            activeUser: null,
            activeUserDepartments: null,
            administrators: null
        }
    },
    computed: {
        isSuperAdmin() {
            return this.$store.getters['isSuperAdmin']
        },
        departments() {
            return this.$store.getters['getDepartments']
        }
    },
    async mounted() {
        await this.getAdministrators()
    },
    methods: {
        async getAdministrators() {
            this.administrators = await this.$store.dispatch('getAdministrators')
        },
        loadAccess(a) {
            if (this.activeUser === a) {
                this.activeUser = null
                this.activeUserDepartments = null
                return false
            }
            this.activeUser = a
            this.activeUserDepartments = null
            if (a.departments !== null) {
                this.activeUserDepartments = a.departments
            }
        },
        isSelected(d) {
            if (this.activeUserDepartments) {
                return this.activeUserDepartments.find(dep => {
                    return d.id === dep.department_id
                }) !== undefined
            }
            return false
        },
        async setAccess(d) {
            if (this.isSelected(d)) {
                await this.$store.dispatch('deleteAccess', {
                    admin_id: this.activeUser.id,
                    department_id: d.id
                }).then(() => {
                    this.activeUserDepartments = this.activeUserDepartments.filter(department => {
                        return d.id !== department.department_id
                    })
                })
            } else {
                await this.$store.dispatch('addAccess', {
                    admin_id: this.activeUser.id,
                    department_id: d.id
                }).then(() => {
                    d.department_id = d.id
                    this.activeUserDepartments.push(d)
                })
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.access-management {
    display: flex;
    flex-wrap: wrap;

    .administrators {
        width: 50%;

        .administrator {
            display: flex;
            align-items: center;
            padding: var(--padding-box);
            border-radius: var(--border-radius);
            border: 1px solid var(--bs-border-color);
            margin: 0 20px 20px 0;
            transition: var(--transition-duration);
            cursor: pointer;

            &.selected {
                background: var(--bs-purple);
                border-color: var(--bs-purple-darker);
                color: var(--bs-white);
            }

            .info {
                margin-left: 10px;

                .name {
                    font-weight: bold;
                }
            }
        }
    }

    .departments {
        width: 50%;

        .department {
            padding: var(--padding-box);
            border-radius: var(--border-radius);
            border: 1px solid var(--bs-border-color);
            margin-bottom: 20px;
            transition: var(--transition-duration);

            .name {
                font-weight: bold;
            }

            &.selected {
                background: var(--bs-purple);
                border-color: var(--bs-purple-darker);
                color: var(--bs-white);
            }
        }
    }
}
</style>
