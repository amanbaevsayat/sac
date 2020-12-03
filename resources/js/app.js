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
Vue.component('customer-component', require('./components/CustomerComponent.vue').default);
Vue.component('button-customer-component', require('./components/ButtonCustomerComponent.vue').default);
Vue.component('product-price-component', require('./components/ProductPriceComponent.vue').default);

import VueToast from 'vue-toast-notification';
import { Settings } from 'luxon'
 
Settings.defaultLocale = 'ru'
// import PortalVue from 'portal-vue'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
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
