import '@helpers/less/pm-style.less'
import router from '@router/router'
import store from '@store/store'
import '@directives/directive'
import Mixin from '@helpers/mixin/mixin'
import ModuleMixins from '@helpers/mixin/module-mixin'
import App from './App.vue'
import '@helpers/common-components'
import menuFix from '@helpers/menu-fix';

import ElementUI from 'element-ui';
import '@assets/css/element-ui/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/en';

import BootstrapVue from 'bootstrap-vue';
import '@assets/css/bootstrap/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

import PrimeVue from 'primevue/config';
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Slider from 'primevue/slider';
import MultiSelect from 'primevue/multiselect';
import Calendar from 'primevue/calendar';

import "@assets/css/primevue/themes/bootstrap4-light-blue/theme.css"
import "@assets/css/primevue/primevue.css"
import "@assets/css/primevue/primeicons.css"

// import {registerLicense } from "@syncfusion/ej2-base";
// import { ScheduleComponent } from "@syncfusion/ej2-vue-schedule";
//Trial License Key	:	ORg4AjUWIQA/Gnt2UlhhQlVMfV5AQmFWfFN0QXNYdV92fldAcC0sT3RfQFliSH5RdkRiXn1feXRWQg==
// registerLicense("ORg4AjUWIQA/Gnt2UlhhQlVMfV5AQmFWfFN0QXNYdV92fldAcC0sT3RfQFliSH5RdkRiXn1feXRWQg==");
import { ScheduleComponent, Day, Week, WorkWeek, Month, Agenda } from "@node/@syncfusion/ej2-vue-schedule";

if (typeof Vue.observable !== 'function') {

    Vue.observable = function(obj) {
        return new Vue({
            data() {
                return obj;
            }
        });
    };
}

Vue.use(ElementUI, {locale});

Vue.use(BootstrapVue);

Vue.component('Dialog', Dialog);
Vue.component('DataTable', DataTable);
Vue.component('Column', Column);
Vue.component('ColumnGroup', ColumnGroup);
Vue.component('InputText', InputText);
Vue.component('Button', Button);
Vue.component('Slider', Slider);
Vue.component('MultiSelect', MultiSelect);
Vue.component('Calendar', Calendar);
Vue.use(PrimeVue);
Vue.component('ScheduleComponent', ScheduleComponent)
Vue.component('Day', Day)
Vue.component('Week', Week)
Vue.component('WorkWeek', WorkWeek)
Vue.component('Month', Month)

window.pmBus = new Vue();
Vue.config.devtools = true;

/**
 * Project template render
 */
var PM_Vue = {
    el: `#${PM_Vars.id}`,
    store,
    router,
    render: t => t(App),
    ModuleMixins
}
console.log("PM_Vue => ", PM_Vue);
Vue.mixin(Mixin);

new Vue(PM_Vue);

// fix the admin menu for the slug "vue-app"
menuFix('pm_projects');

//Always load in the bottom of the code
import '@helpers/underscore'


