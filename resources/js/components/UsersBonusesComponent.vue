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
                            <div class="col">
                                <div class="form-group">
                                    <label for="started_at" class="col-form-label">Выберите оператора</label>
                                    <select v-model="dataProp.userId" name="userId" class="form-control">
                                        <option v-for="(option, optionIndex) in users" :key="optionIndex" :value="optionIndex">{{ option }}</option>
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
                    <div style="margin-left: 20px" v-if="dataProp.period == 'week'">
                        <h1 style="text-align: center">{{ getTitle() }}</h1>
                        <div v-for="(usersBonus, paymentType) in usersBonuses[data.currentPoint]" :key="paymentType">
                            <div v-for="(bonus, bonusIndex) in usersBonus" :key="bonusIndex">
                                <div v-if="bonus.user_id == dataProp.userId">
                                    <h2>{{ bonusesHeaders[paymentType] }}</h2>
                                    <hr>
                                    <div style="margin-bottom: 30px;">
                                        <div class="row" style="margin-bottom: 15px; font-size: 15px;">
                                            <div class="col-sm-2">
                                                <span>Текущая неделя</span>
                                            </div>
                                            <div class="col-sm-6">
                                                <b-progress :max="100" height="1.2rem">
                                                    <b-progress-bar :value="100">
                                                        <span style="font-size: 14px;">{{ getCurrentWeekAmount(bonusIndex, paymentType) }} платежей</span>
                                                    </b-progress-bar>
                                                </b-progress>
                                            </div>
                                            <div class="col-sm-4">
                                                <span> * {{ getCurrentWeekBonusesAmount(bonusIndex, paymentType) }}₸ = {{ getCurrentWeekTotalBonus(bonusIndex, paymentType) }}₸</span>
                                            </div>
                                        </div>
                                        <div v-if="isNotEmpty(paymentType)" class="row" style="margin-bottom: 15px; font-size: 15px;">
                                            <div class="col-sm-2">
                                                <span>Прошлая неделя</span>
                                            </div>
                                            <div class="col-sm-6">
                                                <b-progress :max="100" height="1.2rem">
                                                    <b-progress-bar variant="warning" :value="getLastWeekAmount(paymentType) / bonus.amount * 100">
                                                        <span style="font-size: 14px;">{{ getLastWeekAmount(paymentType) }} платежей</span>
                                                    </b-progress-bar>
                                                </b-progress>
                                            </div>
                                            <div class="col-sm-4">
                                                <span> * {{ getLastWeekBonusesAmount(paymentType) }}₸ = {{ getLastWeekTotalBonus(paymentType) }}₸</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <hr>
                            <h2>Сумма бонусов за неделю: {{ getTotalSum() }}₸</h2>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-sm-4">
                <div class="card" style="padding: 20px 15px;">
                    <h2>Распределение бонусов</h2>
                    <hr>
                    <p style="font-size: 15px" v-for="(stake, stakeIndex) in getStakesOfUser()" :key="stakeIndex">{{ stake.name }} ({{ stake.percent }}%) <span style="float: right;">{{ stake.share }}₸</span></p>
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
        'productsProp',
        'periodsProp',
        'chartProp',
        'usersBonusesProp',
        'bonusesHeadersProp',
        'totalSumProp',
        'usersProp',
        'userIdProp',
    ],
    components: {
        highcharts: Chart,
    },
    data() {
        return {
            total: 0,
            userId: this.userIdProp,
            users: this.usersProp,
            bonusesHeaders: this.bonusesHeadersProp,
            usersBonuses: this.usersBonusesProp,
            route: this.routeProp,
            chart: this.chartProp,
            filterOpen: false,
            products: this.productsProp,
            periods: this.periodsProp,
            data: {
                from: moment(this.dataProp.from).tz('Asia/Almaty').format(),
                to: moment(this.dataProp.to).tz('Asia/Almaty').format(),
                productId: this.dataProp.productId,
                period: this.dataProp.period,
                currentPoint: this.dataProp.currentPoint,
                lastPoint: this.dataProp.lastPoint,
                userId: this.userId,
            },
        }
    },
    beforeMount() {
        this.chart.xAxis.labels = {
            formatter: function(value) {
                return moment(value.value).tz('Asia/Almaty').locale("ru").format('LL');
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
        getStakesOfUser() {
            if (this.usersBonuses[this.data.currentPoint]) {
                let users = [];
                let response = [];
                Object.keys(this.usersBonuses[this.data.currentPoint]).forEach(function(bonusType) {
                    let bonuses = this.usersBonuses[this.data.currentPoint][bonusType]
                    bonuses.forEach((bonus) => {
                        if (! users[bonus.user_id]) {
                            users[bonus.user_id] = [];
                        }
                        if (! users[bonus.user_id][bonus.stake]) {
                            users[bonus.user_id][bonus.stake] = 0;
                        }
                        users[bonus.user_id][bonus.stake] = users[bonus.user_id][bonus.stake] + bonus.total_bonus;
                    });
                }.bind(this));
                users.forEach((bonuses, userId) => {
                    bonuses.forEach((total, stake) => {
                        if (this.dataProp.userId == userId) {
                            this.total = total;
                        }
                        response.push({
                            name: this.users[userId],
                            stake: stake,
                            share: total * stake / 100,
                            percent: stake,
                        });
                    });
                });


                return response;
            } else {
                return [];
            }
        },
        getTitle() {
            let end = moment.unix(this.data.currentPoint / 1000).locale("ru").format('LL');
            let start = moment.unix(this.data.currentPoint / 1000).weekday(-7).locale("ru").format('D');
            return  start +' - ' + end;
        },
        getTotalSum() {
            return this.total;
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
        getLastWeekAmount(paymentType) {
            if (this.usersBonuses[this.data.lastPoint]) {
                return this.usersBonuses[this.data.lastPoint][paymentType][0].amount;
            } else {
                return 0;
            }
        },
        getLastWeekBonusesAmount(paymentType) {
            if (this.usersBonuses[this.data.lastPoint]) {
                return this.usersBonuses[this.data.lastPoint][paymentType][0].bonuses_amount;
            } else {
                return 0;
            }
        },
        getLastWeekTotalBonus(paymentType) {
            if (this.usersBonuses[this.data.lastPoint]) {
                return this.usersBonuses[this.data.lastPoint][paymentType][0].total_bonus;
            } else {
                return 0;
            }
        },
        getCurrentWeekAmount(bonusIndex, paymentType) {
            if (this.usersBonuses[this.data.currentPoint]) {
                return this.usersBonuses[this.data.currentPoint][paymentType][bonusIndex].amount;
            } else {
                return 0;
            }
        },
        getCurrentWeekBonusesAmount(bonusIndex, paymentType) {
            if (this.usersBonuses[this.data.currentPoint]) {
                return this.usersBonuses[this.data.currentPoint][paymentType][bonusIndex].bonuses_amount;
            } else {
                return 0;
            }
        },
        getCurrentWeekTotalBonus(bonusIndex, paymentType) {
            if (this.usersBonuses[this.data.currentPoint]) {
                return this.usersBonuses[this.data.currentPoint][paymentType][bonusIndex].total_bonus;
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
</style>