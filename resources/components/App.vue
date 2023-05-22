<template>
  <div class="test">
    Appxxxx
  </div>
</template>

<script>
export default {
    name: 'App',
    created() {
        console.log({
            a: 1,
            b: 2
        })
    }
}
</script>

<style scoped>
.app {
    font-weight: normal;
}
</style>
