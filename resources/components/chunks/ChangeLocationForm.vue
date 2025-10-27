<script>
import ChevronRightIcon from 'vue-material-design-icons/ChevronRight.vue'
import OfficesMultiselect from '../elements/OfficesMultiselect.vue'
import RoomsMultiselect from '../elements/RoomsMultiselect.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
export default {
    name: 'ChangeLocationForm',
    components: {
        RoomsMultiselect,
        OfficesMultiselect,
        ChevronRightIcon
    },
    data() {
        return {
            loading: false,
            office: null,
            room: null,
            noRoom: false
        }
    },
    computed: {
        user() {
            return this.$store.getters['getUser']
        },
        disabled() {
            return (this.room === null && !this.noRoom) || this.office === null
        }
    },
    watch: {
        noRoom() {
            if (this.noRoom) {
                this.room = -1
            } else {
                this.room = null
            }
            this.emitter.emit('clear-room-value')
        }
    },
    created() {
        this.office = this.user?.office?.id
        this.room = this.user?.room?.id
    },
    methods: {
        setNoRoom() {
            this.noRoom = !this.noRoom
        },
        onClearOffice() {
            this.office = null
            this.room = null
        },
        onSelectOffice(e) {
            this.office = e.id
            this.room = null
        },
        async saveProfile() {
            this.loading = true
            await this.$store.dispatch('editProfile', {
                room_id: this.room,
                office_id: this.office
            }).catch(e => {
                toast.error(this.$t(e?.response?.data?.message))
                this.loading = false
                return false
            })
            this.loading = false
            toast.success(this.$t('Saved'))
        }

    }
}
</script>

<template>
    <div class="form-group">
        <OfficesMultiselect
            @select="onSelectOffice"
            @clear="onClearOffice" />
        <RoomsMultiselect
            v-model="room"
            class="mt-4"
            :disabled="noRoom"
            @select="room = $event.id"
            @clear="room = null" />
        <VCheckbox
            id="noRoom"
            v-model="noRoom"
            :label="$t('There is no cabinet number')"
            class="form-check-input"
            type="checkbox" />

        <VBtn
            :disabled="disabled || loading"
            prepend-icon="mdi-chevron-right"
            @click="saveProfile">
            {{ $t('Continue') }}
        </VBtn>
    </div>
</template>

<style scoped lang="scss">

</style>
