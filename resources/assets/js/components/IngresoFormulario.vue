<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> REGISTRAR COMPRA           
                </div>
                <div class="card-body">
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
                                          @keypress.enter="getProveedores"
                                        >
                                    </div>
                                </div>   
                                <div class="col-md-10" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <label for="">NOMBRE DEL PROVEEDOR</label>
                                        <input type="text" v-model="proveedor" 
                                          class="form-control" 
                                          @input="proveedor = $event.target.value.toUpperCase()" 
                                          placeholder="Nombre del Proveedor"
                                          @keypress.enter="getProveedores"
                                        >
                                    </div>
                                </div>    
                                <div class="col-md-12" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <label for="">DIRECCION</label>
                                        <input type="text" v-model="direccion" 
                                          class="form-control" 
                                          @input="direccion = $event.target.value.toUpperCase()" 
                                          placeholder="Dirección del Proveedor"
                                        >
                                    </div>
                                </div>   
                                <div class="col-md-9" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <label for="">CONTACTO</label>
                                        <input type="text" v-model="contacto" 
                                          class="form-control" 
                                          @input="contacto = $event.target.value.toUpperCase()" 
                                          placeholder="Nombre del Contacto"
                                        >
                                    </div>
                                </div>  
                                <div class="col-md-3" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <label for="">TELEFONO CONTACTO</label>
                                        <input type="text" v-model="telefono_contacto" 
                                          class="form-control" 
                                          @input="telefono_contacto = $event.target.value.toUpperCase()" 
                                          placeholder="Numero de Telefono"
                                        >
                                    </div>
                                </div> 
                                <hr>
                                <div class="col-md-3">
                                    <div class="form-control">
                                        <label for="">SERIE</label>
                                        <input type="text" v-model="serie_comprobante" @input="serie_comprobante = $event.target.value.toUpperCase()" placeholder="Serie del comprobante" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-control">
                                        <label for="">NUMERO</label>
                                        <input type="text" v-model="numero_comprobante" @input="numero_comprobante = $event.target.value.toUpperCase()" placeholder="Numero del comprobante" class="form-control">
                                    </div>
                                </div>   
                                <div class="col-md-3">
                                    <div class="form-control">
                                        <label for="">FECHA DE INGRESO</label>
                                        <input type="date" v-model="fecha" placeholder="Fecha de ingreso" class="form-control">
                                    </div>
                                </div>   
                                <div class="col-md-3">
                                    <div class="form-control">
                                        <label for="">TOTAL DE LA FACTURA Q</label>
                                        <input type="text" v-model="total_cuadrar" @input="total_cuadrar = $event.target.value.toUpperCase()" placeholder="Total facturado" class="form-control">
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
                            
                            <div class="form-group row border">
                                <div class="table-responsive col-md-12">
                                    &nbsp;&nbsp; 
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Opcion</th>
                                                <th>Cantidad</th>
                                                <th>Articulo</th>
                                                <th>Precio</th>                                                
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
                                                <td> <input type="number" v-model="detalle.cantidad" step="any" class="form-control" value="0"> </td>
                                                <td v-text="detalle.articulo"> </td>
                                                <td> <input type="text" @keyup.capture="validarNumero(detalle.precio)" v-model="detalle.precio" step="any" class="form-control" value="0"> </td>                                                
                                                <td align="right">  {{ (detalle.precio * detalle.cantidad).toFixed(2) }} </td>
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
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                    <input type="button" class="btn btn-warning" value="CANCELAR REGISTRO" @click="verFormDetalleIngresos()">
                                    <input type="button" @click="agregarIngreso()" class="btn btn-success" value="GUARDAR">
                                </div>
                            </div>                                                      
                        </div>
                    </template>
                    <!--FIN DETALLE-->
                </div>
            </div>

        </div>
    </main>
</template>

<script>
    import vSelect from 'vue-select';
    import moment from 'moment'
    export default {
        data() {
            return {
                proveedor: '',
                txt_buscar_articulo: '',
                criterio_articulo: 'codigo',
                listado: 1, 
                id_ingreso: 0,
                id_proveedor: 0,
                id_articulo: 0,
                total_cuadrar: 0,
                tipo_comprobante: 'Boleta',
                numero_comprobante: '',
                serie_comprobante: '',
                fecha: '',
                impuesto: 12.5,
                total: 0.0,
                total_impuesto: 0.0,
                total_parcial: 0.0,
                lista_ingreso: [],
                lista_detalle: [],
                modal: 0,
                titulo_modal: '',
                tipo_accion: 0,
                error_ingreso: 0,
                errores: [],
                validaciones: {},
                paginacion: {
                    'total': 0,
                    'pagina_actual': 0,
                    'por_pagina': 0,
                    'ultima_pagina': 0,
                    'desde': 0,
                    'hasta': 0
                },
                offset: 3,
                criterio: 'tipo_comprobante',
                txt_buscar: '',
                lista_proveedores: [],
                lista_articulo: [],
                codigo: '',
                articulo: '',
                precio: 0,
                cantidad: 1,
                flag_detalle: 0,
                subtotal : 0.0,
                nit: '',
                proveedor: '',
                direccion: '',
                contacto: '',
                telefono_contacto: '',
                selected: null,
            }
        },
        components: {
            moment,
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
            getProveedores() {
                let me = this;
                me.id_proveedor = ''  
                var url = '/proveedores?nit=' + me.nit + '&proveedor=' + me.proveedor;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        respuesta.proveedores.forEach(function(item) {
                          me.nit = ''
                          me.proveedor = ''   
                          me.direccion = ''
                          me.id_proveedor = ''     
                          me.contacto = ''
                          me.telefono_contacto = ''                                               
                          me.nit = item.nit
                          me.proveedor = item.proveedor
                          me.direccion = item.direccion
                          me.contacto = item.contacto
                          me.telefono_contacto = item.telefono_contacto
                          me.id_proveedor = item.id
                        })
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },      
            validarNumero(numero){
                if(isNaN(numero))
                {
                    swal({
                        type: 'warning',
                        title: 'Advertencia',
                        html: 'El dato ingresado no es un número'
                    }); 
                }
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
            quitarDeDetalle(index) {
                this.lista_detalle.splice(index, 1);
            },
            verificarArticuloEnDetalle(id) {
                for (var i=0; i<this.lista_detalle.length; i++) {
                    if(id == this.lista_detalle[i].id_articulo)
                        return true;
                }
                return false;
            },
            verFormDetalleIngresos(){
                this.validaciones = []
                this.listado = this.listado == 1 ? 0 : 1;
                this.id_proveedor = 0;
                this.lista_detalle = [];
                this.impuesto = 12.5;
                this.tipo_comprobante = '';
                this.serie_comprobante = '';
                this.numero_comprobante = '';
                this.fecha = ''
                this.nit = ''
                this.proveedor = ''   
                this.direccion = ''
                this.id_proveedor = ''     
                this.contacto = ''
                this.telefono_contacto = '' 
                this.codigo = ''
                this.cantidad = 1 
                this.total = 0
                this.total_cuadrar = 0
            },
            agregarIngreso() {
                let me = this;
                me.errores = [];
                me.error_ingreso = 0;
                if(me.nit == '') {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingrese el NIT del proveedor'); 
                } if(me.proveedor == '') {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingrese el Nombre del proveedor'); 
                } if(me.serie_comprobante == '') {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingrese la Serie de la compra');
                } if(me.numero_comprobante == '') {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingrese el Numero de la compra');
                } if(me.fecha == '') {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingrese la Fecha de la compra');
                } if(isNaN(me.total_cuadrar)) {
                    me.error_ingreso = 1;
                    me.errores.push('(*) El total facturado no es un número');
                } if(me.total_cuadrar != me.total) {
                    me.error_ingreso = 1;
                    me.errores.push('(*) El total no cuadra');
                } if(me.lista_detalle.length <= 0) {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingresa articulos de la compra');
                }
                if(me.error_ingreso == 1) {
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
                    axios.post('/ingreso', {
                            'id_proveedor': me.id_proveedor,
                            'nit': me.nit,
                            'proveedor': me.proveedor,
                            'direccion': me.direccion,
                            'contacto': me.contacto,
                            'telefono_contacto': me.telefono_contacto,
                            'tipo_comprobante': me.tipo_comprobante,
                            'numero_comprobante': me.numero_comprobante,
                            'serie_comprobante': me.serie_comprobante,
                            'impuesto': me.impuesto,
                            'fecha': me.fecha,
                            'total': me.total,
                            'data': me.lista_detalle
                        })
                        .then( function(res){
                            swal({
                                type: 'success',
                                title: 'La compra se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.verFormDetalleIngresos();
                        } )
                        .catch( function(error){
                            if(error.response.status == 409)
                            {
                                me.validaciones = error.response.data
                            }
                        } )
                }
            },                  
        },
        mounted() {
            console.log('Component Formulario COmpra cargado.')
            this.verFormDetalleIngresos();
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
