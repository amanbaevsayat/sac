<template>
    <div class="container-fluid">
        <form :action="route">
            <pulse-loader class="spinner" :loading="spinnerData.loading" :color="spinnerData.color" :size="spinnerData.size"></pulse-loader>
            <div class="row">
                <div class="col">
                    <div class="card card-body">
                        <div class="form-group">
                            <label for="started_at" class="col-form-label">Выберите продукт</label>
                            <select v-model="data.productId" name="productId" class="form-control">
                                <option v-for="(option, optionIndex) in products" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card card-body">
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
                </div>
                <div class="col">
                    <div class="card card-body">
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
                </div>
            </div>
            <button style="margin-bottom: 15px;" type="submit" class="btn btn-primary">Перейти</button>
        </form>

        <div class="row" v-for="(chart, chartIndex) in charts" :key="chartIndex">
            <div class="col">
                <div v-for="(item, seriesIndex) in chart.series" :key="seriesIndex">
                    <div class="card card-body" v-if="item.editable == true">
                        <h3>{{ item.name }}</h3>
                        <div class="row">
                            <div class="col-sm-3" v-for="(data, dataIndex) in item.data" :key="dataIndex">
                                <div class="form-group">
                                    <label for="form_name">{{ convertDate(data.x, 'LL') }}</label>
                                    <input 
                                        @change="inputChangeEvent($event, chartIndex, seriesIndex, dataIndex)" 
                                        v-model="charts[chartIndex].series[seriesIndex].data[dataIndex]['y']" 
                                        type="number" 
                                        class="form-control" 
                                        >
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <button @click="saveCustomData(item)" class="btn btn-primary">Сохранить данные</button>
                    </div>
                </div>
                <div class="card card-body">
                    <highcharts 
                        :options="charts[chartIndex]"
                        :updateArgs="[true, true, true]"
                        :ref="'chart-' + chartIndex"
                    ></highcharts>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {Chart} from 'highcharts-vue'
import moment from 'moment-timezone';

    export default {
        props: [
            'dataProp',
            'productsProp',
            'chartsProp',
            'routeProp'
        ],
        components: {
            highcharts: Chart 
        },
        data() {
            return {
                route: this.routeProp,
                charts: this.chartsProp,
                charts: [],
                products: this.productsProp,
                data: {
                    from: moment(this.dataProp.from).tz('Asia/Almaty').format(),
                    to: moment(this.dataProp.to).tz('Asia/Almaty').format(),
                    productId: this.dataProp.productId,
                },
                spinnerData: {
                    loading: false,
                    color: '#6cb2eb',
                    size: '100px',
                },
            }
        },
        mounted() {
            this.chartsProp.forEach(function(chart, index) {
                this.charts[index] = chart;
                this.charts[index].xAxis.labels = {
                    formatter: function(value) {
                        return moment(value.value).tz('Asia/Almaty').lang("ru").format('LL');
                    }
                };
            }.bind(this));
        },
        methods: {
            saveCustomData(item) {
                this.spinnerData.loading = true;
                let data = {
                    item: item,
                    productId: this.data.productId
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