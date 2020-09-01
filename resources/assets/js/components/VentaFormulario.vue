<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> FACTURACIÓN
                </div>
                <div class="card-body">
                    <!--DETALLE-->
                    <template>
                        <div class="card-body">
                            <div class="alert alert-danger" v-if="Object.keys(validaciones).length !== 0">
                                <template v-for="(value, index) in validaciones">
                                    <strong v-bind:key="value[0]">ERROR EN {{ index.toUpperCase() }}:</strong> {{ value[0] }}.
                                    <br>
                                </template>
                            </div>                            
                            <div class="row border">
                                <div class="col-md-2" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <label for="">NIT</label>
                                        <input type="text" v-model="nit" 
                                          class="form-control" 
                                          @input="nit = $event.target.value.toUpperCase()" 
                                          placeholder="NIT"
                                          @keypress.enter="getClientes"
                                        >
                                    </div>
                                </div>   
                                <div class="col-md-10" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <label for="">NOMBRE DEL CLIENTE</label>
                                        <input type="text" v-model="cliente" 
                                          class="form-control" 
                                          @input="cliente = $event.target.value.toUpperCase()" 
                                          placeholder="Nombre del Cliente"
                                          @keypress.enter="getClientes"
                                        >
                                    </div>
                                </div>    
                                <div class="col-md-12" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <label for="">DIRECCION</label>
                                        <input type="text" v-model="direccion" 
                                          class="form-control" 
                                          @input="direccion = $event.target.value.toUpperCase()" 
                                          placeholder="Dirección del Cliente"
                                        >
                                    </div>
                                </div>                                                          
                            </div>
                            <div class="row border">
                                <div class="col-md-6" style="margin-top:1%;margin-bottom:1%;">
                                  <div class="row">
                                    <div class="col-md-12" style="margin-top:1%;margin-bottom:1%;">
                                        <div class="form-group">
                                            <label for="">CANTIDAD</label>
                                            <input type="number" v-model="cantidad" 
                                              class="form-control" 
                                              @input="cantidad = $event.target.value.toUpperCase()" 
                                              placeholder="Ingresar la cantidad"
                                            >
                                        </div>
                                    </div>                                    
                                    <div class="col-md-12" style="margin-top:1%;margin-bottom:1%;">
                                        <div class="form-group">
                                            <label for="">BUSCAR ARTICULO</label>
                                                <v-select 
                                                    v-model="selected"
                                                    :on-search="getArticulos"
                                                    label="nombre"
                                                    :options="lista_articulo"
                                                    :filterable="false"
                                                    placeholder="Buscando artículo..."
                                                    :on-change="getDatosArticulo"
                                                    >
                                                </v-select>
                                        </div>
                                    </div>                                      
                                  </div>
                                </div>                                    
                                <div class="col-md-6" style="margin-top:1%;margin-bottom:1%;background-color: lightblue;">
                                  <div class="row">
                                    <div class="col-md-12" style="margin-top:1%; margin-bottom:1%; font-size: 72px; text-align: right;">
                                        <label for=""> <b>Q {{ total.toFixed(2) }}</b> </label>
                                    </div>                                     
                                  </div>
                                </div>                               
                            </div>
                            &nbsp; 
                            &nbsp;  
                            <div class="form-group row border">
                                <div class="table-responsive col-md-12">
                                    &nbsp; 
                                    &nbsp;  
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Opciones</th>
                                                <th>Articulo</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="lista_detalle.length >= 1">
                                            <tr v-for="(detalle, index) in lista_detalle">
                                                <td>
                                                    <button class="btn btn-danger btn-sm" @click="quitarDeDetalle(index)">
                                                        <i class="icon-close"></i>
                                                    </button> {{ detalle.codigo }}
                                                </td>
                                                <td v-text="detalle.articulo"> </td>
                                                <td>  {{ detalle.precio }}  </td>
                                                <td>
                                                    <span style="color:red;">
                                                    <input type="number" v-model="detalle.cantidad" step="any" class="form-control"> 
                                                    Stock: {{ detalle.stock-detalle.cantidad }} </span> 
                                                </td>
                                                <td style="text-align: right;">  {{ (detalle.precio * detalle.cantidad).toFixed(2) }} </td>
                                            </tr>
                                            <tr style="background-color:#EEEEEE;">
                                                <td colspan="4" style="font-size: 22px;" align="right"> <strong>Total Q:</strong> </td>
                                                <td style="text-align: right;">{{ calcularTotal }} </td>
                                            </tr>
                                        </tbody>
                                        <tbody v-else>
                                            <tr>
                                                <td colspan="5"> No hay articulos agregados </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>                              
                            </div> 
                            <div class="form-group row">
                                <div class="col-md-5">
                                    <section>
                                        <input type="radio" v-model="tipo_comprobante" value="FACTURA"> FACTURA
                                        &nbsp; &nbsp; &nbsp; 
                                        <input type="radio" v-model="tipo_comprobante" value="RECIBO"> RECIBO
                                    </section>
                                </div>
                                <div class="col-md-5" style="text-align: right;">
                                    <input type="button" @click="abrirModalArticulo()" class="btn btn-success" value="ARTICULO">
                                    <input v-if="tipo_comprobante=='FACTURA'" type="button" @click="cobroFactura()" class="btn btn-success" value="FACTURAR">
                                    <div class="input-group" v-if="tipo_comprobante=='RECIBO'">
                                        <input type="number" v-model="descuento" class="form-control" placeholder="Descuento Q." aria-label="Descuento" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <input type="button" @click="cobroRecibo()" class="btn btn-primary" value="RECIBO">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <input type="button" class="btn btn-warning" value="CANCELAR VENTA" @click="verFormDetalleVentas()">
                                </div>
                            </div>                              
                        </div>
                    </template>
                    <!--FIN DETALLE-->
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>

    </main>
    <!-- /Fin del contenido principal -->
</template>

<script>
import vSelect from "vue-select";

    export default {
        data() {
            return {
                descuento: null,
                id_venta: 0,
                id_cliente: 0,
                tipo_comprobante: '',
                numero_comprobante: '',
                serie_comprobante: '',
                impuesto: 0.1,
                total: 0.0,
                total_impuesto: 0.0,
                total_parcial: 0.0,
                lista_venta: [],
                lista_clientes: [],
                lista_detalle: [],
                validaciones: {},

                txt_buscar_articulo: '',
                criterio_articulo: 'codigo',
                listado: 1, 
                precio: 0,
                cantidad: 0,
                nombre: '',
                nit: '',
                cliente: '',
                direccion:'',
                
                id_articulo: 0,
                
                modal: 0,
                titulo_modal: '',
                tipo_accion: 0,
                error_venta: 0,
                errores: [],
                paginacion: {
                    'total': 0,
                    'pagina_actual': 0,
                    'por_pagina': 0,
                    'ultima_pagina': 0,
                    'desde': 0,
                    'hasta': 0
                },
                offset: 3,
                criterio: 'numero_comprobante',
                txt_buscar: '',
                
                lista_articulo: [],
                codigo: '',
                articulo: '',
                precio: 0,
                cantidad: 1,
                flag_detalle: 0,
                subtotal : 0.0,
                stock: 0,

                lista_empleados: [],
                selected: null,
            }
        },
        components: {
            vSelect
        },
        computed: {
            calcularTotal: function(){
                var resultado = 0.0;
                for(var i=0; i<this.lista_detalle.length; i++)
                    resultado += this.lista_detalle[i].cantidad * this.lista_detalle[i].precio;
                    
                this.total = resultado
                return this.total.toFixed(2);
            }
        },        
        methods: {
            abrirModalArticulo(){
                this.modal = 1;
                this.tipo_accion = 1;
                this.titulo_modal = 'Agregar articulo';
            },
            
            getClientes() {
                let me = this;
                me.id_cliente = ''
                var url = '/clientes?nit=' + me.nit + '&cliente=' + me.cliente;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        respuesta.clientes.forEach(function(item) {
                          me.nit = ''
                          me.cliente = ''   
                          me.direccion = ''
                          me.id_cliente = ''                                                
                          me.nit = item.nit
                          me.cliente = item.cliente
                          me.direccion = item.direccion
                          me.id_cliente = item.id
                        })
                        console.log(me.nit, me.cliente, me.direccion, me.id_cliente)
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            verFormDetalleVentas(){
                this.validaciones = []
                this.id_cliente = '';
                this.lista_detalle = [];
                this.impuesto = 0.1;
                this.tipo_comprobante = '';
                this.serie_comprobante = '';
                this.numero_comprobante = '';
                this.nit = ''
                this.cliente = ''
                this.direccion = ''
                this.total = 0.0;
                this.lista_detalle = [];
                this.precio = 0;
                this.cantidad = 1;
                this.stock = 0;
                this.codigo = '';
                this.descuento = null;                
            },
            quitarDeDetalle(index) {
                this.lista_detalle.splice(index, 1);
            },
            getEmpleados(search, loading) {
                let me = this;
                loading( true );
                var url = '/users?filtro=' + search;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        q: search
                        me.lista_empleados = respuesta.users;
                        loading( false );
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },

            getArticulos(search, loading) {
                let me = this;
                var url = '/articulos?criterio=nombre&buscar=' + search;
                loading( true );
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data.articulos.data;
                        q: search
                        me.lista_articulo = respuesta;
                        loading( false );
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            getDatosArticulo(item) {
                let me = this;
                me.loading = true;
                me.id_articulo = item.id;

                if(me.verificarArticuloEnDetalle(item.id)){
                    swal({
                        type: 'error',
                        title: 'Error',
                        text: 'El articulo ya se encuentra agregado!'
                    }); 
                }
                else{
                    me.lista_detalle.push({
                        codigo: item.codigo,
                        id_articulo: item.id,
                        articulo: item.nombre,
                        cantidad: me.cantidad,
                        precio: item.precio_venta,
                        subtotal: item.precio_venta*me.cantidad,
                        descuento: 0,
                        stock: item.stock
                    });
                }
                me.codigo = ''
                me.cantidad = 1
                me.selected = null
            },

            verificarArticuloEnDetalle(id) {
                for (var i=0; i<this.lista_detalle.length; i++) {
                    if(id == this.lista_detalle[i].id_articulo)
                        return true;
                }
                return false;
            },
            cobroFactura() {
                let me = this;
                me.descuento = 0;
                me.tipo_comprobante='FACTURA';

                if(me.lista_detalle.length > 16){
                    swal({
                        type: 'error',
                        title: 'Error',
                        text: 'No puede agregar más de 16 tipos de artículos a la venta!'
                    }); 
                }
                else{
                    me.agregarVenta();
                }
            },
            cobroRecibo(){
                let me = this;
                me.tipo_comprobante='RECIBO';

                if(me.descuento > me.total){
                    swal({
                        type: 'error',
                        title: 'Error',
                        text: 'El descuento excede al valor total de la venta!'
                    }); 
                }
                else{
                    me.agregarVenta();
                }
            },         
            agregarVenta() {
                let me = this;
                me.errores = [];
                me.error_venta = 0;
                if(me.nit == '') {
                    me.error_venta = 1;
                    me.errores.push('(*) Ingrese el NIT del cliente'); 
                } if(me.cliente == '') {
                    me.error_venta = 1;
                    me.errores.push('(*) Ingrese el Nombre del cliente'); 
                } if(me.lista_detalle.length <= 0) {
                    me.error_venta = 1;
                    me.errores.push('(*) Ingresa articulos al detalle de la factura');
                }
                
                me.lista_detalle.map( function(x) {
                    if(Number(x.cantidad) > Number(x.stock) ) {
                        me.error_venta = 1;
                        me.errores.push('(*) Verifica stock del producto ' + x.articulo);
                    }
                } );

                if(me.error_venta == 1) {
                    let cadena = "<div class='row'>";
                    cadena += "<div class='col-1'></div>";
                    cadena += "<div class='col-10'>";
                    cadena += "<table>";
                    for(var i=0; i<me.errores.length; i++) {
                        cadena += "<tr>";
                        cadena += "<td></td>";
                        cadena += "<td>" + (i+1) + ".</td>" + "<td>" +  me.errores[i] + "</td>";
                        cadena += "</tr>";
                    }
                    cadena += "</table>";
                    cadena += "</div>";
                    cadena += "</div>";
                    swal({
                        type: 'error',
                        title: 'Errores:',
                        html: cadena
                    }); 
                }
                else{
                    axios.post('/venta', {
                            'id_cliente': me.id_cliente,
                            'nit': me.nit,
                            'cliente': me.cliente,
                            'direccion': me.direccion,
                            'tipo_comprobante': me.tipo_comprobante,
                            'numero_comprobante': me.numero_comprobante,
                            'serie_comprobante': me.serie_comprobante,
                            'impuesto': me.impuesto,
                            'total': me.total,
                            'descuento': me.descuento,
                            'data': me.lista_detalle
                        })
                        .then( function(res){
                            swal({
                                type: 'success',
                                title: 'La venta se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            
                            if(me.tipo_comprobante=='RECIBO')
                                window.open( '/venta/recibo/pdf/' + res.data.data.id);
                            else
                                window.open( '/venta/factura/pdf/' + res.data.data.id);
                            
                            me.verFormDetalleVentas();
                        } )
                        .catch( function(error){
                            if(error.response.status == 409)
                            {
                                me.validaciones = error.response.data
                            }
                        } )
                }
            },
            getReciboPDF(id){
                window.open( '/venta/recibo/pdf/' + id , '_blank' );
            }
        },
        mounted() {
            console.log('Component Venta cargado.')     
            this.verFormDetalleVentas()       
        }
    }
</script>
<style>
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .error{
        text-align: center;
        color: #B22305;
        font-style: oblique;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
    @media (min-width: 600px) {
        .btn-agregar{
            margin-top: 1.7rem;
        }   
    }
</style>
