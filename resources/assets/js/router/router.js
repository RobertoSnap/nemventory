import Vue from 'vue'
import Router from 'vue-router'

// Containers
import Full from '../containers/Full.vue'



// Views
import Dashboard from '../views/Dashboard.vue'

// Pages
import WarehouseList from '../views/warehouse/List.vue'
import WarehouseCreate from '../views/warehouse/Create.vue'
import WarehouseDetails from '../views/warehouse/Details.vue'

import ItemList from '../views/item/List.vue'
import ItemCreate from '../views/item/Create.vue'
import ItemDetails from '../views/item/Details.vue'

import PurchaseList from '../views/purchase/List.vue'
import PurchaseCreate from '../views/purchase/Create.vue'
import PurchaseEdit from '../views/purchase/Edit.vue'

import SalesList from '../views/sales/List.vue'
import SalesCreate from '../views/sales/Create.vue'
import SalesEdit from '../views/sales/Edit.vue'



Vue.use(Router);

export default new Router({
    mode: 'hash', // Demo is living in GitHub.io, so required!
    linkActiveClass: 'open active',
    scrollBehavior: () => ({y: 0}),
    routes: [
        {
            path: '/',
            name: 'NEMventory',
            component: Full,
            children: [
                {
                    path: '/',
                    name: 'Dashboard',
                    component: Dashboard
                },

                /*Warehouse*/
                {
                    path: '/warehouse/list',
                    name: 'Warehouse list',
                    component: WarehouseList,

                },
                {
                    path: '/warehouse/create',
                    name: 'Warehouse create',
                    component: WarehouseCreate,
                },
                {
                    path: '/warehouse/:id',
                    name: 'Warehouse details',
                    component: WarehouseDetails,
                    props: (route) => ({ id: route.params.id  })
                },



                /*Item*/
                {
                    path: '/item/list',
                    name: 'Item list',
                    component: ItemList,

                },
                {
                    path: '/item/create',
                    name: 'Create item',
                    component: ItemCreate,
                },
                {
                    path: '/item/:id',
                    name: 'Item details',
                    component: ItemDetails,
                    props: (route) => ({ id: route.params.id  })
                },


                /*Purchase*/
                {
                    path: '/purchase/list',
                    name: 'Purchase order list',
                    component: PurchaseList,
                },
                {
                    path: '/purchase/create',
                    name: 'Create purchase order',
                    component: PurchaseCreate,
                },
                {
                    path: '/purchase/:id',
                    name: 'Purchase edit',
                    component: PurchaseEdit,
                    props: (route) => ({ id: route.params.id  })
                },


                /*Sales*/
                {
                    path: '/sales/list',
                    name: 'Sales order list',
                    component: SalesList,
                },
                {
                    path: '/sales/create',
                    name: 'Create sales order',
                    component: SalesCreate,
                },
                {
                    path: '/sales/:id',
                    name: 'Sales edt',
                    component: SalesEdit,
                    props: (route) => ({ id: route.params.id  })
                },

            ]
        },
    ]
})
