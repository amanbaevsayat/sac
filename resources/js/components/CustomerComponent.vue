<template>
    <div>
        <div class="modal fade bd-example-modal-lg" :id="'modal-customer-' + type" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="card">
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
                        <div class="card" v-for="(subscription, subIndex) in subscriptions" :key="subIndex">
                            <div class="card-body">
                                <div class="row">
                                    <button 
                                        type="button" 
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
                                        <select v-model="subscription.product_id" :name="'subscriptions.' + subIndex + '.product_id'" id="product_id" class="col-sm-10 form-control">
                                            <option v-for="(option, optionIndex) in products" :key="optionIndex" :value="optionIndex">{{ option.title }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="price" class="col-form-label">Цена</label>
                                        <select v-model="subscription.price" :name="'subscriptions.' + subIndex + '.price'" id="price" class="col-sm-10 form-control">
                                            <option v-if="subscription.price != null" :value="subscription.price" selected>{{ subscription.price }}</option>
                                            <option v-for="(option, optionIndex) in getPrices(subscription.product_id)" :key="optionIndex" :value="option" v-if="option != subscription.price">{{ option }}</option>
                                        </select>
                                    </div>
                                </div>
                               
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="payment_type" class="col-form-label">Тип оплаты</label>
                                        <select v-model="subscription.payment_type" :name="'subscriptions.' + subIndex + '.payment_type'" id="payment_type" class="col-sm-10 form-control">
                                            <option v-for="(option, optionIndex) in paymentTypes" :key="optionIndex" :value="optionIndex">{{ option.title }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="status" class="col-form-label">Статус подписки</label>
                                        <select v-model="subscription.status" :name="'subscriptions.' + subIndex + '.status'" id="status" class="col-sm-10 form-control">
                                            <option v-for="(option, optionIndex) in getStatuses(subscription.payment_type)" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-6">
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
                                            @input="setDates(subscription.started_at, subIndex)"
                                        ></datetime>
                                    </div>
                                    <div class="form-group col-sm-6" v-if="subscription.payment_type == 'tries'">
                                        <label for="tries_at" class="col-form-label">Дата окончания</label>
                                        <datetime
                                            :name="'subscriptions.' + subIndex + '.tries_at'"
                                            type="date"
                                            v-model="subscription.tries_at"
                                            input-class="col-sm-10 my-class form-control"
                                            valueZone="Asia/Almaty"
                                            value-zone="Asia/Almaty"
                                            zone="Asia/Almaty"
                                            format="dd LLLL"
                                            @input="setDates(subscription.tries_at, subIndex)"
                                        ></datetime>
                                    </div>
                                    <div class="form-group col-sm-6" v-else-if="subscription.payment_type == 'transfer' || subscription.payment_type == 'cloudpayments'">
                                        <label for="ended_at" class="col-form-label">Дата окончания</label>
                                        <datetime
                                            :name="'subscriptions.' + subIndex + '.ended_at'"
                                            type="date"
                                            v-model="subscription.ended_at"
                                            input-class="col-sm-10 my-class form-control"
                                            valueZone="Asia/Almaty"
                                            value-zone="Asia/Almaty"
                                            zone="Asia/Almaty"
                                            format="dd LLLL"
                                            @input="setDates(subscription.ended_at, subIndex)"
                                        ></datetime>
                                    </div>
                                </div>
                                <div class="row" v-if="subscription.payment_type == 'transfer'">
                                    <div class="form-group col-sm-6">
                                        <label for="check" class="col-form-label">Загрузить чек</label>
                                        <upload-file :value-prop="subscription.newPayment.check" @file='setFileToSubscription($event, subIndex)'></upload-file>
                                    </div>
                                    <div class="form-group col-sm-6" v-if="false">
                                        <label for="quantity" class="col-form-label">Период</label>
                                        <select v-model="subscription.newPayment.quantity" name="quantity" id="quantity" class="col-sm-10 form-control">
                                            <option v-for="(option, optionIndex) in quantities" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" v-if="subscription.recurrent && subscription.payment_type == 'cloudpayments'" style="margin-bottom: 15px">
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

                                <div class="row">
                                    <div class="col-sm-12">
                                        <p style="width: 100%" v-for="(payment, paymentIndex) in subscription.payments" :key="paymentIndex">
                                            {{ payment.title }}
                                            <a v-if="payment.type == 'transfer' && payment.status == 'Completed'" target="_blank" :href="payment.check">(чек оплаты)</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="text-align: center">
                        <a @click="addProduct()" class="btn btn-primary">
                            Добавить услугу
                        </a>
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

export default {
    components: {TheMask},
    directives: {mask},
    props: [
        'typeProp',
        'nameProp',
        'customerIdProp',
        // 'paymentTypesProp',
        // 'statusesProp',
        // 'quantitiesProp',
    ],
    data() {
        return {
            customerId: this.customerIdProp,
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
            paymentTypes: {},
            statuses: {},
            quantities: {},
        }
    },
    watch: {
        customerIdProp: function(newVal, oldVal) { // watch it
            console.log('Prop changed: ', newVal, ' | was: ', oldVal);
            this.customerId = newVal;
            this.getCustomerWithData();
        },
        customer: function (newVal, oldVal) {
            console.log('Customer changed: ', newVal, ' | was: ', oldVal);
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
        salam() {
            console.log('salam');
        },
        setDates(startedAt, subIndex) {
            if (this.type == 'create') {
                this.subscriptions[subIndex].ended_at = moment().format();
                this.subscriptions[subIndex].tries_at = moment().add(7, 'days').format();
            }
        },
        showDate(date) {
            return moment(date).locale('ru').format('DD MMM');
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
            console.log(input);
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
            axios.get('/customers/' + this.customerId + '/with-data').then(response => {
                let data = response.data.data;
                if (response.data.data) {
                    console.log(data.phone);
                    if (data.phone.length == 10) {
                        data.phone = '+7' + data.phone;
                    }
                    this.customer = data;
                    this.subscriptions = data.subscriptions;
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
                customer: this.customer,
                subscriptions: this.subscriptions,
            };
            this.removeClassInvalid();
            axios.post('/customers/update-with-data', data).then(response => {
                this.spinnerData.loading = false;
                console.log(response.data.customer);
                let customer = response.data.customer;
                if (response.data.customer) {
                    this.customer = {
                        name: customer.name,
                        phone: customer.phone,
                        email: customer.email,
                        comments: customer.comments,
                    };

                    this.subscriptions = customer.subscriptions;
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
            console.log(id, subIndex);
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
                price: null,
                payment_type: null,
                started_at: now.format(),
                paused_at: null,
                ended_at: moment().format(),
                tries_at: moment().add(7, 'days').format(),
                status: null,
                description: null,
                newPayment: {
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
        getStatuses(paymentTypeKey) {
            if (paymentTypeKey) {
                return this.paymentTypes[paymentTypeKey]['statuses'];
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
    margin-right: 0;
    margin-left: 0;
}
</style>