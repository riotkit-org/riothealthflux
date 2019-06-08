<template>
    <d-container fluid class="main-content-container px-4">
        <d-alert class="alert-royal-blue" show v-if="backendInitializationInProgress">
            <i class="fa fa-info mx-2"></i>
            The dashboard is not ready yet, please wait a few minutes until the data will be initially processed...
        </d-alert>

        <div v-if="ready">
            <d-row no-gutters class="page-header py-4">
                <d-col col sm="4" class="text-center text-sm-left mb-4 mb-sm-0">
                    <span class="text-uppercase page-subtitle">Health checks dashboard</span>
                    <h3 class="page-title">
                        {{ title }}
                    </h3>
                </d-col>
            </d-row>

            <!-- Succeeding & Failing count -->
            <d-row>
                <d-col lg v-for="(stats, idx) in failingAndSucceedingCount" :key="idx" class="mb-4">
                    <small-stats :id="`small-stats-${idx}`" variation="1" :chart-data="stats.datasets" :label="stats.label"
                                 :value="stats.value" :percentage="stats.percentage" :increase="stats.increase"
                                 :decrease="stats.decrease"/>
                </d-col>
            </d-row>

            <d-row>
                <d-col lg="4" md="12" sm="12" class="mb-4">
                    <current-status title="Current status" :dataset="current.checks"/>
                </d-col>

                <d-col lg="8" md="6" sm="12" class="mb-4">
                    <d-row>
                        <d-col lg="12" md="6" sm="12" class="mb-4">
                            <bar-chart :chart-data="uptimeByHourData" title="Uptime / Downtime comparison"/>
                        </d-col>

                        <d-col lg="6" md="6" sm="12" class="mb-4" v-if="topFailing.length">
                            <top-failing title="Top failing / Lowest downtime" :dataset="topFailing"/>
                        </d-col>

                        <d-col lg="6" md="6" sm="12" class="mb-4" v-if="mostUnstableIn24Hours.length">
                            <most-unstable title="Most unstable in 24 hours" :dataset="mostUnstableIn24Hours"/>
                        </d-col>

                        <d-col lg="6" md="6" sm="12" class="mb-4" v-if="recentlyResolved.length">
                            <recently-resolved title="Recently resolved" :dataset="recentlyResolved"/>
                        </d-col>
                    </d-row>
                </d-col>
            </d-row>
        </div>

        <loader v-else></loader>
    </d-container>
</template>

<script>
    import SmallStats from '@/components/common/SmallStats.vue';
    import CurrentStatus from '@/components/common/CurrentStatus.vue';
    import MostUnstable from '@/components/common/MostUnstable.vue';
    import TopFailing from '@/components/common/TopFailing.vue';
    import RecentlyResolved from '@/components/common/RecentlyResolved.vue';
    import BarChart from '@/components/common/BarChart.vue';
    import Loader from '@/components/common/Loader.vue';

    export default {
        components: {
            SmallStats,
            barChart: BarChart,
            currentStatus: CurrentStatus,
            mostUnstable: MostUnstable,
            topFailing: TopFailing,
            recentlyResolved: RecentlyResolved,
            loader: Loader
        },

        data() {
            return {
                title: '?',
                current: {
                    failingCount: 0,
                    successCount: 0,
                    checks: []
                },
                historicCountPerHourAndDay: {
                    failures: [1, 1, 1],
                    successes: [2, 2, 2]
                },
                mostUnstableIn24Hours: [],
                topFailing: [],
                recentlyResolved: [],
                ready: false,
                backendInitializationInProgress: false
            }
        },

        beforeMount() {
            this.fetchData();
            this.enableAutoRefresh();
        },

        methods: {
            enableAutoRefresh() {
                setInterval(() => this.fetchData(), 1000 * 30);
            },

            fetchData() {
                let lThis = this;
                this.$http.get('/api')
                    .then(response => response.json())
                    .then(json => {
                        lThis.title = json.title;

                        if (typeof json.stats.mostUnstableInCurrent24Hours == 'undefined') {
                            lThis.backendInitializationInProgress = true;
                            lThis.ready = false;
                            return
                        }

                        lThis.ready = true;
                        lThis.backendInitializationInProgress = false;
                        lThis.current.failingCount = json.stats.failingChecks;
                        lThis.current.successCount = json.stats.succeedingChecks;
                        lThis.current.checks = json.stats.nodesOrderedByStatus;
                        lThis.mostUnstableIn24Hours = json.stats.mostUnstableInCurrent24Hours;
                        lThis.topFailing = json.stats.topFailing;
                        lThis.recentlyResolved = json.stats.recentlyFixed;

                        lThis.historicCountPerHourAndDay.failures = Object.keys(json.stats.countByHour).map(key => {
                            return parseInt(json.stats.countByHour[key]['down'])
                        });

                        lThis.historicCountPerHourAndDay.successes = Object.keys(json.stats.countByHour).map(key => {
                            return parseInt(json.stats.countByHour[key]['up'])
                        });

                        lThis.historicCountPerHourAndDay.labels = Object.keys(json.stats.countByHour);
                })
            }
        },


        computed: {
            failingAndSucceedingCount() {
                return [
                    {
                        label: 'Success',
                        value: this.current.successCount,
                        percentage: '',
                        increase: true,
                        datasets: [{
                            label: '',
                            fill: 'start',
                            borderWidth: 2,
                            backgroundColor: 'rgba(23,198,113,0.1)',
                            borderColor: 'rgb(23,198,113)',
                            data: this.historicCountPerHourAndDay.successes,
                        }],
                    },
                    {
                        label: 'Failing',
                        value: this.current.failingCount,
                        percentage: '',
                        increase: true,
                        datasets: [{
                            label: '',
                            fill: 'start',
                            borderWidth: 2,
                            backgroundColor: '#f9e3e3',
                            borderColor: '#eaaeae',
                            data: this.historicCountPerHourAndDay.failures,
                        }],
                    }
                ];
            },

            uptimeByHourData() {
                return {
                    labels: this.historicCountPerHourAndDay.labels,
                    datasets: [{
                        label: 'Uptime',
                        fill: 'start',
                        data: this.historicCountPerHourAndDay.successes,
                        labels: this.historicCountPerHourAndDay.labels,
                        backgroundColor: 'rgba(0,123,255,0.1)',
                        borderColor: 'rgba(0,123,255,1)',
                        pointBackgroundColor: '#ffffff',
                        pointHoverBackgroundColor: 'rgb(0,123,255)',
                        borderWidth: 1.5,
                        pointRadius: 5,
                        pointHoverRadius: 3,
                    }, {
                        label: 'Downtime',
                        fill: 'start',
                        data: this.historicCountPerHourAndDay.failures,
                        backgroundColor: 'rgba(255,65,105,0.1)',
                        borderColor: 'rgba(255,65,105,1)',
                        pointBackgroundColor: '#ffffff',
                        pointHoverBackgroundColor: 'rgba(255,65,105,1)',
                        borderDash: [3, 3],
                        borderWidth: 1,
                        pointRadius: 0,
                        pointHoverRadius: 2,
                        pointBorderColor: 'rgba(255,65,105,1)',
                    }],
                }
            }
        },
    };
</script>

