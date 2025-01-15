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
            <ChangeLocationForm />
        </div>
    </div>
</template>

<script>
import ChangeLocationForm from '@/components/chunks/ChangeLocationForm.vue'

export default {
    name: 'FinishProfileSettingsModal',
    components: {
        ChangeLocationForm
    },
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    computed: {
        fullName() {
            return `${this.user.firstname} ${this.user.lastname}`
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
