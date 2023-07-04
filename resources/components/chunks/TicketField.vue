<template>
    <div class="ticket-field">
        <div class="row">
            <div class="col-md-4">
                <div class="name">
                    {{ field.field_name }}
                </div>
            </div>
            <div class="col-md-8">
                <div
                    v-if="isFile"
                    class="content">
                    <a
                        target="_blank"
                        :href="`/user/tickets/file/${field.id}`"
                        class="download">
                        <DownLoadIcon :size="20" />
                        {{ fileName }}
                    </a>
                </div>
                <div
                    v-else
                    class="content">
                    {{ field.content }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DownLoadIcon from 'vue-material-design-icons/Download.vue'
import { TYPES } from '../../js/consts.js'

export default {
    name: 'TicketField',
    components: {
        DownLoadIcon
    },
    props: {
        field: {
            type: Object,
            required: true
        }
    },
    computed: {
        isFile() {
            return this.field.field_type === TYPES.TYPE_FILE
        },
        fileName() {
            return this.field.content.replace('/', '')
        }
    }
}
</script>

<style lang="scss" scoped>
.ticket-field {
    margin-bottom: 10px;

    .name {
        font-weight: bold;
    }

    .content {
        a.download {
            text-decoration: none;
        }
    }
}
</style>
