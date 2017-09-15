export default {
    items: [
        {
            name: 'Dashboard',
            url: '/',
            icon: 'icon-speedometer',
        },
        {
            name: 'Warehouse',
            url: '/warehouse',
            icon: 'icon-organization',
            children: [
                {
                    name: 'List warehouses',
                    url: '/warehouse/list',
                    icon: 'icon-list'
                },
                {
                    name: 'Add new',
                    url: '/warehouse/create',
                    icon: 'icon-plus'
                },
            ]
        },
        {
            name: 'Item',
            url: '/item',
            icon: 'icon-mustache',
            children: [
                {
                    name: 'List items',
                    url: '/item/list',
                    icon: 'icon-list'
                },
                {
                    name: 'Add new',
                    url: '/item/create',
                    icon: 'icon-plus'
                },
            ]
        },
        {
            name: 'Purchase',
            url: '/purchase',
            icon: 'icon-basket-loaded',
            children: [
                {
                    name: 'List',
                    url: '/purchase/list',
                    icon: 'icon-list'
                },
                {
                    name: 'Add new',
                    url: '/purchase/create',
                    icon: 'icon-plus'
                },
            ]
        },
        {
            name: 'Sales',
            url: '/sales',
            icon: 'icon-rocket',
            children: [
                {
                    name: 'List',
                    url: '/sales/list',
                    icon: 'icon-list'
                },
                {
                    name: 'Add new',
                    url: '/sales/create',
                    icon: 'icon-plus'
                },
            ]
        },
        {
            divider: true
        },
        {
            title: true,
            name: 'Account'
        }
    ]
}
