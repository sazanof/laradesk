<template>
    <div class="finish-profile-settings">
        <div class="inner">
            <div class="title">
                {{ $t('Hello') }}, {{ fullName }}
            </div>
            <div class="info">
                {{ $t('To use the system further, you must specify an office and an room') }} <br>
                {{ $t('If your office is not on the list, please contact us') }}
            </div>
            <div class="form-group">
                <label>{{ $t('Office') }}</label>
                <OfficesMultiselect
                    @select="office = $event.id"
                    @clear="office =null" />
            </div>
            <div class="form-group">
                <label>{{ $t('Room') }}</label>
                <input
                    v-if="!noRoom"
                    v-model="room"
                    :disabled="noRoom"
                    type="text"
                    class="form-control">
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
        </div>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import ChevronRightIcon from 'vue-material-design-icons/ChevronRight.vue'
import OfficesMultiselect from '../ elements/OfficesMultiselect.vue'

const toast = useToast()

export default {
    name: 'FinishProfileSettingsModal',
    components: {
        OfficesMultiselect,
        ChevronRightIcon
    },
    props: {
        user: {
            type: Object,
            required: true
        }
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
        disabled() {
            return this.room === null || this.office === null
        },
        fullName() {
            return `${this.user.firstname} ${this.user.lastname}`
        }
    },
    watch: {
        noRoom() {
            this.room = this.noRoom ? -1 : null
        }
    },
    methods: {
        setNoRoom() {
            this.noRoom = !this.noRoom
        },
        async saveProfile() {
            this.loading = true
            await this.$store.dispatch('editProfile', {
                room_id: this.room,
                office_id: this.office
            }).catch(e => {
                toast.error(this.$t(e?.response?.data?.message))
                this.loading = false
            })
            this.loading = false
        }

    }
}
</script>

<style lang="scss" scoped>
.finish-profile-settings {
    position: relative;
    display: flex;
    height: 100vh;
    align-items: center;
    justify-content: center;

    .inner {
        max-width: 450px;
        background-color: var(--bs-white);
        padding: var(--padding-box);
        border-radius: var(--border-radius);

        .title {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .info {
            font-style: italic;
            margin: 4px 0;
            font-size: var(--font-small);
        }
    }

}
</style>
