<template>
    <div>
        <div class="modal bd-example-modal-lg" :id="'modal-customer-' + type">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Карточка клиента</h5>
                        <button type="button" data-dismiss="modal" class="close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <pulse-loader style="z-index: 999; background: rgba(0, 0, 0, 0.19); height: 100%; line-height: 100vh;" class="spinner" :loading="spinnerData.loading" :color="spinnerData.color" :size="spinnerData.size"></pulse-loader>
                    <div class="card" style="padding-bottom: 0px">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name" class="col-sm-4 col-form-label">Имя</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="name" v-model="customer.name" name="customer.name" required>
                                    </div>
                                    <label for="phone" class="col-sm-4 col-form-label">Телефон</label>
                                    <div class="col-sm-8">
                                        <the-mask
                                            :masked="false"
                                            mask="+# (###) ### ##-##"
                                            type="text"
                                            class="form-control"
                                            id="phone"
                                            v-model="customer.phone"
                                            name="customer.phone"
                                            required
                                        ></the-mask>
                                    </div>
                                    <label for="email" class="col-sm-4 col-form-label">E-mail</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="email" v-model="customer.email" name="customer.email">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="comments" class="col-sm-4 col-form-label">Примечания</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" v-model="customer.comments" name="customer.comments" id="" cols="30" rows="10"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bd-example">
                        <b-card no-body>
                            <b-tabs card>
                                <!-- Render Tabs, supply a unique `key` to each tab -->
                                <b-tab v-for="(subscription, subIndex) in subscriptions" :key="subIndex" :active="showTab(subIndex)" :title="getSubscriptionTitle(subscription.product_id)" style="position: relative;">
                                    <div v-if="isDisabled(subscription)" style="width: 100%; height: 100%; display: block; background: #0000001a; z-index: 10000; top: 0; position: absolute; left: 0;"></div>
                                    <div class="row">
                                        <button 
                                            type="button" 
                                            title="Удалить услугу"
                                            @click="removeProduct(subscription.id, subIndex)" 
                                            class="close" 
                                            data-dismiss="alert" 
                                            aria-label="Close"
                                            style="right: 20px; position: absolute; z-index: 1"
                                        ><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <label for="product_id" class="col-form-label">Услуга</label>
                                            <select v-if="! subscription.id" v-model="subscription.product_id" :name="'subscriptions.' + subIndex + '.product_id'" id="product_id" class="col-sm-10 form-control">
                                                <option v-for="(option, optionIndex) in products" :key="optionIndex" :value="optionIndex">{{ option.title }}</option>
                                            </select>
                                            <input v-else :value="getSubscriptionTitle(subscription.product_id)" id="product_id" class="col-sm-10 form-control" type="text" disabled>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="price" class="col-form-label">Цена</label>
                                            <select v-model="subscription.price" :name="'subscriptions.' + subIndex + '.price'" id="price" class="col-sm-10 form-control" :disabled="isDisabled(subscription)">
                                                <option v-if="subscription.price != null" :value="subscription.price" selected>{{ subscription.price }}</option>
                                                <option v-for="(option, optionIndex) in getPrices(subscription.product_id)" :key="optionIndex" :value="option" v-if="option != subscription.price">{{ option }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <label for="payment_type" class="col-form-label">Тип оплаты</label>
                                            <select v-model="subscription.payment_type" :name="'subscriptions.' + subIndex + '.payment_type'" id="payment_type" class="col-sm-10 form-control" :disabled="isDisabled(subscription)">
                                                <option v-for="(option, optionIndex) in getPaymentTypes(subscription.product_id)" :key="optionIndex" :value="optionIndex">{{ option.title }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="status" class="col-form-label">Статус абонемента</label>
                                            <select v-model="subscription.status" :name="'subscriptions.' + subIndex + '.status'" id="status" class="col-sm-10 form-control" :disabled="isDisabled(subscription)">
                                                <option v-for="(option, optionIndex) in getStatuses(subscription.product_id, subscription.payment_type)" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-6" v-if="subscription.payment_type != 'simple_payment'">
                                            <label for="started_at" class="col-form-label">Дата старта</label>
                                            <datetime
                                                :name="'subscriptions.' + subIndex + '.started_at'"
                                                type="date"
                                                v-model="subscription.started_at"
                                                input-class="col-sm-10 my-class form-control"
                                                valueZone="Asia/Almaty"
                                                value-zone="Asia/Almaty"
                                                zone="Asia/Almaty"
                                                format="dd LLLL"
                                                :auto="true"
                                                :disabled="isDisabled(subscription)"
                                            ></datetime>
                                        </div>
                                        <div class="form-group col-sm-6" v-if="subscription.payment_type != 'simple_payment'">
                                            <label for="tries_at" class="col-form-label">Дата окончания абонемента и следующего платежа</label>
                                            <div v-if="subscription.payment_type == 'tries'">
                                                <div v-show="!subscription.is_edit_ended_at">
                                                    <span class="ended_at-span">{{ showDate(subscription.tries_at) }}</span>
                                                    <button class="btn btn-info" @click="subscription.is_edit_ended_at = !subscription.is_edit_ended_at" :disabled="isDisabled(subscription)">Изменить</button>
                                                </div>
                                                <datetime
                                                    v-show="subscription.is_edit_ended_at"
                                                    :name="'subscriptions.' + subIndex + '.tries_at'"
                                                    type="date"
                                                    v-model="subscription.tries_at"
                                                    input-class="col-sm-10 my-class form-control"
                                                    valueZone="Asia/Almaty"
                                                    value-zone="Asia/Almaty"
                                                    zone="Asia/Almaty"
                                                    format="dd LLLL"
                                                    :auto="true"
                                                    :disabled="isDisabled(subscription)"
                                                ></datetime>
                                            </div>
                                            <div v-else-if="subscription.payment_type == 'transfer' || subscription.payment_type == 'cloudpayments'">
                                                <div v-show="!subscription.is_edit_ended_at">
                                                    <span class="ended_at-span">{{ showDate(subscription.ended_at) }}</span>
                                                    <button class="btn btn-info" @click="subscription.is_edit_ended_at = !subscription.is_edit_ended_at" :disabled="isDisabled(subscription)">Изменить</button>
                                                </div>
                                                <datetime
                                                    v-show="subscription.is_edit_ended_at"
                                                    :name="'subscriptions.' + subIndex + '.ended_at'"
                                                    type="date"
                                                    v-model="subscription.ended_at"
                                                    input-class="col-sm-10 my-class form-control"
                                                    valueZone="Asia/Almaty"
                                                    value-zone="Asia/Almaty"
                                                    zone="Asia/Almaty"
                                                    format="dd LLLL"
                                                    :auto="true"
                                                    :disabled="isDisabled(subscription)"
                                                ></datetime>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom: 15px" v-if="subscription.payment_type == 'cloudpayments' && type == 'edit' && subscription.cp_subscription_id != null">
                                        <div class="form-group col-sm-6">
                                            <button type="button" class="btn btn-dark" :id="'subscription-' + subscription.id" @click="manualWriteOffPayment(subscription.id)">Ручное списание</button>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom: 15px">
                                        <div v-if="subscription.status == 'refused'" class="form-group col-sm-6">
                                            <label for="reason_id" class="col-form-label">Причина отказа</label>
                                            <select v-model="subscription.reason_id" :name="'subscriptions.' + subIndex + '.reason_id'" id="reason_id" class="col-sm-10 form-control" :disabled="isDisabled(subscription)">
                                                <option v-for="(reason, reasonIndex) in subscription.reasons" :key="reasonIndex" :value="reason.id">{{ reason.title }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom: 15px" v-if="subscription.payment_type == 'transfer'">
                                        <div class="col-sm-6">
                                            <button data-toggle="modal" class="btn btn-primary" @click="showTransferModal(subscription.id)" :disabled="isDisabled(subscription)">Загрузить чек</button>
                                        </div>
                                        <div :id="'modalTransfer-' + subscription.id" class="modal">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Загрузить чек</h5>
                                                        <button type="button" class="close" @click="hideTransferModal(subscription.id)">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="form-group col-sm-6">
                                                                <label for="check" class="col-form-label">Оплачен от</label>
                                                                <datetime
                                                                    :name="'subscriptions.' + subIndex + '.newPayment.from'"
                                                                    type="date"
                                                                    v-model="subscription.newPayment.from"
                                                                    input-class="col-sm-10 my-class form-control"
                                                                    valueZone="Asia/Almaty"
                                                                    value-zone="Asia/Almaty"
                                                                    zone="Asia/Almaty"
                                                                    format="dd LLLL"
                                                                    :auto="true"
                                                                ></datetime>
                                                            </div>
                                                            <div class="form-group col-sm-6">
                                                                <label for="check" class="col-form-label">До</label>
                                                                <datetime
                                                                    :name="'subscriptions.' + subIndex + '.newPayment.from'"
                                                                    type="date"
                                                                    v-model="subscription.newPayment.to"
                                                                    input-class="col-sm-10 my-class form-control"
                                                                    valueZone="Asia/Almaty"
                                                                    value-zone="Asia/Almaty"
                                                                    zone="Asia/Almaty"
                                                                    format="dd LLLL"
                                                                    :auto="true"
                                                                ></datetime>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group col-sm-6">
                                                                <label for="check" class="col-form-label">Загрузить чек</label>
                                                                <upload-file :value-prop="subscription.newPayment.check" @file='setFileToSubscription($event, subIndex)'></upload-file>
                                                            </div>
                                                            <div class="form-group col-sm-6">
                                                                <label for="quantity" class="col-form-label">Период</label>
                                                                <select v-model="subscription.newPayment.quantity" name="quantity" id="quantity" class="col-sm-10 form-control">
                                                                    <option v-for="(option, optionIndex) in quantities" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <footer class="modal-footer">
                                                        <button @click="hideTransferModal(subscription.id, subscription.newPayment.to, subIndex)" type="button" class="btn btn-primary">Сохранить</button>
                                                        <!-- <button @click="submit()" type="button" class="btn btn-primary">Сохранить</button> -->
                                                    </footer>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" v-if="customer.card && (subscription.payment_type == 'simple_payment') && subscription.status != 'paid'" style="margin-bottom: 15px">
                                        <div class="col-sm-12">
                                            <span><span style="font-weight: bold">{{ customer.card.type }}</span> (конец карты - {{ customer.card.last_four }}) </span>
                                            <button type="button" class="btn btn-dark" :id="'writeOffPaymentByToken-' + subscription.id" @click="writeOffPaymentByToken(subscription.id, customer.card.id)" :disabled="isDisabled(subscription)">Списать оплату с привязанной карты</button>
                                        </div>
                                    </div>
                                    <div class="row" v-if="subscription.recurrent && (subscription.payment_type == 'cloudpayments' || subscription.payment_type == 'simple_payment')" style="margin-bottom: 15px">
                                        <div class="col-sm-6">
                                            <div class="recurrent_block">
                                                <a target="_blank" :href="subscription.recurrent.link">{{ subscription.recurrent.link }}</a>
                                                <input type="hidden" :id="'recurrent-link-' + subIndex" :value="subscription.recurrent.link">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="recurrent_button-block">
                                                <button class="btn btn-info" @click="copyRecurrentLink(subIndex)">Копировать</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-show="type == 'edit'" class="row" style="margin-bottom: 15px;">
                                        <div class="col-sm-12">
                                            <a target="_blank" :href="'/userlogs?subscription_id=' + subscription.id">Логи абонемента</a>
                                            <span> | </span>
                                            <a style="color: #3490dc;cursor: pointer" @click="showHistorySubscriptionModal(subscription.id)">История абонемента</a>
                                            <div :id="'modalHistorySubscription-' + subscription.id" class="modal">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">История абонемента</h5>
                                                            <button type="button" class="close" @click="hideHistorySubscriptionModal(subscription.id)">
                                                            <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <vc-calendar
                                                                :min-date='new Date(subscription.started_at)'
                                                                :max-date='new Date(subscription.ended_at)'
                                                                :columns="$screens({ default: 1, lg: 2 })"
                                                                :rows="$screens({ default: 1, lg: 2 })"
                                                                :is-expanded="$screens({ default: true, lg: true })"
                                                                :attributes='subscription.history'
                                                                />

                                                                <div style="width: 100%; margin-top: 20px; text-align: center">
                                                                    <p><span style="color: green">Зеленый</span> - заморозка</p>
                                                                    <p><span style="color: red">Красный</span> - перевод</p>
                                                                    <p><span style="color: yellow">Желтый</span> - подписка</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <footer class="modal-footer">
                                                            <button @click="hideHistorySubscriptionModal(subscription.id)" type="button" class="btn btn-dark">Выйти</button>
                                                            <!-- <button @click="submit()" type="button" class="btn btn-primary">Сохранить</button> -->
                                                        </footer>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <ul class="list-group">
                                                <li class="list-group-item" v-for="(payment, paymentIndex) in subscription.payments" :key="paymentIndex">
                                                    <a :href="payment.url" target="_blank">ID: {{ payment.id }}</a>
                                                    <span> | </span>
                                                    {{ payment.title }}
                                                    <a v-if="payment.type == 'transfer' && payment.status == 'Completed'" target="_blank" :href="payment.check">(чек оплаты)</a>
                                                    <span> | </span>
                                                    <a :href="payment.user.url" target="_blank">{{ payment.user.name }}</a>
                                                    <span class="list-group-remove" @click="deletePayment(payment, paymentIndex, subIndex)">X</span>
                                                    <a :href="payment.edit" target="_blank" class="list-group-remove" style="
                                                        margin-right: 5px;
                                                        background-color: #abab00;
                                                    ">E</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </b-tab>
                                <template #tabs-end>
                                <b-nav-item style="background: #6cb2eb; border-radius: 5px;" role="presentation" @click.prevent="addProduct()" title="Добавить услугу" href="#"><b style="color: #ffffff;">+</b></b-nav-item>
                                </template>
                            </b-tabs>
                        </b-card>
                    </div>
                    <footer class="modal-footer">
                        <button @click="closeModal()" data-dismiss="modal" type="button" class="btn btn-secondary">Отмена</button>
                        <button @click="submit()" type="button" class="btn btn-primary">Сохранить</button>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
import {TheMask} from 'vue-the-mask'
import {mask} from 'vue-the-mask'
import DatePicker from 'v-calendar/lib/components/date-picker.umd'
import Calendar from 'v-calendar/lib/components/calendar.umd'

export default {
    components: {
        TheMask,
        Calendar,
        DatePicker
    },
    directives: {mask},
    props: [
        'typeProp',
        'nameProp',
        'customerIdProp',
        'subscriptionIdProp',
    ],
    data() {
        return {
            customerId: this.customerIdProp,
            userTeamIds: [],
            userRole: 'operator',
            subscriptionId: this.subscriptionIdProp,
            type: this.typeProp,
            name: this.nameProp,
            spinnerData: {
                loading: false,
                color: '#6cb2eb',
                size: '100px',
            },
            customer: {
                name: '',
                phone: '',
                email: '',
                comments: '',
            },
            products: {},
            subscriptions: [],
            users: [],
            paymentTypes: {},
            statuses: {},
            quantities: {},
            typesColor: {
                frozen: 'green',
                transfer: 'red',
                cloudpayments: 'yellow',
            },
            spinnerData: {
                loading: false,
                color: '#6cb2eb',
                size: '100px',
            },
        }
    },
    watch: {
        customerIdProp: function(newVal, oldVal) { // watch it
            console.log('Prop changed: ', newVal, ' | was: ', oldVal);
            this.customerId = newVal;
            if (this.customerId !== null) {
                this.getCustomerWithData();
            }
        },
        customer: function (newVal, oldVal) {
            if (newVal.phone.length == 10) {
                newVal.phone = '7' . newVal.phone;
            }
            this.customer = newVal;
        }
    },
    mounted() {
        if (this.type == 'create') {
            this.addProduct();
        } else if (this.type == 'edit') {
        }

        this.getOptions();
    },
    methods: {
        isDisabled(subscription) {
            if (this.userRole == 'head' || this.userRole == 'host') {
                return false;
            }

            if (subscription.team_id) {
                return ! this.userTeamIds.includes(subscription.team_id);
            } else {
                return false;
            }
        },
        writeOffPaymentByToken(subId, cardId) {
            document.getElementById('writeOffPaymentByToken-' + subId).disabled = true;
            this.spinnerData.loading = true;
            axios.post('/subscriptions/writeOffPaymentByToken', {
                subscriptionId: subId,
                cardId: cardId
            }).then(response => {
                this.spinnerData.loading = false;
                document.getElementById('writeOffPaymentByToken-' + subId).disabled = false;
                Vue.$toast.success(response.data.message);
            })
            .catch(err => {
                this.spinnerData.loading = false;
                document.getElementById('writeOffPaymentByToken-' + subId).disabled = false;
                Vue.$toast.error(err.response.data.message);
            });
        },
        showTab(subIndex) {
            let k = 0;
            this.subscriptions.forEach((sub, key) => {
                if (sub.id == this.subscriptionIdProp) {
                    k = key;
                }
            });

            if (subIndex == k) {
                return true;
            } else {
                return false;
            }
        },
        manualWriteOffPayment(subscriptionId) {
            document.getElementById('subscription-' + subscriptionId).disabled = true;
            this.spinnerData.loading = true;

            axios.post('/subscriptions/manualWriteOffPayment', {
                subscriptionId: subscriptionId
            }).then(response => {
                this.spinnerData.loading = false;
                document.getElementById('subscription-' + subscriptionId).disabled = false;
                Vue.$toast.success(response.data.message);
            })
            .catch(err => {
                this.spinnerData.loading = false;
                document.getElementById('subscription-' + subscriptionId).disabled = false;
                Vue.$toast.error(err.response.data.message);
            });
        },
        getSubscriptionTitle(productId) {
            if (productId) {
                return this.products[productId].title;
            } else {
                return 'Новый абонемент';
            }
        },
        openTransferModal() {
            $('#modalTransfer').modal('toggle');
        },
        hideCloudpaymentsModal(index) {
            $('#modalCloudpayments-' + index).modal('hide');
        },
        showCloudpaymentsModal(index) {
            $('#modalCloudpayments-' + index).modal('show');
        },
        hideTransferModal(id, toDate, index) {
            this.subscriptions[index].ended_at = toDate;
            $('#modalTransfer-' + id).modal('hide');
        },
        showTransferModal(id) {
            $('#modalTransfer-' + id).modal('show');
        },
        hideHistorySubscriptionModal(id) {
            $('#modalHistorySubscription-' + id).modal('hide');
        },
        showHistorySubscriptionModal(id) {
            $('#modalHistorySubscription-' + id).modal('show');
        },
        deletePayment(payment, paymentIndex, subIndex) {
            if (payment.id) {
                let result = confirm('Удалить платеж?');
                if (result) {
                    axios.delete(`/payments/${payment.id}`).then(response => {
                        let message = response.data.message;
                        if (response.data.message) {
                            Vue.$toast.success(message);
                            this.subscriptions[subIndex].payments.splice(paymentIndex, 1);
                        }
                    })
                    .catch(err => {
                        if (err.response.status === 422) {
                            let errors = err.response.data.errors;
                            if (errors) {
                                Object.keys(errors).forEach(function(name) {
                                    Vue.$toast.error(errors[name][0]);
                                });
                            }
                        }
                        throw err;
                    });
                }
            }
        },
        setDates(startedAt, subIndex) {
            if (this.type == 'create') {
                this.subscriptions[subIndex].ended_at = moment().format();
                this.subscriptions[subIndex].tries_at = moment().add(7, 'days').format();
            }
        },
        showDate(date) {
            return moment(date).locale('ru').format('DD MMM YY');
        },
        closeModal() {
            // this.customer = {
            //     name: '',
            //     phone: '',
            //     email: '',
            //     comments: '',
            // };
            // this.subscriptions = [];
        },
        copyRecurrentLink(index) {
            var input = $('#recurrent-link-' + index);
            var success   = true,
                range     = document.createRange(),
                selection;

            // For IE.
            if (window.clipboardData) {
                window.clipboardData.setData("Text", input.val());        
            } else {
                // Create a temporary element off screen.
                var tmpElem = $('<div>');
                tmpElem.css({
                position: "absolute",
                left:     "-1000px",
                top:      "-1000px",
                });
                // Add the input value to the temp element.
                tmpElem.text(input.val());
                $("body").append(tmpElem);
                // Select temp element.
                range.selectNodeContents(tmpElem.get(0));
                selection = window.getSelection ();
                selection.removeAllRanges ();
                selection.addRange (range);
                // Lets copy.
                try { 
                    success = document.execCommand("copy", false, null);
                }
                catch (e) {
                    copyToClipboardFF(input.val());
                }
                if (success) {
                    Vue.$toast.success('Ссылка скопирована!');

                    // remove temp element.
                    tmpElem.remove();
                }
            }
        },
        getCustomerWithData() {
            this.spinnerData.loading = true;
            axios.get('/customers/' + this.customerId + '/with-data').then(response => {
                let data = response.data.data;
                if (response.data.data) {
                    this.spinnerData.loading = false;
                    if (data.phone.length == 10) {
                        data.phone = '+7' + data.phone;
                    }
                    this.customer = data;
                    this.subscriptions = data.subscriptions;
                    this.userTeamIds = data.userTeamIds;
                    this.userRole = data.userRole;
                    let typesColor = this.typesColor;
                    this.subscriptions.forEach((subscription, subIndex, selfSubscriptions) => {
                        let hstr = [];
                        Object.keys(subscription.history).forEach(function(historyIndex) {
                            let attr = {};
                            attr.highlight = typesColor[historyIndex];
                            attr.dates = [];
                            subscription.history[historyIndex].forEach((payment, paymentIndex, selfPayments) => {
                                attr.dates.push(payment.dates);
                            });
                            hstr.push(attr);
                        });
                        this.subscriptions[subIndex].history = hstr;
                    });
                }
            })
            .catch(err => {
                throw err;
            });
        },
        getOptions() {
            axios.get('/customers/get-options').then(response => {
                this.paymentTypes = response.data.paymentTypes;
                this.statuses = response.data.statuses;
                this.quantities = response.data.quantities;
                this.users = response.data.users;
                this.user = response.data.user;
                this.userTeamIds = response.data.userTeamIds;
                this.userRole = response.data.userRole;
            });
        },
        setFileToSubscription(value, index) {
            this.subscriptions[index].newPayment.check = value;
        },
        removeClassInvalid() {
            var paras = document.getElementsByClassName('is-invalid');

            while(paras.length > 0){
                paras[0].classList.remove('is-invalid');
            }
        },
        submit() {
            this.spinnerData.loading = true;
            let data = {
                customer: {
                    name: this.customer.name,
                    phone: this.customer.phone,
                    email: this.customer.email,
                    comments: this.customer.comments,
                },
                subscriptions: this.subscriptions,
            };
            if (this.type != 'create') {
                data.customer.id = this.customer.id;
            }
            this.removeClassInvalid();
            axios.post('/customers/update-with-data', data).then(response => {
                this.spinnerData.loading = false;
                let customer = response.data.customer;
                if (response.data.customer) {
                    this.customer = {
                        id: customer.id,
                        name: customer.name,
                        phone: customer.phone,
                        email: customer.email,
                        comments: customer.comments,
                        card: customer.card,
                    };

                    this.subscriptions = customer.subscriptions;
                }

                // this.subscriptions.forEach((value, index, self) => {
                //     if (value.payment_type != 'cloudpayments' || value.payment_type != 'simple_payment') {
                //         this.customer = {
                //             name: '',
                //             phone: '',
                //             email: '',
                //             comments: '',
                //         };

                //         this.subscriptions = [];
                //         this.addProduct();
                //         this.customerId = null;
                //         this.customerIdProp = null;
                //         this.subscriptionId = null;
                //         this.subscriptionIdProp = null;
                //         $('#modal-customer-create').modal('hide');
                //         $('#modal-customer-edit').modal('hide');
                //     }
                // });
                if (document.getElementById("file")) {
                    document.getElementById("file").value = "";
                }

                Vue.$toast.success(response.data.message);
            })
            .catch(err => {
                this.spinnerData.loading = false;
                if (err.response.status === 422) {
                    let errors = err.response.data.errors;
                    if (errors) {
                        Object.keys(errors).forEach(function(name) {
                            let element = document.getElementsByName(name)[0];
                            if (element) {
                                element.classList.add('is-invalid');
                            }
                            Vue.$toast.error(errors[name][0]);
                        });
                    }
                }
                throw err;
            });
        },
        removeProduct(id, subIndex) {
            if (this.type == 'create' || !id) {
                if (subIndex > -1) {
                    if (this.subscriptions.length > 1) {
                        this.subscriptions.splice(subIndex, 1);
                    }
                }
            } else if (this.type == 'edit') {
                let result = confirm('Удалить подписку?');
                if (result) {
                    axios.delete(`/subscriptions/${id}`).then(response => {
                        let message = response.data.message;
                        if (response.data.message) {
                            this.subscriptions.splice(subIndex, 1);
                            Vue.$toast.success(message);
                        }
                    })
                    .catch(err => {
                        if (err.response.status === 422) {
                            let errors = err.response.data.errors;
                            if (errors) {
                                Object.keys(errors).forEach(function(name) {
                                    Vue.$toast.error(errors[name][0]);
                                });
                            }
                        }
                        throw err;
                    });
                }
            }
        },
        addProduct() {
            let now = moment();

            this.subscriptions.push({
                id: null,
                product_id: null,
                reason_id: null,
                team_id: null,
                user_id: null,
                price: null,
                payment_type: null,
                started_at: now.format(),
                paused_at: null,
                ended_at: moment().format(),
                frozen_at: null,
                defrozen_at: null,
                tries_at: moment().add(7, 'days').format(),
                status: null,
                description: null,
                newPayment: {
                    from: null,
                    to: null,
                    quantity: null,
                    check: null,
                },
                product: {
                    id: null,
                    title: null,
                    price: null,
                },
            });
        },
        getProductsWithPrices() {
            axios.get(`/products/with-prices`).then(response => {
                this.products = response.data;
            });
        },
        getPrices(productId) {
            if (productId) {
                return this.products[productId]['prices'];
            }
            return [];
        },
        getPaymentTypes(productId) {
            if (productId) {
                return this.paymentTypes[productId];
            }
            return [];
        },
        getStatuses(productId, paymentTypeKey) {
            if (paymentTypeKey && this.paymentTypes[productId] && this.paymentTypes[productId][paymentTypeKey] && this.paymentTypes[productId][paymentTypeKey]!==undefined) {
                return this.paymentTypes[productId][paymentTypeKey]['statuses'];
            }
            return [];
        }
    },
    created() {
        this.getProductsWithPrices();
    },
}
</script>
<style scoped>
.b-tabs .card-header {
    background-color: rgba(0,0,0,.03)!important;
    border-bottom: 1px solid rgba(0,0,0,.125)!important;
}
.ended_at-span {
    padding: 5px 0px;
    padding-right: 10px;
    display: inline-block;
}
.v-spinner {
    width: 100%;
    height: 100%;
    text-align: center;
    position: absolute;
    background: #00000017;
}
.recurrent_block {
    padding: 20px;
    border: 1px solid #3490dc;
}
.recurrent_button-block {
    margin: 15px;
}
.selectpicker {
    display: block!important;
}
.bd-example {
    padding: 1.5rem;
    padding-top: 0px;
    padding-bottom: 0px;
    margin-right: 0;
    margin-left: 0;
}
.list-group-remove {
    color: #ffffff;
    background-color: #3490dc;
    float: right;
    display: inline-block;
    border-radius: 10rem;
    padding: 2px 6px;
    font-size: 9px;
    font-weight: 700;
    cursor: pointer;
}
</style>