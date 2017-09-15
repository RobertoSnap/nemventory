<template>
    <header class="app-header navbar">
        <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button" @click="mobileSidebarToggle">
            &#9776;
        </button>
        <a class="navbar-brand" href="http://nemventory2.app"><b>NEM</b>ventory</a>
        <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" @click="sidebarMinimize">&#9776;
        </button>

        <b-nav is-nav-bar class="ml-auto">
            <b-nav-item-dropdown :text="userName" right>

                <a class="dropdown-item" href="http://nemventory2.app">Exit</a>
            </b-nav-item-dropdown>
        </b-nav>
    </header>


</template>
<script>
    export default {
        name: 'header',
        data: function() {
            return {
                userName: undefined,
            }
        },
        methods: {
            sidebarToggle (e) {
                e.preventDefault()
                document.body.classList.toggle('sidebar-hidden')
            },
            sidebarMinimize (e) {
                e.preventDefault()
                document.body.classList.toggle('sidebar-minimized')
            },
            mobileSidebarToggle (e) {
                e.preventDefault()
                document.body.classList.toggle('sidebar-mobile-show')
            },
            asideToggle (e) {
                e.preventDefault()
                document.body.classList.toggle('aside-menu-hidden')
            },
            logout(){
                location.href = 'http://nemventory2.app'
            }
        },
        mounted () {
            axios.get('/api/user/name')
                .then(response => {
                    this.userName = response.data;
                    
                });
        }
    }
</script>
<style lang="scss">
    .ml-auto {
        margin-right: 50px;
    }
</style>
