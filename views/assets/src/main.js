import '@helpers/less/pm-style.less'
import router from '@router/router'
import store from '@store/store'
import '@directives/directive'
import Mixin from '@helpers/mixin/mixin'
import ModuleMixins from '@helpers/mixin/module-mixin'
import App from './App.vue'
import '@helpers/common-components'
import menuFix from '@helpers/menu-fix';

// import ElementUI from '../../../node_modules/element-ui/types';
// import ElementUI from 'element-ui/types';
import ElementUI from 'element-ui';

import '@assets/css/element-ui/theme-chalk/index.css';
import locale from '../../../node_modules/element-ui/lib/locale/lang/en';

import BootstrapVue from '../../../node_modules/bootstrap-vue/src';
import '@assets/css/bootstrap/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

// import PrimeVue from '../../../node_modules/primevue/config';
import PrimeVue from 'primevue/config';
import Dialog from '../../../node_modules/primevue/dialog';
import DataTable from '../../../node_modules/primevue/datatable';
import Column from '../../../node_modules/primevue/column';
import ColumnGroup from '../../../node_modules/primevue/columngroup';
import Button from '../../../node_modules/primevue/button';
import InputText from '../../../node_modules/primevue/inputtext';
import Slider from '../../../node_modules/primevue/slider';
import MultiSelect from '../../../node_modules/primevue/multiselect';
import Calendar from '../../../node_modules/primevue/calendar';


import "@assets/css/primevue/themes/bootstrap4-light-blue/theme.css"
import "@assets/css/primevue/primevue.css"
import "@assets/css/primevue/primeicons.css"
import "@assets/css/syncfusion/syncfusion.css"

import  {ScheduleComponent} from '../../../node_modules/@syncfusion/ej2-vue-schedule';//added on 28-10-2024
import { registerLicense } from '../../../node_modules/@syncfusion/ej2-base';

registerLicense('ORg4AjUWIQA/Gnt2UlhhQlVMfV5AQmFWfFN0QXNYdV92fldAcC0sT3RfQFliSH5RdkRiXn1feXRWQg=='); //trail key prabakaran@santhila.co
// registerLicense('ORg4AjUWIQA/Gnt2UlhhQlVMfV5AQmBIYVp/TGpJfl96cVxMZVVBJAtUQF1hTX9SdkNjWH1ccXdURGheRes');

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
// Vue.component('Calendar', Calendar);
Vue.component('ScheduleComponent', ScheduleComponent);
Vue.use(PrimeVue);

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

Vue.mixin(Mixin);

new Vue(PM_Vue);

// fix the admin menu for the slug "vue-app"
menuFix('pm_projects');

//Always load in the bottom of the code
import '@helpers/underscore'
import '@syncfusion/ej2-base/styles/material.css';
// import '@syncfusion/ej2-vue-schedule/styles/material.css';
import '@syncfusion/ej2-schedule/styles/material.css';
import './test.css';

