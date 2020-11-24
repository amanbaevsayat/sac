<template>
    <div class="container">
        <a @click="pay()">Pay</a>
    </div>
</template>

<script>
    export default {
        props: [
            'paymentProp',
            'subscriptionProp',
            'customerProp',
            'productProp',
            'publicIdProp',
        ],
        data() {
            return {
                payment: this.paymentProp,
                subscription: this.subscriptionProp,
                customer: this.customerProp,
                product: this.productProp,
                publicId: this.publicIdProp,
                data: {},
            }
        },
        mounted() {
            let recaptchaScript = document.createElement('script')
            recaptchaScript.setAttribute('src', 'https://widget.cloudpayments.ru/bundles/cloudpayments')
            document.head.appendChild(recaptchaScript);
        },
        methods: {
            pay() {
                this.fillInData();

                let widget = new cp.CloudPayments();
                

                widget.charge({ // options
                    publicId: this.publicId, //id из личного кабинета
                    description: this.subscription.description, //назначение
                    amount: this.payment.amount, //сумма
                    currency: 'KZT', //валюта
                    invoiceId: this.payment.id, //номер заказа  (необязательно)
                    accountId: this.customer.phone, //идентификатор плательщика (обязательно для создания подписки)
                    data: this.data
                },
                function (options) { // success
                    //действие при успешной оплате
                },
                function (reason, options) { // fail
                    //действие при неуспешной оплате
                });
            },
            fillInData() {
                if (this.payment.recurrent) {
                    let receipt = {
                        Items: [//товарные позиции
                            {
                                label: this.product.title, //наименование товара
                                price: this.payment.amount, //цена
                                quantity: 1.00, //количество
                                amount: this.payment.amount, //сумма
                                vat: 0, //ставка НДС
                                method: 0, // тег-1214 признак способа расчета - признак способа расчета
                                object: 0, // тег-1212 признак предмета расчета - признак предмета товара, работы, услуги, платежа, выплаты, иного предмета расчета
                            }
                        ],
                        taxationSystem: 0, //система налогообложения; необязательный, если у вас одна система налогообложения
                        email: this.customer.email, //e-mail покупателя, если нужно отправить письмо с чеком
                        phone: this.customer.phone, //телефон покупателя в любом формате, если нужно отправить сообщение со ссылкой на чек
                        isBso: false, //чек является бланком строгой отчётности
                        amounts:
                        {
                            electronic: 0.00, // Сумма оплаты электронными деньгами
                            advancePayment: 0.00, // Сумма из предоплаты (зачетом аванса) (2 знака после запятой)
                            credit: 0.00, // Сумма постоплатой(в кредит) (2 знака после запятой)
                            provision: 0.00 // Сумма оплаты встречным предоставлением (сертификаты, др. мат.ценности) (2 знака после запятой)
                        }
                    };

                    this.data.cloudPayments = {recurrent: { interval: this.payment.interval, period: this.payment.period, customerReceipt: receipt}}; //создание ежемесячной подписки
                } else {
                    this.data = {};
                }
            },
        }
    }
</script>
