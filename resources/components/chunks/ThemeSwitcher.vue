<script>
export default {
    name: 'ThemeSwitcher',
    computed: {
        theme() {
            return this.$store.getters['getTheme']
        }
    },
    methods: {
        switchTheme() {
            const newTheme = this.theme === 'dark' ? 'light' : 'dark'
            this.$store.commit('setTheme', newTheme)
        }
    }
}
</script>

<template>
    <VBtn
        density="comfortable"
        variant="plain"
        :color="theme === 'dark' ? 'white' : 'dark'"
        :icon="theme === 'light' ?'mdi-weather-sunny' :'mdi-weather-night'"
        @click="switchTheme" />
</template>

<style lang="scss" scoped>

</style>
