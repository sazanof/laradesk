<template>
    <div class="office">
        <div class="name">
            {{ office.name }}
        </div>
        <div class="address">
            {{ office.address }}
        </div>
        <div class="buttons">
            <button
                class="btn btn-icon btn-purple"
                @click="$emit('on-edit-click',office)">
                <PencilIcon :size="20" />
            </button>
            <button
                class="btn btn-icon btn-danger"
                @click="deleteOffice">
                <TrashCanIcon :size="20" />
            </button>
        </div>
        <ConfirmDialog ref="deleteOffice" />
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import ConfirmDialog from '../elements/ConfirmDialog.vue'
import PencilIcon from 'vue-material-design-icons/Pencil.vue'
import TrashCanIcon from 'vue-material-design-icons/TrashCan.vue'

const toast = useToast()

export default {
    name: 'AdmOfficeItem',
    components: {
        PencilIcon,
        TrashCanIcon,
        ConfirmDialog
    },
    props: {
        office: {
            type: Object,
            required: true
        }
    },
    emits: [ 'on-edit-click', 'on-delete-office' ],
    methods: {
        async deleteOffice() {
            const ok = await this.$refs.deleteOffice.show({
                title: this.$t('Delete office'),
                message: this.$t('Are you sure you want to delete this office?'),
                okButton: this.$t('Delete')
            })
            if (ok) {
                this.$emit('on-delete-office', this.office)

            }
        }
    }
}
</script>

<style lang="scss" scoped>
.office {
    position: relative;
    transition: var(--transition-duration);
    border-radius: var(--border-radius);
    padding: calc(var(--padding-box) / 2);
    margin-bottom: var(--padding-box);
    border: 1px solid var(--bs-border-color);

    &:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .name {
        font-weight: bold;
        margin-bottom: 4px;
    }

    .address {
        font-size: var(--font-small);
    }

    .buttons {
        position: absolute;
        z-index: 2;
        top: 6px;
        right: 6px;

        .btn {
            margin-left: 6px;
        }
    }
}
</style>
