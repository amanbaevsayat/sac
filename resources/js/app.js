require('./bootstrap');

window.Vue = require('vue');

var $loader = $("#loader");

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
});

$(document)
    .ajaxStart(function() {
        $loader.show();
    })
    .ajaxStop(function() {
        $loader.hide();
    });

(function($) {
    $(document).on("scroll", function() {
        if ($(this).scrollTop() < $(window).height()) {
            $("#up-button").hide();
        } else {
            $("#up-button").show();
        }
    });

    $("#up-button").on("click", function() {
        $("html, body").animate({ scrollTop: 0 }, "fast");
    });

    $("input[type='checkbox']").on("change", function() {
        $(this)
            .prev("input[type='hidden']")
            .val($(this).is(":checked") ? 1 : 0);
    });
})(jQuery);


Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('index-component', require('./components/IndexComponent.vue').default);
Vue.component('pulse-loader', require('vue-spinner/src/PulseLoader.vue').default);
Vue.component('payment-show-recurrent', require('./components/PaymentShowRecurrentComponent.vue').default);
Vue.component('upload-file', require('./components/UploadFileComponent.vue').default);
Vue.component('cloudpayments-widget', require('./components/CloudpaymentsWidgetComponent.vue').default);
Vue.component('cloudpayments-thank-you', require('./components/CloudpaymentsThankYouComponent.vue').default);
Vue.component('cloudpayments-checkout', require('./components/CloudpaymentsCheckoutComponent.vue').default);
Vue.component('customer-component', require('./components/CustomerComponent.vue').default);
Vue.component('button-customer-component', require('./components/ButtonCustomerComponent.vue').default);
Vue.component('product-price-component', require('./components/ProductPriceComponent.vue').default);
Vue.component('product-reasons-component', require('./components/ProductReasonComponent.vue').default);
Vue.component('product-payment-type-component', require('./components/ProductPaymentTypeComponent.vue').default);
Vue.component('product-users-component', require('./components/ProductUserComponent.vue').default);
Vue.component('users-bonuses-component', require('./components/UsersBonusesComponent.vue').default);

Vue.component('phone-component', require('./components/PhoneComponent.vue').default);
Vue.component('date-component', require('./components/DateComponent.vue').default);
Vue.component('statistics-component', require('./components/StatisticsComponent.vue').default);
Vue.component('simple-timeline', require('./components/SimpleTimeline/SimpleTimeline.vue').default);

// import SimpleTimeline from './components/SimpleTimeline/SimpleTimeline';
// import SimpleTimelineItem from './components/SimpleTimeline/SimpleTimelineItem';
// import SimpleTimelineControl from './components/SimpleTimeline/SimpleTimelineControl';

// export { SimpleTimeline, SimpleTimelineItem, SimpleTimelineControl };

import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(fas)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.config.productionTip = false

import VueToast from 'vue-toast-notification';

import Vue2Editor from "vue2-editor";

Vue.use(Vue2Editor);

import { Settings } from 'luxon'
// import moment from 'moment';
// Vue.use(moment);
Settings.defaultLocale = 'ru'
// import PortalVue from 'portal-vue'
import { BootstrapVue, BadgePlugin, ButtonPlugin, CardPlugin, LayoutPlugin } from 'bootstrap-vue'

Vue.use(BadgePlugin);
Vue.use(ButtonPlugin);
Vue.use(CardPlugin);
Vue.use(LayoutPlugin);
// import VModal from 'vue-js-modal/dist/ssr.nocss'
import 'vue-toast-notification/dist/theme-default.css';
// Vue.use(VModal, {})
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Vue.use(PortalVue);
Vue.use(VueToast, {
    position: 'top',
    duration: 7000,
});
// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
// Vue.use(IconsPlugin)

import { Datetime } from 'vue-datetime'
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css'
 
Vue.use(Datetime)
Vue.component('datetime', Datetime);


// Vue select start
import vSelect from 'vue-select'
Vue.component('v-select', vSelect)
import 'vue-select/dist/vue-select.css';

import VCalendar from 'v-calendar';
import DatePicker from 'v-calendar/lib/components/date-picker.umd'
import Calendar from 'v-calendar/lib/components/calendar.umd'
import Highcharts from 'highcharts'
import highchartsMore from 'highcharts/highcharts-more'
import highchartsFunnel from 'highcharts/modules/funnel'
import highchartsTimeline from 'highcharts/modules/timeline'

highchartsMore(Highcharts);
highchartsFunnel(Highcharts);
highchartsTimeline(Highcharts);
import HighchartsVue from 'highcharts-vue'
Highcharts.setOptions({
    lang: {
        months: [
            'Январь', 'Февраль', 'Март', 'Апрель',
            'Май', 'Июнь', 'Июль', 'Август',
            'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
        ],
        weekdays: [
            'Воскресение', 'Понедельник', 'Вторник', 'Среда', 'Четверг',
            'Пятница', 'Суббота',
        ]
    },
    time: {
        timezone: 'Asia/Almaty',
        // // timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        timezoneOffset: -1800,
        // useUTC: false,
    },
    global: {
        /**
         * Use moment-timezone.js to return the timezone offset for individual
         * timestamps, used in the X axis labels and the tooltip header.
         */
        timezoneOffset: 5 * 60,
    }
});
Vue.use(HighchartsVue, {
	highcharts: Highcharts
});

// Register components in your 'main.js'
Vue.component('date-picker', DatePicker)
Vue.component('calendar', Calendar)
// Use v-calendar & v-date-picker components
Vue.use(VCalendar, {
  componentPrefix: 'vc',
});

// Vue select end
// import ElementUI from 'element-ui';
// import 'element-ui/lib/theme-chalk/index.css';

// Vue.use(ElementUI);
// import { Button, Select, Option } from 'element-ui';
// Vue.component(Button.name, Button);
// Vue.component(Select.name, Select);
// Vue.component(Option.name, Select);



const app = new Vue({
    el: '#app',
});
