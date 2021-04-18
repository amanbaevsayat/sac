<template>
   <div class="container">
      <div class="wrapper" id="app">
         <div class="container">
            <div class="row popup-pay card-form__inner" style="
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
                    <h4>Оплата завершена успешно!</h4>
                  </div>
                  <div>
                    <blockquote class="blockquote" style="border-left: 0.7rem solid rgb(30 190 136)">
                      <p class="mb-0">Екатерина Скурихина</p>
                      <footer style="font-size: 14px; color: #3c2529;" class="blockquote-footer">тренер йоги, пилатеса и стрейчинга</footer>
                    </blockquote>
                    <div style="padding-left: 10px">
                      <p style="font-size: 15px; margin-bottom: 9px;">По всем вопросам можно обратиться по номеру: </p>
                      <a style="font-size: 15px" href="tel:+79647881221">+7 775 899-45-85</a>
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

export default {
  props: [
    'subscriptionProp',
  ],
  data() {
    return {
    }
  },
  computed: {
  },
  watch: {
  },
  created() {
  },
  mounted() {
  },
  methods: {
  }
}
</script>
