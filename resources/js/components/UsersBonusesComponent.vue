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

        <div class="row">
            <div class="col-sm-8">
                <div class="card">
                    <highcharts 
                        :options="chart"
                        :updateArgs="[true, true, true]"
                        :ref="'chart'"
                    ></highcharts>
                    <div v-for="(usersBonus, paymentType) in usersBonuses" :key="paymentType">
                        <div v-for="(bonus, bonusIndex) in usersBonus" :key="bonusIndex">
                            <h2>{{ bonusesHeaders[paymentType] }}</h2>

                            <p>Текущая неделя - {{ bonus.amount }} платежей * {{ bonus.bonuses_amount }}₸ = {{ bonus.total_bonus }}₸</p>
                            <p>Прошлая неделя - {{ bonus.amount }} платежей * {{ bonus.bonuses_amount }}₸ = {{ bonus.total_bonus }}₸</p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-sm-4">
                <div class="card">
                    awd
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>
import moment from 'moment-timezone';
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
    ],
    components: {
        highcharts: Chart,
    },
    data() {
        return {
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
            },
        }
    },
    methods: {
        convertDate(date, format) {
            return moment(date).tz('Asia/Almaty').lang("ru").format(format);
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