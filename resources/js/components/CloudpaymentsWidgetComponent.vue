<template>
   <div v-if="payment.status == 'new'" class="container">
      <div class="wrapper" id="app">
         <div class="container">
            <vue-element-loading :active="spinnerData.loading" spinner="bar-fade-scale" color="#FF6700"/>
            <form :class="errors ? 'errors' : false" v-show="openFirstStep" v-on:submit.prevent="submit()" id="paymentFormSample" class="card-form__inner" autocomplete="off">
              <div class="row">
                <div class="col-sm-12" style="text-align: center;font-size: 14px;margin-bottom: 20px;font-weight: 600;">
                  <p style="line-height: 19px">Безопасность платежей обеспечивается банковской системой Республики Казахстан и <a href="https://cloudpayments.kz" target="_blank">Cloudpayments.kz</a></p>
                </div>
                <div class="col-sm-5">
                  <div class="card-input">
                    <label class="card-input__label">Имя</label>
                    <input type="text" class="card-input__input" v-model="customer.name" disabled>
                  </div>
                  <div class="card-input">
                    <label class="card-input__label">Телефон абонента</label>
                    <the-mask
                      :masked="false"
                      mask="+# (###) ### ##-##"
                      type="text"
                      class="card-input__input"
                      id="phone"
                      v-model="customer.phone"
                      required
                      disabled
                    ></the-mask>
                  </div>
                  <div class="card-input">
                    <label class="card-input__label">E-mail *</label>
                    <input type="email" class="card-input__input" v-model="customer.email" required @invalid="invalidateForm">
                    <small class="form-text text-muted">На указанный e-mail будет отправлен чек об оплате</small>
                  </div>
                </div>
                <div class="col-sm-1"></div>
                <div class="col-sm-6">
                  <div class="card-input">
                    <label class="card-input__label">Услуга</label>
                    <input type="text" class="card-input__input" v-model="product.title" disabled>
                  </div>
                  <div class="card-input">
                    <label class="card-input__label">Стоимость в месяц</label>
                    <input type="text" class="card-input__input" :value="payment.amount + ' тенге'" disabled>
                  </div>
                </div>
                <button type="submit" class="card-form__button" style="font-size: 18px">
                Перейти к оплате
                </button>
                <p style="margin-top: 20px;text-align: center;">Нажимая "Перейти к оплате", Вы даёте согласие
                    на обработку Ваших персональных данных и принимаете 
                    <a href="https://www.strela-academy.ru/offer.pdf" style="color: #fc0000;text-decoration: underline;" target="_blank">Пользовательское соглашение</a>
                </p>
              </div>
            </form>
            <div v-show="openSecondStep" class="popup-pay card-form__inner">
              <div class="widget-item success">
                <div class="widget-item-header">
                    <div class="header">
                        <div class="header-container">
                            <span class="close">
                                <svg width="28" height="28">
                                    <use xlink:href="#close"></use>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="widget-item-body status-container">
                    <!-- ko if: !threeDSPopupButtonIsVisible() -->
                    <div class="ico" style="text-align: center">
                        <div :class="{ 'success-icon': success, 'error-icon': !success }"></div>
                        <strong class="code">{{ message }}</strong>
                        <button v-if="!success" @click="reloadPage()" class="btn btn-primary" style="
                            margin: 0 auto;
                            display: block;
                            position: relative;
                            margin-top: 15px;
                        ">Обновите страницу, чтобы повторить платеж</button>
                        <p class="fail-msg" style="display: none;"></p>
                    </div>
                    <!-- /ko -->
                    <!-- ko if: threeDSPopupButtonIsVisible() --><!-- /ko -->
                </div>
              </div>
            </div>
            <div v-show="openSuccess" class="row popup-pay card-form__inner" style="
              background: rgba(255,255,255,0);
              background-image: url(http://d9hhrg4mnvzow.cloudfront.net/unbouncepages.com/yogawa/78ec164f-dark_100000000000000000001o.jpg);
              background-repeat: repeat;
              background-position: left top;
              border-style: none;
              border-radius: 0px;
              width: 100%;
              position: relative;"
            >
              <div class="col-sm-6">
                <div class="lp-pom-image-container" style="overflow: hidden;border-style: none;
                  border-radius: 20px;"
                >
                  <img style="width: 100%" src="/images/trener2.jpeg" alt="" loading="lazy" data-src-desktop-1x="//d9hhrg4mnvzow.cloudfront.net/unbouncepages.com/yogawa/635043a6-alex-shaw-msjsiqcm6og-unsplash_10mv0gs0kl0gs01500001o.jpg" data-src-mobile-1x="//d9hhrg4mnvzow.cloudfront.net/unbouncepages.com/yogawa/635043a6-alex-shaw-msjsiqcm6og-unsplash_107s06n07s06c00000501o.jpg" srcset="">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="trener-body">
                  <div style="text-align: center">
                    <div class="success-icon" style="margin: 12px auto"></div>
                    <h4>{{ message }}</h4>
                  </div>
                  <div>
                    <blockquote class="blockquote" style="border-left: 0.7rem solid rgb(30 190 136)">
                      <p class="mb-0">Екатерина Скурихина</p>
                      <footer style="font-size: 14px; color: #3c2529;" class="blockquote-footer">тренер йоги, пилатеса и стрейчинга</footer>
                    </blockquote>
                    <div style="padding-left: 10px">
                      <p style="font-size: 15px; margin-bottom: 9px;">По всем вопросам можно обратиться по номеру: </p>
                      <a style="font-size: 15px" href="tel:+79647881221">+7 964 788-12-21</a>
                      <p style="font-size: 15px; margin: 9px 0px">Больше полезных видео у меня в инстаграм: </p>
                      <a href="https://www.instagram.com/strela_academy/">
                        <img style="width: 30px" src="/images/instagram.svg" alt="">
                        <span style="font-size: 16px; margin-left: 5px">@strela_academy</span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
         </div>
      </div>
      <a href="https://cloudpayments.kz" target="_blank" style="width: 100%;text-align: center;display: block; margin-bottom: 30px" class="card__pay-systems">
         <img src="/images/pay-systems.svg" alt="Платежные системы" style="width: 100%; max-width: 500px">
      </a>
   </div>
</template>

<script>
import VueElementLoading from 'vue-element-loading'
import {
  TheMask
} from 'vue-the-mask'
import {
  mask
} from 'vue-the-mask'

export default {
  components: {
    TheMask,
    VueElementLoading
  },
  directives: {
    mask
  },
  props: [
    'paymentProp',
    'subscriptionProp',
    'customerProp',
    'productProp',
    'publicIdProp',
  ],
  data() {
    return {
      errors: false,
      spinnerData: {
        loading: false,
        color: '#6cb2eb',
        size: '100px',
      },
      payment: this.paymentProp,
      subscription: this.subscriptionProp,
      customer: this.customerProp,
      product: this.productProp,
      publicId: this.publicIdProp,
      data: {},
      openFirstStep: true,
      openSecondStep: false,
      openSuccess: false,
      message: 'Оплата завершена успешно!',
      success: true,
    }
  },
  computed: {
  },
  watch: {
  },
  created() {
    let recaptchaScript = document.createElement('script')
    recaptchaScript.setAttribute('src', 'https://widget.cloudpayments.ru/bundles/cloudpayments')
    document.head.appendChild(recaptchaScript);
  },
  mounted() {
  },
  methods: {
    invalidateForm() {
      this.errors = true;
    },
    sendSuccess() {
      let data = {
        phone: this.customer.phone,
        productId: this.product.id,
      };
      axios.post('/api/cloudpayments/change-status', data).then(response => {
        console.log(response);
      })
      .catch(err => {
        throw err;
      });
    },
    submit() {
      // var widget = new cp.CloudPayments();
      // var receipt = {
      //   Items: [//товарные позиции
      //     {
      //       label: this.product.title, //наименование товара
      //       price: this.payment.amount, //цена
      //       quantity: 1.00, //количество
      //       amount: this.payment.amount, //сумма
      //       object: this.product.id, // тег-1212 признак предмета расчета - признак предмета товара, работы, услуги, платежа, выплаты, иного предмета расчета
      //     }
      //   ],
      //   taxationSystem: 0, // Система налогооблажения (необязательный, если у вас одна система налогообложения)
      //   email: this.customer.email, //e-mail покупателя, если нужно отправить письмо с чеком
      //   phone: this.customer.phone, //телефон покупателя в любом формате, если нужно отправить сообщение со ссылкой на чек
      //   isBso: false, //чек является бланком строгой отчётности
      // };

      // var data = {};
      // data.cloudPayments = {
      //   recurrent: { 
      //     interval: 'Month',
      //     period: 1,
      //     customerReceipt: receipt
      //   },
      //   customerReceipt: {
      //     Items: [ //товарные позиции
      //       {
      //         label: this.product.title, // наименование товара
      //         price: this.payment.amount, // цена
      //         quantity: 1.00, //количество
      //         amount: this.payment.amount, // сумма
      //         vat: 0, // ставка НДС
      //         method: 0, // тег-1214 признак способа расчета - признак способа расчета
      //         object: 0, // тег-1212 признак предмета расчета - признак предмета товара, работы, услуги, платежа, выплаты, иного предмета расчета
      //         measurementUnit: "шт" //единица измерения
      //       },
      //     ],
      //     calculationPlace: "www.strela-academy.ru", //место осуществления расчёта, по умолчанию берется значение из кассы
      //     taxationSystem: 0, //система налогообложения; необязательный, если у вас одна система налогообложения
      //     email: this.customer.email, //e-mail покупателя, если нужно отправить письмо с чеком
      //     phone: this.customer.phone, //телефон покупателя в любом формате, если нужно отправить сообщение со ссылкой на чек
      //     //customerInfo: "", // тег-1227 Покупатель - наименование организации или фамилия, имя, отчество (при наличии), серия и номер паспорта покупателя (клиента)
      //     //customerInn: "7708806063", // тег-1228 ИНН покупателя 
      //     isBso: false, //чек является бланком строгой отчётности
      //     // AgentSign: null, //признак агента, тег ОФД 1057
      //     // amounts: {
      //     //     electronic: 1300.00, // Сумма оплаты электронными деньгами
      //     //     advancePayment: 0.00, // Сумма из предоплаты (зачетом аванса) (2 знака после запятой)
      //     //     credit: 0.00, // Сумма постоплатой(в кредит) (2 знака после запятой)
      //     //     provision: 0.00 // Сумма оплаты встречным предоставлением (сертификаты, др. мат.ценности) (2 знака после запятой)
      //     // }
      //   }
      // }; //создание ежемесячной подписки

      // widget.charge({ // options
      //     publicId: this.publicId, //id из личного кабинета
      //     description: 'Подписка на ежемесячный онлайн-абонемент', //назначение
      //     amount: this.payment.amount, //сумма
      //     currency: 'KZT', //валюта
      //     invoiceId: this.payment.id, //номер заказа  (необязательно)
      //     accountId: this.customer.phone, //идентификатор плательщика (обязательно для создания подписки)
      //     data: data
      // },
      // function (options) { // success
      //     console.log('Успешно');
      //     console.log(options);
      //     this.showResultPage('Оплата завершена успешно!', true);
      //     this.sendSuccess();
      // }.bind(this),
      // function (reason, options) { // fail
      //     console.log('Fail');
      //     console.log(reason);
      //     console.log(options);
      //     this.showResultPage(reason, false);
      // }.bind(this));
      let data = {
        subscription_id: this.subscription.id,
        product_id: this.product.id,
        customer: this.customer,
      };
      axios.post('/api/cloudpayments/generate-payment', data).then(response => {
        if (response.data.data.Url) {
          location.href = response.data.data.Url;
        }
      })
      .catch(err => {
        throw err;
      });
    },
    showResultPage(message, success) {
      this.message = message;
      this.success = success;
      if (this.success) {
        this.openSuccess = true;
      } else {
        this.openSecondStep = true;
      }
      this.openFirstStep = false;
    },
    reloadPage() {
      location.reload();
    },
  }
}
</script>

<style lang="scss" scoped>
form.errors {
  :invalid {
    border-color: #dc3545;
  }
}
@import url("https://fonts.googleapis.com/css?family=Source+Code+Pro:400,500,600,700|Source+Sans+Pro:400,600,700&display=swap");
.success-icon {
    background: url(/images/success.svg) no-repeat;
    display: block;
    width: 81px;
    height: 88px;
    margin: 0 auto 40px;
    background-position: center;
}

.error-icon {
    background: url(/images/error.svg) no-repeat;
    display: block;
    width: 81px;
    height: 88px;
    margin: 0 auto 40px;
}

body {
  background: #ddeefc;
  font-family: "Source Sans Pro", sans-serif;
  font-size: 16px;
}
* {
  box-sizing: border-box;
  &:focus {
    outline: none;
  }
}
.wrapper {
  min-height: 100vh;
  display: flex;
  padding: 50px 15px;
  @media screen and (max-width: 700px), (max-height: 500px) {
    flex-wrap: wrap;
    flex-direction: column;
  }
}

.card-form {
  max-width: 570px;
  margin: auto;
  width: 100%;

  @media screen and (max-width: 576px) {
    margin: 0 auto;
  }

  &__inner {
    background: #fff;
    box-shadow: 0 30px 60px 0 rgba(90, 116, 148, 0.4);
    border-radius: 10px;
    padding: 35px;

    @media screen and (max-width: 480px) {
      padding: 25px;
    }
    @media screen and (max-width: 360px) {
      padding: 15px;
    }
  }

  &__row {
    display: flex;
    align-items: flex-start;
    @media screen and (max-width: 480px) {
      flex-wrap: wrap;
    }
  }

  &__col {
    flex: auto;
    margin-right: 35px;

    &:last-child {
      margin-right: 0;
    }

    @media screen and (max-width: 480px) {
      margin-right: 0;
      flex: unset;
      width: 100%;
      margin-bottom: 20px;

      &:last-child {
        margin-bottom: 0;
      }
    }

    &.-cvv {
      max-width: 150px;
      @media screen and (max-width: 480px) {
        max-width: initial;
      }
    }
  }

  &__group {
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;

    .card-input__input {
      flex: 1;
      margin-right: 15px;

      &:last-child {
        margin-right: 0;
      }
    }
  }

  &__button {
    width: 100%;
    height: 55px;
    background: #2364d2;
    border: none;
    border-radius: 5px;
    font-size: 22px;
    font-weight: 500;
    font-family: "Source Sans Pro", sans-serif;
    box-shadow: 3px 10px 20px 0px rgba(35, 100, 210, 0.3);
    color: #fff;
    margin-top: 20px;
    cursor: pointer;

    @media screen and (max-width: 480px) {
      margin-top: 10px;
    }
  }
}

.card-item {
  max-width: 430px;
  height: 270px;
  margin-left: auto;
  margin-right: auto;
  position: relative;
  z-index: 2;
  width: 100%;

  @media screen and (max-width: 480px) {
    max-width: 310px;
    height: 220px;
    width: 90%;
  }

  @media screen and (max-width: 360px) {
    height: 180px;
  }

  &.-active {
    .card-item__side {
      &.-front {
        transform: perspective(1000px) rotateY(180deg) rotateX(0deg)
          rotateZ(0deg);
      }
      &.-back {
        transform: perspective(1000px) rotateY(0) rotateX(0deg) rotateZ(0deg);
      }
    }
  }

  &__focus {
    position: absolute;
    z-index: 3;
    border-radius: 5px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    transition: all 0.35s cubic-bezier(0.71, 0.03, 0.56, 0.85);
    opacity: 0;
    pointer-events: none;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.65);

    &:after {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      background: rgb(8, 20, 47);
      height: 100%;
      border-radius: 5px;
      filter: blur(25px);
      opacity: 0.5;
    }

    &.-active {
      opacity: 1;
    }
  }

  &__side {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 20px 60px 0 rgba(14, 42, 90, 0.55);
    transform: perspective(2000px) rotateY(0deg) rotateX(0deg) rotate(0deg);
    transform-style: preserve-3d;
    transition: all 0.8s cubic-bezier(0.71, 0.03, 0.56, 0.85);
    backface-visibility: hidden;
    height: 100%;

    &.-back {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      transform: perspective(2000px) rotateY(-180deg) rotateX(0deg) rotate(0deg);
      z-index: 2;
      padding: 0;
      height: 100%;

      .card-item__cover {
        transform: rotateY(-180deg)
      }
    }
  }
  &__bg {
    max-width: 100%;
    display: block;
    max-height: 100%;
    height: 100%;
    width: 100%;
    object-fit: cover;
  }
  &__cover {
    height: 100%;
    background-color: #1c1d27;
    position: absolute;
    height: 100%;
    background-color: #1c1d27;
    left: 0;
    top: 0;
    width: 100%;
    border-radius: 15px;
    overflow: hidden;
    &:after {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(6, 2, 29, 0.45);
    }
  }

  &__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 40px;
    padding: 0 10px;

    @media screen and (max-width: 480px) {
      margin-bottom: 25px;
    }
    @media screen and (max-width: 360px) {
      margin-bottom: 15px;
    }
  }

  &__chip {
    width: 60px;
    @media screen and (max-width: 480px) {
      width: 50px;
    }
    @media screen and (max-width: 360px) {
      width: 40px;
    }
  }

  &__type {
    height: 45px;
    position: relative;
    display: flex;
    justify-content: flex-end;
    max-width: 100px;
    margin-left: auto;
    width: 100%;

    @media screen and (max-width: 480px) {
      height: 40px;
      max-width: 90px;
    }
    @media screen and (max-width: 360px) {
      height: 30px;
    }
  }

  &__typeImg {
    max-width: 100%;
    object-fit: contain;
    max-height: 100%;
    object-position: top right;
  }

  &__info {
    color: #fff;
    width: 100%;
    max-width: calc(100% - 85px);
    padding: 10px 15px;
    font-weight: 500;
    display: block;

    cursor: pointer;

    @media screen and (max-width: 480px) {
      padding: 10px;
    }
  }

  &__holder {
    opacity: 0.7;
    font-size: 13px;
    margin-bottom: 6px;
    @media screen and (max-width: 480px) {
      font-size: 12px;
      margin-bottom: 5px;
    }
  }

  &__wrapper {
    font-family: "Source Code Pro", monospace;
    padding: 25px 15px;
    position: relative;
    z-index: 4;
    height: 100%;
    text-shadow: 7px 6px 10px rgba(14, 42, 90, 0.8);
    user-select: none;
    @media screen and (max-width: 480px) {
      padding: 20px 10px;
    }
  }

  &__name {
    font-size: 18px;
    line-height: 1;
    white-space: nowrap;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: uppercase;
    @media screen and (max-width: 480px) {
      font-size: 16px;
    }
  }
  &__nameItem {
    display: inline-block;
    min-width: 8px;
    position: relative;
  }

  &__number {
    font-weight: 500;
    line-height: 1;
    color: #fff;
    font-size: 27px;
    margin-bottom: 35px;
    display: inline-block;
    padding: 10px 15px;
    cursor: pointer;

    @media screen and (max-width: 480px) {
      font-size: 21px;
      margin-bottom: 15px;
      padding: 10px 10px;
    }

    @media screen and (max-width: 360px) {
      font-size: 19px;
      margin-bottom: 10px;
      padding: 10px 10px;
    }
  }

  &__numberItem {
    width: 16px;
    display: inline-block;
    &.-active {
      width: 30px;
    }

    @media screen and (max-width: 480px) {
      width: 13px;

      &.-active {
        width: 16px;
      }
    }

    @media screen and (max-width: 360px) {
      width: 12px;

      &.-active {
        width: 8px;
      }
    }
  }

  &__content {
    color: #fff;
    display: flex;
    align-items: flex-start;
  }

  &__date {
    flex-wrap: wrap;
    font-size: 18px;
    margin-left: auto;
    padding: 10px;
    display: inline-flex;
    width: 80px;
    white-space: nowrap;
    flex-shrink: 0;
    cursor: pointer;

    @media screen and (max-width: 480px) {
      font-size: 16px;
    }
  }

  &__dateItem {
    position: relative;
    span {
      width: 22px;
      display: inline-block;
    }
  }

  &__dateTitle {
    opacity: 0.7;
    font-size: 13px;
    padding-bottom: 6px;
    width: 100%;

    @media screen and (max-width: 480px) {
      font-size: 12px;
      padding-bottom: 5px;
    }
  }
  &__band {
    background: rgba(0, 0, 19, 0.8);
    width: 100%;
    height: 50px;
    margin-top: 30px;
    position: relative;
    z-index: 2;
    @media screen and (max-width: 480px) {
      margin-top: 20px;
    }
    @media screen and (max-width: 360px) {
      height: 40px;
      margin-top: 10px;
    }
  }

  &__cvv {
    text-align: right;
    position: relative;
    z-index: 2;
    padding: 15px;
    .card-item__type {
      opacity: 0.7;
    }

    @media screen and (max-width: 360px) {
      padding: 10px 15px;
    }
  }
  &__cvvTitle {
    padding-right: 10px;
    font-size: 15px;
    font-weight: 500;
    color: #fff;
    margin-bottom: 5px;
  }
  &__cvvBand {
    height: 45px;
    background: #fff;
    margin-bottom: 30px;
    text-align: right;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 10px;
    color: #1a3b5d;
    font-size: 18px;
    border-radius: 4px;
    box-shadow: 0px 10px 20px -7px rgba(32, 56, 117, 0.35);

    @media screen and (max-width: 480px) {
      height: 40px;
      margin-bottom: 20px;
    }

    @media screen and (max-width: 360px) {
      margin-bottom: 15px;
    }
  }
}

.card-list {
  margin-bottom: -130px;

  @media screen and (max-width: 480px) {
    margin-bottom: -120px;
  }
}

.card-input {
  max-width: 100%;
  margin-bottom: 20px;
  &__label {
    font-size: 14px;
    margin-bottom: 5px;
    font-weight: 500;
    color: #1a3b5d;
    width: 100%;
    display: block;
    user-select: none;
  }
  &__input {
    width: 100%;
    height: 50px;
    border-radius: 5px;
    box-shadow: none;
    border: 1px solid #ced6e0;
    transition: all 0.3s ease-in-out;
    font-size: 18px;
    padding: 5px 15px;
    background: none;
    color: #1a3b5d;
    font-family: "Source Sans Pro", sans-serif;

    &:hover,
    &:focus {
      border-color: #3d9cff;
    }

    &:focus {
      box-shadow: 0px 10px 20px -13px rgba(32, 56, 117, 0.35);
    }
    &.-select {
      -webkit-appearance: none;
      background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAeCAYAAABuUU38AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAUxJREFUeNrM1sEJwkAQBdCsngXPHsQO9O5FS7AAMVYgdqAd2IGCDWgFnryLFQiCZ8EGnJUNimiyM/tnk4HNEAg/8y6ZmMRVqz9eUJvRaSbvutCZ347bXVJy/ZnvTmdJ862Me+hAbZCTs6GHpyUi1tTSvPnqTpoWZPUa7W7ncT3vK4h4zVejy8QzM3WhVUO8ykI6jOxoGA4ig3BLHcNFSCGqGAkig2yqgpEiMsjSfY9LxYQg7L6r0X6wS29YJiYQYecemY+wHrXD1+bklGhpAhBDeu/JfIVGxaAQ9sb8CI+CQSJ+QmJg0Ii/EE2MBiIXooHRQhRCkBhNhBcEhLkwf05ZCG8ICCOpk0MULmvDSY2M8UawIRExLIQIEgHDRoghihgRIgiigBEjgiFATBACAgFgghEwSAAGgoBCBBgYAg5hYKAIFYgHBo6w9RRgAFfy160QuV8NAAAAAElFTkSuQmCC');
      background-size: 12px;
      background-position: 90% center;
      background-repeat: no-repeat;
      padding-right: 30px;
    }
  }
}

.slide-fade-up-enter-active {
  transition: all 0.25s ease-in-out;
  transition-delay: 0.1s;
  position: relative;
}
.slide-fade-up-leave-active {
  transition: all 0.25s ease-in-out;
  position: absolute;
}
.slide-fade-up-enter {
  opacity: 0;
  transform: translateY(15px);
  pointer-events: none;
}
.slide-fade-up-leave-to {
  opacity: 0;
  transform: translateY(-15px);
  pointer-events: none;
}

.slide-fade-right-enter-active {
  transition: all 0.25s ease-in-out;
  transition-delay: 0.1s;
  position: relative;
}
.slide-fade-right-leave-active {
  transition: all 0.25s ease-in-out;
  position: absolute;
}
.slide-fade-right-enter {
  opacity: 0;
  transform: translateX(10px) rotate(45deg);
  pointer-events: none;
}
.slide-fade-right-leave-to {
  opacity: 0;
  transform: translateX(-10px) rotate(45deg);
  pointer-events: none;
}



.github-btn {
  position: absolute;
  right: 40px;
  bottom: 50px;
  text-decoration: none;
  padding: 15px 25px;
  border-radius: 4px;
  box-shadow: 0px 4px 30px -6px rgba(36, 52, 70, 0.65);
  background: #24292e;
  color: #fff;
  font-weight: bold;
  letter-spacing: 1px;
  font-size: 16px;
  text-align: center;
  transition: all .3s ease-in-out;

  @media screen and (min-width: 500px) {
    &:hover {
      transform: scale(1.1);
      box-shadow: 0px 17px 20px -6px rgba(36, 52, 70, 0.36);
    }
  }

  @media screen and (max-width: 700px) {
    position: relative;
    bottom: auto;
    right: auto;
    margin-top: 20px;

    &:active {
      transform: scale(1.1);
      box-shadow: 0px 17px 20px -6px rgba(36, 52, 70, 0.36);
    }
  }
}
input[type="text"]:disabled {
    background: #f8f8f8;
}
</style>