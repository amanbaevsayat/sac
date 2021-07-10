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
                                    <label for="started_at" class="col-form-label">Выберите команду</label>
                                    <select v-model="data.teamId" name="teamId" class="form-control">
                                        <option v-for="(team, optionIndex) in teams" :key="optionIndex" :value="team.id">{{ team.name }}</option>
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

        <div class="row">
            <div class="col-sm-8">
                <div class="card">
                    <highcharts 
                        :options="chart"
                        :updateArgs="[true, true, true]"
                        :ref="'chart'"
                    ></highcharts>
                </div>
                <div class="card">
                    <div style="margin-left: 20px; margin-top: 20px; margin-bottom: 30px; margin-right: 0px;" v-if="dataProp.period == 'week'">
                        <h1 style="text-align: center">{{ getTitle() }}</h1>
                        <div v-for="(usersProducts, productIndex) in usersBonuses[data.currentPoint]" :key="productIndex">
                            <h3 style="margin-bottom: 20px">{{ products[productIndex] }}</h3>
                            <div v-for="(usersBonus, paymentType) in usersProducts" :key="paymentType">
                                <div v-for="(bonus, bonusIndex) in usersBonus" :key="bonusIndex">
                                    <div  v-if="bonus.amount > 0">
                                        <h4>{{ bonusesHeaders[paymentType] }}</h4>
                                        <hr>
                                        <div style="margin: 40px 0">
                                            <div class="row" style="margin-bottom: 15px; font-size: 15px;">
                                                <div class="col-sm-10">
                                                    <div class="progress" style="height: 1.6rem;">
                                                        <div role="progressbar" aria-valuemin="0" aria-valuemax="100" class="progress-bar record-polzunok bg-secondary" :style="{width: (records[paymentType] / getMaxValue(productIndex, bonusIndex, paymentType) * 100) + '%'}">
                                                            <div class="record-value progress-value"><span class="record-span">{{ records[paymentType] }}</span></div>
                                                        </div>
                                                        <div role="progressbar" aria-valuemin="0" aria-valuemax="100" class="progress-bar last-polzunok bg-warning" :style="{width: (getLastWeekAmount(productIndex, bonusIndex, paymentType) / getMaxValue(productIndex, bonusIndex, paymentType) * 100) + '%'}">
                                                            <div class="last-value progress-value"><span class="last-span" :style="{left: (getLastWeekAmount(productIndex, bonusIndex, paymentType) / getMaxValue(productIndex, bonusIndex, paymentType) * 100) < 10 ? '4px' : ''}">{{ getLastWeekAmount(productIndex, bonusIndex, paymentType) }}</span></div>
                                                        </div>
                                                        <div role="progressbar" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-success progress-bar-striped progress-bar-animated" :style="{width: (getCurrentWeekAmount(productIndex, bonusIndex, paymentType) / getMaxValue(productIndex, bonusIndex, paymentType) * 100) + '%'}">
                                                            <div style="position: relative; font-size: 14px; font-weight: bold;">{{ getCurrentWeekAmount(productIndex, bonusIndex, paymentType) }} шт.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <span style="font-weight: bold"> x {{ getCurrentWeekBonusesAmount(productIndex, bonusIndex, paymentType) }}₸ = {{ getCurrentWeekTotalBonus(productIndex, bonusIndex, paymentType) }}₸</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <hr>
                            <h3>Сумма бонусов команды за неделю: <span style="float: right;margin-right: 75px">{{ getTotalSum() }}₸</span></h3>
                        </div>
                    </div>
                </div>
                <div class="card" style="padding: 10px;">
                    <div class="row">
                        <div class="col-sm-4">
                            <svg style="color: #38c172" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-square-fill" viewBox="0 0 16 16">
                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2z"></path>
                            </svg>
                            <span style="font-size: 14px;">  - продано абонементов на этой неделе</span>
                        </div>
                        <div class="col-sm-5">
                            <svg style="color: rgb(251 211 98)" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-square-fill" viewBox="0 0 16 16">
                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2z"></path>
                            </svg>
                            <span style="font-size: 14px;">  - продано абонементов на прошлой неделе</span>
                        </div>
                        <div class="col-sm-3">
                            <svg style="color: rgb(208 208 208)" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-square-fill" viewBox="0 0 16 16">
                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2z"></path>
                            </svg>
                            <span style="font-size: 14px;">  - рекорд среди всех недель</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card" style="padding: 20px 15px;">
                    <h2>Распределение бонусов</h2>
                    <hr>
                    <p style="font-size: 15px" v-for="(stake, stakeIndex) in getStakesOfUser()" :key="stakeIndex">{{ stake.name }} ({{ stake.stake }}%) <span style="float: right;">{{ stake.total_bonus }}₸</span></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import {Chart} from 'highcharts-vue'

export default {
    props: [
        'routeProp',
        'dataProp',
        'teamsProp',
        'productsProp',
        'periodsProp',
        'chartProp',
        'usersBonusesProp',
        'bonusesHeadersProp',
        'totalSumProp',
        'recordsProp',
        'usersBonusesGroupUnixDateProp'
    ],
    components: {
        highcharts: Chart,
    },
    data() {
        return {
            usersBonusesGroupUnixDate: this.usersBonusesGroupUnixDateProp,
            records: this.recordsProp,
            total: 0,
            bonusesHeaders: this.bonusesHeadersProp,
            usersBonuses: this.usersBonusesProp,
            route: this.routeProp,
            chart: this.chartProp,
            filterOpen: false,
            teams: this.teamsProp,
            products: this.productsProp,
            periods: this.periodsProp,
            data: {
                from: moment(this.dataProp.from).tz('Asia/Almaty').format(),
                to: moment(this.dataProp.to).tz('Asia/Almaty').format(),
                teamId: this.dataProp.teamId,
                period: this.dataProp.period,
                currentPoint: this.dataProp.currentPoint,
                lastPoint: this.dataProp.lastPoint,
                userId: this.dataProp.userId,
            },
        }
    },
    beforeMount() {
        this.chart.xAxis.labels = {
            formatter: function(value) {
                return moment(value.value).tz('Asia/Almaty').locale("ru").format('D MMM');
            }
        };

        this.chart.plotOptions.series = {
            cursor: 'pointer',
            point: {
                events: {
                    click: (e) => {
                        this.pointClick(e);
                    }
                }
            }
        };
    },
    methods: {
        getCurrentValue(productIndex, bonusIndex, paymentType) {
            return this.getCurrentWeekAmount(productIndex, bonusIndex, paymentType) / this.getMaxValue(productIndex, bonusIndex, paymentType) * 100;
        },
        getLastValue(productIndex, bonusIndex, paymentType) {
            if (this.getCurrentWeekAmount(productIndex, bonusIndex, paymentType) < this.getLastWeekAmount(productIndex, bonusIndex, paymentType)) {
                return (this.getLastWeekAmount(productIndex, bonusIndex, paymentType) / this.getMaxValue(productIndex, bonusIndex, paymentType) * 100) - this.getCurrentValue(productIndex, bonusIndex, paymentType);
            } else {
                return 0;
            }
        },
        getRecordValue(bonusIndex, paymentType) {
            
        },
        getMaxValue(productIndex, bonusIndex, paymentType) {
            return Math.max.apply(null, [
                this.getCurrentWeekAmount(productIndex, bonusIndex, paymentType),
                this.records[paymentType],
                this.getLastWeekAmount(productIndex, bonusIndex, paymentType)
            ]);
        },
        getStakesOfUser() {
            if (this.usersBonusesGroupUnixDate[this.data.currentPoint]) {
                return this.usersBonusesGroupUnixDate[this.data.currentPoint];
            }
            return [];
        },
        getTitle() {
            let end = moment.unix(this.data.currentPoint / 1000).locale("ru").format('LL');
            let start = moment.unix(this.data.currentPoint / 1000).weekday(-6).locale("ru").format('D');
            return  start +' - ' + end;
        },
        getTotalSum() {
            if (this.totalSumProp[this.data.currentPoint]) {
                return this.totalSumProp[this.data.currentPoint];
            } else {
                return 0;
            }
        },
        pointClick(e) {
            this.total = 0;
            this.data.currentPoint = e.point.category;
            this.data.lastPoint = e.point.category - 604800000;
        },
        isNotEmpty(paymentType) {
            return !!this.usersBonuses[this.data.currentPoint][paymentType];
        },
        convertDate(date, format) {
            return moment(date).tz('Asia/Almaty').lang("ru").format(format);
        },
        getLastWeekAmount(productIndex, bonusIndex, paymentType) {
            if (this.usersBonuses[this.data.lastPoint] && this.usersBonuses[this.data.lastPoint][productIndex]) {
                if (this.usersBonuses[this.data.lastPoint][productIndex][paymentType]) {
                    return this.usersBonuses[this.data.lastPoint][productIndex][paymentType][0].amount;
                } else { 
                    return 0;
                }
            } else {
                return 0;
            }
        },
        getLastWeekBonusesAmount(productIndex, bonusIndex, paymentType) {
            if (this.usersBonuses[this.data.lastPoint] && this.usersBonuses[this.data.lastPoint][productIndex]) {
                if (this.usersBonuses[this.data.lastPoint][productIndex][paymentType]) {
                    return this.usersBonuses[this.data.lastPoint][productIndex][paymentType][0].product_bonuses_amount;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        },
        getLastWeekTotalBonus(productIndex, bonusIndex, paymentType) {
            if (this.usersBonuses[this.data.lastPoint] && this.usersBonuses[this.data.lastPoint][productIndex]) {
                if (this.usersBonuses[this.data.lastPoint][productIndex][paymentType]) {
                    return this.usersBonuses[this.data.lastPoint][productIndex][paymentType][0].total_bonus;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        },
        getCurrentWeekAmount(productIndex, bonusIndex, paymentType) {
            if (this.usersBonuses[this.data.currentPoint] && this.usersBonuses[this.data.currentPoint][productIndex]) {
                if (this.usersBonuses[this.data.currentPoint][productIndex][paymentType][bonusIndex]) {
                    return this.usersBonuses[this.data.currentPoint][productIndex][paymentType][bonusIndex].amount;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        },
        getCurrentWeekBonusesAmount(productIndex, bonusIndex, paymentType) {
            if (this.usersBonuses[this.data.currentPoint] && this.usersBonuses[this.data.currentPoint][productIndex]) {
                return this.usersBonuses[this.data.currentPoint][productIndex][paymentType][bonusIndex].product_bonuses_amount;
            } else {
                return 0;
            }
        },
        getCurrentWeekTotalBonus(productIndex, bonusIndex, paymentType) {
            if (this.usersBonuses[this.data.currentPoint] && this.usersBonuses[this.data.currentPoint][productIndex]) {
                return this.usersBonuses[this.data.currentPoint][productIndex][paymentType][bonusIndex].total_bonus;
            } else {
                return 0;
            }
        },
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
.last-polzunok {
    background-color: rgb(251, 211, 98) !important;
    text-align: right;
}
.record-polzunok {
    background-color: #d0d0d0 !important;
    text-align: right;
}
.progress-bar {
    position: absolute;
    height: 100%;
    overflow: unset;
}
.progress {
    position: relative;
    overflow: unset;
}
.progress-value {
    position: relative;
    font-size: 14px;
    font-weight: bold;
    display: block;
    float: right;
    text-align: right;
    width: 100%;
    height: 100%;
}
.record-value::after {
    content: "";
    background: #d0d0d0;
    position: absolute;
    top: 0;
    width: 3px;
    right: 0px;
    height: 55px;
}
.record-span {
    position: absolute;
    right: 7px;
    top: 50px;
    color: #7b7b7b;
}

.last-value::after {
    content: "";
    background: #fbd362;
    position: absolute;
    width: 3px;
    bottom: 0;
    right: 0px;
    height: 55px;
}
.last-span {
    position: absolute;
    right: 7px;
    top: -23px;
    color: rgb(247 183 3);
}
</style>