<template>
    <div class="statistics">
        <h3>{{ $t('Statistics') }}</h3>
        <div class="form">
            <div class="form-group">
                <label for="">{{ $t('Date range') }}</label>
                <div class="row">
                    <div class="col-md-6">
                        <VueDatePicker
                            v-model="dates.start"
                            auto-apply
                            :enable-time-picker="false"
                            :locale="$i18n.locale"
                            format="dd.MM.yyyy" />
                    </div>
                    <div class="col-md-6">
                        <VueDatePicker
                            v-model="dates.end"
                            auto-apply
                            :enable-time-picker="false"
                            :locale="$i18n.locale"
                            format="dd.MM.yyyy"
                            :min-date="dates.start" />
                    </div>
                </div>
            </div>
            <div
                class="form-group">
                <label for="">{{ $t('Statistics type') }}</label>
                <MultiselectElement
                    v-model="type"
                    :object="true"
                    label="name"
                    value-prop="value"
                    track-by="value"
                    :options="types" />
            </div>

            <div
                v-if="needDepartment"
                class="form-group">
                <label for="">{{ $t('Department') }}</label>
                <MultiselectElement
                    v-model="department"
                    :placeholder="$t('Select department')"
                    :object="true"
                    label="name"
                    value-prop="id"
                    track-by="id"
                    :options="departments" />
            </div>
            <div class="form-group text-center">
                <button
                    :disabled="disabled"
                    class="btn btn-purple"
                    @click="getStatistics">
                    <ChartLineIcon
                        v-if="mode==='line'"
                        :size="20" />
                    <ChartBarIcon
                        v-if="mode==='bar'"
                        :size="20" />
                    {{ $t('Generate') }}
                </button>
                <button
                    v-if="chartData"
                    class="btn btn-purple download-chart"
                    @click="downloadChart">
                    <DownLoadIcon
                        :size="20" />
                    {{ $t('Save') }}
                </button>
                <button
                    v-if="chartData"
                    class="btn btn-icon download-chart"
                    @click="changeMode">
                    <ChartLineIcon
                        v-if="mode==='bar'"
                        :size="20" />
                    <ChartBarIcon
                        v-if="mode==='line'"
                        :size="20" />
                </button>
            </div>
        </div>

        <Bar
            v-if="chartData && mode === 'bar'"
            ref="chart"
            :options="chartOptions"
            :data="chartData" />
        <Line
            v-if="chartData && mode === 'line'"
            ref="chart"
            :options="chartOptions"
            :data="chartData" />
    </div>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker'
import { Bar, Line } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    PointElement,
    CategoryScale,
    LinearScale,
    LineElement
} from 'chart.js'
import MultiselectElement from '../elements/MultiselectElement.vue'
import DownLoadIcon from 'vue-material-design-icons/Download.vue'
import ChartLineIcon from 'vue-material-design-icons/ChartLine.vue'
import ChartPieIcon from 'vue-material-design-icons/ChartPie.vue'
import ChartBarIcon from 'vue-material-design-icons/ChartBar.vue'

ChartJS.register(Title, Tooltip, Legend, BarElement, LineElement, PointElement, CategoryScale, LinearScale)

export default {
    name: 'Statistics',
    components: {
        MultiselectElement,
        VueDatePicker,
        Bar,
        Line,
        DownLoadIcon,
        ChartLineIcon,
        ChartPieIcon,
        ChartBarIcon
    },
    data() {
        return {
            mode: 'bar',
            result: [],
            dates: {
                start: new Date(),
                end: new Date()
            },
            department: null,
            type: null,
            types: [
                {
                    name: this.$t('Statistics per department'),
                    value: 1
                },
                {
                    name: this.$t('Statistics per user'),
                    value: 2
                },

                {
                    name: this.$t('Statistics per category'),
                    value: 3
                },
                {
                    name: this.$t('Statistics per status'),
                    value: 4
                }
            ],
            chartData: null,
            chartOptions: {
                responsive: true
            }
        }
    },
    computed: {
        departments() {
            return this.$store.getters['getActiveDepartments']
        },
        needDepartment() {
            return this.type !== null && this.type.value !== 1
        },
        disabled() {
            return this.type === null || (this.needDepartment && !this.department)
        },
        datasets() {
            return this.result
        }
    },
    methods: {
        async getStatistics() {
            this.chartData = await this.$store.dispatch('getStatistics', {
                type: this.type?.value,
                department: this.department?.id,
                dates: {
                    start: this.dates.start.toLocaleDateString(),
                    end: this.dates.end.toLocaleDateString()
                }

            })
        },
        downloadChart() {
            const a = document.createElement('a')
            const canvas = this.$refs.chart.chart.canvas

            const resizedCanvas = document.createElement('canvas')
            const resizedContext = resizedCanvas.getContext('2d')

            resizedCanvas.height = canvas.height
            resizedCanvas.width = canvas.width
            resizedContext.fillStyle = 'rgb(255,255,255)'

            resizedContext.fillRect(0, 0, resizedCanvas.width, resizedCanvas.height)
            resizedContext.drawImage(canvas, 0, 0)

            a.href = resizedCanvas.toDataURL('image/jpg')
            a.download = 'Chart.jpg'
            a.click()
        },
        changeMode() {
            this.mode = this.mode === 'bar' ? 'line' : 'bar'
        }
    }
}
</script>

<style lang="scss" scoped>
.statistics {
    padding: var(--padding-box);

    .form {
        padding-bottom: var(--padding-box);
        margin-bottom: var(--padding-box);
        border-bottom: 1px solid var(--bs-border-color);

        .download-chart {
            margin-left: 6px;
        }
    }
}
</style>
