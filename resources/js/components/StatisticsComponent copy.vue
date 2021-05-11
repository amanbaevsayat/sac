<template>
    <div class="container-fluid">
        <pulse-loader class="spinner" :loading="spinnerData.loading" :color="spinnerData.color" :size="spinnerData.size"></pulse-loader>
        <div class="row">
            <div class="col">
                <div class="card card-body">
                    <div class="form-group">
                        <label for="started_at" class="col-form-label">С</label>
                        <datetime
                            name="from"
                            type="date"
                            v-model="statistics.dates.from"
                            input-class="form-control"
                            valueZone="Asia/Almaty"
                            value-zone="Asia/Almaty"
                            zone="Asia/Almaty"
                            format="dd LLLL"
                            :auto="true"
                        ></datetime>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-body">
                    <div class="form-group">
                        <label for="started_at" class="col-form-label">По</label>
                        <datetime
                            name="to"
                            type="date"
                            v-model="statistics.dates.to"
                            input-class="form-control"
                            valueZone="Asia/Almaty"
                            value-zone="Asia/Almaty"
                            zone="Asia/Almaty"
                            format="dd LLLL"
                            :auto="true"
                        ></datetime>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 id="card-styles"><div>Пробные<a class="anchorjs-link " href="#card-styles" aria-label="Anchor" data-anchorjs-icon="#" style="padding-left: 0.375em;"></a></div></h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col" class="bd-warning">Кол-во новых лидов</th>
                            <th scope="col" class="bd-warning">Кол-во пробных</th>
                            <th scope="col" class="bd-baza">Кол-во новых подключений (за период)</th>
                            <th scope="col" class="bd-formula">Конверсия перехода с лидфида на ватсап</th>
                            <th scope="col" class="bd-baza">Кол-во должников (за период)</th>
                            <th scope="col" class="bd-baza">Кол-во оплат за (за период)</th>
                            <th scope="col" class="bd-formula">Конверсия перехода пробных в клиентов</th>
                            <th scope="col" class="bd-baza">Кол-во отказавшихся (за период)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control" v-model="statistics.trials.leads">
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="statistics.trials.trial">
                            </td>
                            <td>{{ statistics.trials.new }}</td>
                            <td>{{ statistics.trials.leadfeed_to_whatsap }}</td>
                            <td>{{ statistics.trials.deptors }}</td>
                            <td>{{ statistics.trials.from_beginners }}</td>
                            <td>{{ statistics.trials.trial_to_customers }}</td>
                            <td>{{ statistics.trials.refused_during_trial_period }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h2 id="card-styles"><div>Клиенты<a class="anchorjs-link " href="#card-styles" aria-label="Anchor" data-anchorjs-icon="#" style="padding-left: 0.375em;"></a></div></h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col" class="bd-warning">Кол-во клиентов</th>
                            <th scope="col" class="bd-baza">Кол-во должников (за весь период)</th>
                            <th scope="col" class="bd-baza">Кол-во покупок второго абонемента (за период)</th>
                            <th scope="col" class="bd-baza">Кол-во держателей 1 абонемента (за период)</th>
                            <th scope="col" class="bd-formula">Конверсия продления аб-тов после 1 месяца</th>
                            <th scope="col" class="bd-formula">Кол-во оплат от стареньких</th>
                            <th scope="col" class="bd-baza">Кол-во отказавшихся клиентов (за весь период)</th>
                            <th scope="col" class="bd-formula">Приток новых клиентов</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control" v-model="statistics.customers.clients">
                            </td>
                            <td>{{ statistics.customers.debtors }}</td>
                            <td>{{ statistics.customers.second_subscription }}</td>
                            <td>{{ statistics.customers.first_subscription }}</td>
                            <td>{{ statistics.customers.conversion_after_month }}</td>
                            <td>{{ statistics.customers.old }}</td>
                            <td>{{ statistics.customers.refused }}</td>
                            <td>{{ statistics.customers.new }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 id="card-styles"><div>Финансовые показатели<a class="anchorjs-link " href="#card-styles" aria-label="Anchor" data-anchorjs-icon="#" style="padding-left: 0.375em;"></a></div></h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col" class="bd-formula">Оборот за неделю</th>
                            <th scope="col" class="bd-warning">Расходы на рекламу</th>
                            <th scope="col" class="bd-warning">Расходы на бонусы</th>
                            <th scope="col" class="bd-warning">Расходы на съемки</th>
                            <th scope="col" class="bd-warning">Расходы на оплату тренеру</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {{ statistics.financial.turnover }}
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="statistics.financial.advertising_costs">
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="statistics.financial.bonus_costs">
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="statistics.financial.shooting_costs">
                            </td>
                            <td>
                                <input type="text" class="form-control" v-model="statistics.financial.coach_costs">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h2 id="card-styles"><div>Оплаты<a class="anchorjs-link " href="#card-styles" aria-label="Anchor" data-anchorjs-icon="#" style="padding-left: 0.375em;"></a></div></h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col" class="bd-warning">Всего оплат за период</th>
                            <th scope="col" class="bd-formula">Кол-во оплат от стареньких</th>
                            <th scope="col" class="bd-formula">Кол-во оплат от новеньких</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control" v-model="statistics.payments.total_payments">
                            </td>
                            <td>{{ statistics.payments.old }}</td>
                            <td>{{ statistics.payments.new }}</td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" class="btn btn-success btn-sm" @click="submit()" style="margin-top: 20px">Рассчитать</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                spinnerData: {
                    loading: false,
                    color: '#6cb2eb',
                    size: '100px',
                },
                statistics: {
                    dates: {
                        from: null,
                        to: null,

                    },
                    trials: {
                        leads: null,
                        trial: null,
                        new: null,
                        leadfeed_to_whatsap: null,
                        deptors: null,
                        from_beginners: null,
                        trial_to_customers: null,
                        refused_during_trial_period: null,
                    },
                    customers: {
                        clients: null,
                        debtors: null,
                        second_subscription: null,
                        first_subscription: null,
                        conversion_after_month: null,
                        old: null,
                        refused: null,
                        new: null,
                    },
                    financial: {
                        turnover: null, // disabled
                        advertising_costs: null,
                        bonus_costs: null,
                        shooting_costs: null,
                        coach_costs: null,
                    },
                    payments: {
                        total_payments: null,
                        old: null, // disabled
                        new: null, // disabled

                    },
                },
            }
        },
        methods: {
            submit() {
                this.spinnerData.loading = true;
                axios.post(`/statistics`, this.statistics)
                .then(response => {
                    let responseData = response.data;
                    console.log(response);
                    this.spinnerData.loading = false;
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