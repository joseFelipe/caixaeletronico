/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");
import moment from 'moment';

import { Form, HasError, AlertError } from "vform";
import VueRouter from "vue-router";
import VueProgressBar from 'vue-progressbar';
import swal from 'sweetalert2';

window.swal = swal;

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

Vue.use(VueRouter);
Vue.use(VueProgressBar, options)

// PROGRESSBAR CONFIGURATION
const options = {
    color: '#00c4f5',
    failedColor: '#b30000',
    thickness: '3px',
    transition: {
        speed: '0.2s',
        opacity: '0.6s',
        termination: 300
    },
    autoRevert: true,
    location: 'top',
    inverse: false
}

//TOAST MESSAGE
const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

window.toast = toast;

window.Form = Form;

let routes = [
    {
        path: "/dashboard",
        component: require("./components/Dashboard.vue").default
    },
    {
        path: "/profile",
        component: require("./components/Profile.vue").default
    },
    {
        path: "/users",
        component: require("./components/Users.vue").default
    }
];

const router = new VueRouter({
    mode: "history",
    routes // short for `routes: routes`
});

Vue.filter('upText', function (text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
});

Vue.filter('Type', function (type) {
    switch (type) {
        case 0:
            return 'Saque';
        case 1:
            return 'Depósito';
        case 2:
            return 'Transferência';
        default:
            return 'Erro (case) type';
    }
});

Vue.filter('myDate', function (date) {
    return moment(date).format('DD/MM/YYYY');
});

Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue").default
);

window.Fire = new Vue();

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app",
    router
});
