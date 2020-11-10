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

import VueToast from 'vue-toast-notification';
import PortalVue from 'portal-vue'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
// Import one of available themes
import 'vue-toast-notification/dist/theme-default.css';
import 'bootstrap-vue/dist/bootstrap-vue.css'
Vue.use(PortalVue);
Vue.use(VueToast, {
    position: 'top',
    duration: 7000,
});
// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

import { Datetime } from 'vue-datetime'
// You need a specific loader for CSS files
import 'vue-datetime/dist/vue-datetime.css'
 
Vue.use(Datetime)
Vue.component('datetime', Datetime);

const app = new Vue({
    el: '#app',
});
