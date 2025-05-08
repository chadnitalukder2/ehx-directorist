import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router';
import App from './App.vue';
import Dashboard from './admin/components/Dashboard.vue';
import Settings from './admin/components/Settings.vue';

import Categories from './admin/modules/Categories/AllCategory.vue';
import Tags from './admin/modules/Tags/AllTag.vue';

import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'


// Create router
const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        { path: '/', component: Dashboard },
        { path: '/categories', component: Categories },
        { path: '/tags', component: Tags },
        { path: '/settings', component: Settings }
    ]
});



// Create and mount Vue app
const app = createApp(App);
app.use(ElementPlus);
app.use(router);
app.mount('#ehx-directorist-app');

for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component)
  }