require('./bootstrap');

window.$ = window.jQuery = require('jquery');

window.Vue = require('vue');

Vue.component('categoria', require('./components/Categoria.vue'));
Vue.component('talonario', require('./components/Talonario.vue'));
Vue.component('turno', require('./components/Turno.vue'));
Vue.component('deposito', require('./components/Deposito.vue'));
Vue.component('articulo', require('./components/Articulo.vue'));
Vue.component('cliente', require('./components/Cliente.vue'));
Vue.component('proveedor', require('./components/Proveedor.vue'));
Vue.component('rol', require('./components/Rol.vue'));
Vue.component('user', require('./components/User.vue'));
Vue.component('ingreso', require('./components/Ingreso.vue'));
Vue.component('ingreso_formulario', require('./components/IngresoFormulario.vue'));
Vue.component('venta', require('./components/Venta.vue'));
Vue.component('venta_formulario', require('./components/VentaFormulario.vue'));
Vue.component('dashboard', require('./components/Dashboard.vue'));
Vue.component('consulta_ingreso', require('./components/ConsultaIngreso.vue'));
Vue.component('consulta_venta', require('./components/ConsultaVenta.vue'));
Vue.component('notificacion', require('./components/Notificacion.vue'));
Vue.component('credito', require('./components/Credito.vue'));

const app = new Vue({
    el: '#app',
    data: {
        menu: 0,
        notifications: []
    },
    created() {
        let me = this;
        axios.post( 'notification/get' )
            .then( function( res ) {
                // console.log( res.data );userId
                me.notifications = res.data;
            } )
            .catch( function( error ) {
                console.log( error );
            } );
        
        var user_id = $('meta[name="userId"]').attr( 'content' );
        Echo.private( 'App.User.' + user_id ).notification( ( notification ) => {
            me.notifications.unshift( notification );
        } );
    }
});
