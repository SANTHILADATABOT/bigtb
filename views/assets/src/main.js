import '@helpers/less/pm-style.less';
import router from '@router/router';
import store from '@store/store';
import '@directives/directive';
import Mixin from '@helpers/mixin/mixin';
import ModuleMixins from '@helpers/mixin/module-mixin';
import Vue from 'vue';
import App from './App.vue';
import '@helpers/common-components';
import menuFix from '@helpers/menu-fix';
import { SchedulePlugin, ScheduleComponent } from '@syncfusion/ej2-vue-schedule';

if (typeof Vue.observable !== 'function') {

    Vue.observable = function(obj) {
        return new Vue({
            data() {
                return obj;
            }
        });
    };
}
Vue.component('ScheduleComponent', ScheduleComponent);
Vue.use(SchedulePlugin);

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


