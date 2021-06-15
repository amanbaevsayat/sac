<template>
    <div class="container-fluid">
        <form :action="route">
            <div>
                <div class="card mb-2" id="filter">
                    <div 
                        style="line-height: 25px; cursor: pointer;" 
                        @click="filterOpen = !filterOpen" 
                        class="card-header py-1"
                    >
                        Фильтр
                        <small class="float-right">
                            <a id="filter-toggle" class="btn btn-default btn-sm" title="Скрыть/показать">
                                <i class="fa fa-toggle-off " :class="{'fa-toggle-on': filterOpen}"></i>
                            </a>
                        </small>
                    </div>
                    <div class="card-body" v-show="filterOpen" :class="{slide: filterOpen}">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="started_at" class="col-form-label">Выберите продукт</label>
                                    <select v-model="data.productId" name="productId" class="form-control">
                                        <option v-for="(option, optionIndex) in products" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="started_at" class="col-form-label">С</label>
                                    <datetime
                                        type="date"
                                        v-model="data.from"
                                        input-class="form-control"
                                        valueZone="Asia/Almaty"
                                        value-zone="Asia/Almaty"
                                        zone="Asia/Almaty"
                                        format="dd LLLL"
                                        :auto="true"
                                    ></datetime>
                                    <input type="hidden" name="from" :value="convertDate(data.from, 'YYYY-MM-DD')">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="started_at" class="col-form-label">По</label>
                                    <datetime
                                        type="date"
                                        v-model="data.to"
                                        input-class="form-control"
                                        valueZone="Asia/Almaty"
                                        value-zone="Asia/Almaty"
                                        zone="Asia/Almaty"
                                        format="dd LLLL"
                                        :auto="true"
                                    ></datetime>
                                    <input type="hidden" name="to" :value="convertDate(data.to, 'YYYY-MM-DD')">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="started_at" class="col-form-label">Выберите период</label>
                                    <select v-model="data.period" name="period" class="form-control">
                                        <option v-for="(option, optionIndex) in periods" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button style="margin-bottom: 15px;" type="submit" class="btn btn-primary">Перейти</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>

        <div class="row" v-for="(chart, chartIndex) in charts" :key="chartIndex">
            <div class="col">
                <div style="display: inline-block;margin-bottom: 10px;" v-for="(item, seriesIndex) in chart.series" :key="seriesIndex">
                    <div v-if="item.editable == true">
                        <b-button v-b-toggle="'collapse-' + chartIndex + '-' + seriesIndex" class="m-1">{{ item.name }}</b-button>

                        <!-- Element to collapse -->
                        <b-collapse :id="'collapse-' + chartIndex + '-' + seriesIndex">
                            <b-card>
                                <div class="row">
                                    <div class="col-sm-3" v-for="(data, dataIndex) in item.data" :key="dataIndex">
                                        <div class="form-group">
                                            <label for="form_name">{{ convertDate(data.x, 'LL') }}</label>
                                            <textarea v-if="item.type" 
                                                @input="inputChangeEvent($event, chartIndex, seriesIndex, dataIndex)" 
                                                v-model="charts[chartIndex].series[seriesIndex].data[dataIndex]['y']" 
                                                id="" cols="30" rows="10"></textarea>
                                            <input 
                                                v-else
                                                @change="inputChangeEvent($event, chartIndex, seriesIndex, dataIndex)" 
                                                v-model="charts[chartIndex].series[seriesIndex].data[dataIndex]['y']" 
                                                type="number" 
                                                class="form-control" 
                                                >
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button @click="saveCustomData(item)" class="btn btn-primary">Сохранить данные</button>
                                </div>
                            </b-card>
                        </b-collapse>
                    </div>
                </div>
                <div class="card card-body" v-if="chart.type == 'highchart'">
                    <highcharts 
                        :options="charts[chartIndex]"
                        :updateArgs="[true, true, true]"
                        :ref="'chart-' + chartIndex"
                    ></highcharts>
                    <div v-for="(item, seriesIndex) in chart.series" :key="seriesIndex" style="font-size: 11px">
                        <p v-show="item.description" style="margin-bottom: 0px"><span :style="'font-size: 24px; color:' + item.color">•</span> - {{ item.description }}</p>
                    </div>
                </div>
                <div id="timeline-modal" class="modal">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">{{ timeline.title }}</h5>
                                <button type="button" class="close" @click="closeTimelineModal()">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <vue-editor v-model="timeline.data" />
                            </div>
                            <footer class="modal-footer">
                                <button @click="saveTimelineModal()" type="button" class="btn btn-primary">Сохранить</button>
                            </footer>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {Chart} from 'highcharts-vue'
import moment from 'moment-timezone';
import { VueEditor } from "vue2-editor";

    export default {
        props: [
            'dataProp',
            'productsProp',
            'periodsProp',
            'chartsProp',
            'routeProp'
        ],
        components: {
            highcharts: Chart ,
            VueEditor,
        },
        data() {
            return {
                filterOpen: false,
                route: this.routeProp,
                charts: this.chartsProp,
                // charts: [],
                products: this.productsProp,
                periods: this.periodsProp,
                data: {
                    from: moment(this.dataProp.from).tz('Asia/Almaty').format(),
                    to: moment(this.dataProp.to).tz('Asia/Almaty').format(),
                    productId: this.dataProp.productId,
                    period: this.dataProp.period,
                },
                spinnerData: {
                    loading: false,
                    color: '#6cb2eb',
                    size: '100px',
                },
                timeline: {
                    title: '',
                    data: '',
                    statisticsType: '',
                    key: '',
                }
            }
        },
        beforeMount() {
            this.charts.forEach(function(chart, chartIndex) {
                if (chart.type == 'timeline') {
                    chart.items.forEach((item, itemIndex) => {
                        this.charts[chartIndex].items[itemIndex].createdDate = new Date(item.createdDate);
                    });
                }
            }.bind(this));


            this.chartsProp.forEach(function(chart, index) {
                this.charts[index] = chart;
                this.charts[index].xAxis.labels = {
                    formatter: function(value) {
                        return moment(value.value).tz('Asia/Almaty').lang("ru").format('LL');
                    }
                };

                if (chart.chart.type == 'timeline') {
                    this.charts[index].plotOptions = {
                        series: {
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: (e) => {
                                        this.pointClick(e, index);
                                    }
                                }
                            }
                        }
                    };
                }
            }.bind(this));
        },
        methods: {
            pointClick(e, index) {
                this.chartsProp[index].series[0].data.forEach(function(series, i) {
                    if (e.point.category == series.x) {
                        console.log(index);
                        this.timeline = {
                            title: series.name,
                            data: series.description,
                            statisticsType: this.chartsProp[index].series[0].statisticsType,
                            key: series.x,
                        };
                    }
                }.bind(this));
                this.showTimelineModal();
            },
            clearTimeline() {
                this.timeline = {
                    title: '',
                    data: '',
                    statisticsType: '',
                    key: '',
                };
            },
            closeTimelineModal() {
                $('#timeline-modal').modal('hide');
            },
            saveTimelineModal() {
                this.saveTimelineData(this.timeline);
                this.closeTimelineModal();
            },
            showTimelineModal() {
                $('#timeline-modal').modal('show');
            },
            timelineEdit(items, statisticsType, e) {
                this.clearTimeline();

                items.forEach(function(item, index) {
                    if (item.id == e.eventId) {
                        this.timeline.data = item.body;
                        this.timeline.title = item.title;
                        this.timeline.key = item.key;
                    }
                }.bind(this));
                this.timeline.statisticsType = statisticsType;

                this.showTimelineModal();
            },
            saveTimelineData(timeline) {
                this.spinnerData.loading = true;
                let data = {
                    item: timeline,
                    productId: this.data.productId,
                    period: this.data.period,
                };
                axios.post(`/statistics/timeline`, data)
                .then(response => {
                    let message = response.data.message;
                    if (response.data.message) {
                        Vue.$toast.success(message);
                    }
                    this.spinnerData.loading = false;
                })
                .catch(err => {
                    this.spinnerData.loading = false;
                    throw err;
                });
            },
            // convertToItems(items) {
            //     let data = [];

            //     items.forEach((item, itemIndex, selfItems) => {
            //         data.push(item);

            //         data[itemIndex].createdDate = new Date(item.createdDate);
            //     });

            //     console.log(data);

            //     return data;
            // },
            saveCustomData(item) {
                this.spinnerData.loading = true;
                let data = {
                    item: item,
                    productId: this.data.productId,
                    period: this.data.period,
                };
                axios.post(`/statistics`, data)
                .then(response => {
                    let message = response.data.message;
                    if (response.data.message) {
                        Vue.$toast.success(message);
                    }
                    this.spinnerData.loading = false;
                })
                .catch(err => {
                    this.spinnerData.loading = false;
                    throw err;
                });
            },
            inputChangeEvent(e, chartIndex, seriesIndex, categoryIndex) {
                this.charts[chartIndex].series[seriesIndex].data[categoryIndex]['y'] = parseInt(e.target.value);
                this.$refs['chart-' + chartIndex][0].chart.series[seriesIndex].setData(this.charts[chartIndex].series[seriesIndex].data, true);
            },
            convertDate(date, format) {
                return moment(date).tz('Asia/Almaty').lang("ru").format(format);
            },
            submit() {
                this.spinnerData.loading = true;
                axios.post(`/statistics`, this.statistics)
                .then(response => {
                    let responseData = response.data;
                    console.log(response);
                    this.spinnerData.loading = false;
                    this.statistics.dates.from = responseData.from;
                    this.statistics.dates.to = responseData.to;
                    this.statistics.trials = responseData.trials;
                    this.statistics.customers = responseData.customers;
                    this.statistics.financial = responseData.financial;
                    this.statistics.payments = responseData.payments;
                })
                .catch(err => {
                    this.spinnerData.loading = false;
                    throw err;
                });
            }
        }
    }
</script>

<style scoped>
.v-spinner {
    width: 100%;
    height: 100%;
    text-align: center;
    position: absolute;
    background: #00000017;
}
.bg-baza {
    background: #ead1dc;
}
.bg-formula {
    background-color: #d9ead3;
}

.bd-baza {
    background: #ead1dc;
}
.bd-formula {
    background-color: #d9ead3;
}
.bd-warning {
    background-color: #ffed4a !important;
}
.table th, .table td {
    text-align: center;
}
</style>