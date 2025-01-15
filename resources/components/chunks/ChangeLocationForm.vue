<script>
import ChevronRightIcon from 'vue-material-design-icons/ChevronRight.vue'
import OfficesMultiselect from '@/components/elements/OfficesMultiselect.vue'
import RoomsMultiselect from '@/components/elements/RoomsMultiselect.vue'
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
        this.room = this.user?.room.id
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
        <label>{{ $t('Office') }}</label>
        <OfficesMultiselect
            @select="onSelectOffice"
            @clear="onClearOffice" />
    </div>
    <div class="form-group">
        <label>{{ $t('Room') }}</label>
        <RoomsMultiselect
            v-model="room"
            :disabled="noRoom"
            @select="room = $event.id"
            @clear="room = null" />
        <div class="form-check">
            <input
                id="noRoom"
                class="form-check-input"
                type="checkbox"
                @change="setNoRoom">
            <label
                class="form-check-label"
                for="noRoom">
                {{ $t('There is no cabinet number') }}
            </label>
        </div>
    </div>
    <div class="form-group">
        <button
            class="btn btn-primary w-100"
            :disabled="disabled || loading"
            @click="saveProfile">
            <ChevronRightIcon />
            {{ $t('Continue') }}
        </button>
    </div>
</template>

<style scoped lang="scss">

</style>
