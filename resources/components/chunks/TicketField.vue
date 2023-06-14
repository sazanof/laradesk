<template>
    <div class="ticket-field">
        <div class="row">
            <div class="col-md-4">
                <div class="name">
                    {{ field.field_name }}
                </div>
            </div>
            <div class="col-md-8">
                <div class="content">
                    {{ field.content }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TicketField',
    props: {
        field: {
            type: Object,
            required: true
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
        font-style: italic;
    }
}
</style>
