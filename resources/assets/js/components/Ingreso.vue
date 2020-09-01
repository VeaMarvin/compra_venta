<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Compras Registradas                  
                </div>
                <div class="card-body">
                    <!--LISTADO-->
                    <template v-if="listado == 1">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select class="form-control col-md-3" v-model="criterio">
                                            <option value="numero_comprobante">Número Factura</option>
                                        </select>
                                        <input type="text" v-model="txt_buscar" @keyup.enter="listarIngreso(1, criterio, txt_buscar)" class="form-control" placeholder="Texto a buscar">
                                        <button type="submit" @click="listarIngreso(1, criterio, txt_buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                        &nbsp;&nbsp;
                                        <button type="button" class="btn btn-success" @click="listarIngreso(1,'','')"><i class="fa fa-refresh"></i> Recargar</button>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>Usuario</th>
                                            <th>Proveedor</th>
                                            <th>Empresa</th>
                                            <th>Serie / No. Factura</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ingreso in lista_ingreso">
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm" @click="mostrarDetalle(ingreso.id)">
                                                    <i class="icon-eye"></i>
                                                </button> &nbsp;
                                                <template v-if="ingreso.estado == 'REGISTRADO' && puede_anular(ingreso.created_at) == false">
                                                    <button type="button" class="btn btn-danger btn-sm" @click="anularIngreso(ingreso.id)">
                                                        <i class="icon-trash"></i>
                                                    </button>
                                                </template>
                                            </td>
                                            <td v-text="ingreso.usuario">  </td>  
                                            <td v-text="ingreso.nombre">  </td>  
                                            <td v-text="ingreso.contacto">  </td>  
                                            <td v-text="ingreso.serie_comprobante + ' / F.' + ingreso.numero_comprobante"> </td>  
                                            <td v-text="ingreso.fecha_hora">  </td>  
                                            <td class="text-right" v-text="'Q '+ingreso.total">  </td>  
                                            <td class="text-center" v-if="ingreso.estado == 'REGISTRADO'">
                                                <span class="badge badge-success">Registrado</span>
                                            </td>
                                            <td class="text-center" v-else>
                                                <span class="badge badge-warning">Anulado</span>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item" v-if="paginacion.pagina_actual > 1">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(paginacion.pagina_actual - 1, criterio, txt_buscar)">Ant</a>
                                    </li>
                                    <li class="page-item" v-for="pagina in numeroPaginas" :class="[pagina == paginaActiva ? 'active' : '']">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagina, criterio, txt_buscar)" v-text="pagina"></a>
                                    </li>
                                    <li class="page-item" v-if="paginacion.pagina_actual < paginacion.ultima_pagina">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(paginacion.pagina_actual + 1, criterio, txt_buscar)">Sig</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </template>
                    <!--FIN LISTADO-->
                    <!--DETALLE-->
                    <template v-if="listado == 0">
                        <div class="card-body">
                            <div class="row border">
                                <div class="alert alert-danger" v-if="Object.keys(validaciones).length !== 0">
                                    <template v-for="(value, index) in validaciones">
                                        <strong v-bind:key="value[0]">ERROR EN {{ index.toUpperCase() }}:</strong> {{ value[0] }}.
                                        <br>
                                    </template>
                                </div>

                                <div class="col-md-12" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <label for="">Proveedor</label>
                                        <v-select 
                                            :on-search="getProveedores"
                                            label="nombre"
                                            :options="lista_proveedores"
                                            placeholder="Buscando proveedores..."
                                            :on-change="getDatosProveedor">
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-control">
                                        <label for="">Serie</label>
                                        <input type="text" v-model="serie_comprobante" @input="serie_comprobante = $event.target.value.toUpperCase()" placeholder="Serie del comprobante" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-control">
                                        <label for="">Numero</label>
                                        <input type="text" v-model="numero_comprobante" @input="numero_comprobante = $event.target.value.toUpperCase()" placeholder="Numero del comprobante" class="form-control">
                                    </div>
                                </div>   
                                <div class="col-md-3">
                                    <div class="form-control">
                                        <label for="">Fecha de Ingreso</label>
                                        <input type="date" v-model="fecha" placeholder="Fecha de ingreso" class="form-control">
                                    </div>
                                </div>                                  
                            
                                <div class="col-md-3">
                                    &nbsp; 
                                    <div class="form-group text-center">
                                        <label for="">Agregar artículo <span v-show="id_articulo == 0" style="color:#3B0B0B;"> (*) </span> </label>
                                        &nbsp;
                                        <input value="Mostrar artículos" type="button" @click="abrirModalArticulo()" class="btn btn-primary" >
                                    </div>
                                </div>    
                                &nbsp;&nbsp;                            
                            </div>
                            <br><br>
                            <div class="form-group row border">
                                <div class="table-responsive col-md-12">
                                    &nbsp;&nbsp; 
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Opcion</th>
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
                                                    </button>
                                                </td>
                                                <td v-text="detalle.articulo"> </td>
                                                <td> <input type="number" v-model="detalle.precio" step="any" class="form-control" value="0"> </td>
                                                <td> <input type="number" v-model="detalle.cantidad" step="any" class="form-control" value="0"> </td>
                                                <td>  {{ detalle.precio * detalle.cantidad }} </td>
                                            </tr>
                                            <tr style="background-color:#EEEEEE;">
                                                <td colspan="4" align="right"> <strong>Total neto:</strong> </td>
                                                <td>Q {{ total = calcularTotal }} </td>
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
                                <div class="col-md-9"></div>
                                <div class="col-md-3">
                                    <input type="button" class="btn btn-secondary" value="Cerrar" @click="verFormDetalleIngresos()">
                                    <input type="button" @click="agregarIngreso()" class="btn btn-primary" value="Registrar">
                                </div>
                            </div>
                        </div>
                    </template>
                    <!--FIN DETALLE-->
<!-- VER DETALLE-->
                    <template v-if="flag_detalle == 1">
                        <div class="card-body">
                            <div class="form-group row border">
                                <div class="col-md-8" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <label for="">Proveedor</label>
                                        <p v-text="proveedor"></p>
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-top:2%;margin-bottom:2%;">
                                    <label for="">Impuesto</label>
                                    <p v-text="impuesto">%</p>
                                </div>
                                <div class="col-md-4" style="margin-bottom:2%;">
                                    <div class="form-control">
                                        <label for="">Tipo de comprobante</label>
                                        <p v-text="tipo_comprobante"></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-control">
                                        <label for="">Serie</label>
                                        <p v-text="serie_comprobante"></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-control">
                                        <label for="">Numero</label>
                                        <p v-text="numero_comprobante"></p>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group row border">
                                <div class="table-responsive col-md-12">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Articulo</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="lista_detalle.length >= 1">
                                            <tr v-for="detalle in lista_detalle">
                                                <td v-text="detalle.nombre"> </td>
                                                <td v-text="detalle.precio"> </td>
                                                <td v-text="detalle.cantidad"> </td>
                                                <td>Q  {{ detalle.precio * detalle.cantidad }} </td>
                                            </tr>
                                            <tr style="background-color:#EEEEEE;">
                                                <td colspan="3" align="right"> <strong>Total neto:</strong> </td>
                                                <td>Q  {{ total }} </td>
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
                                <div class="col-md-12">
                                    <input type="button" class="btn btn-secondary" value="Cerrar" @click="cerrarListadoDetalle()">
                                </div>
                            </div>
                        </div>
                    </template>
<!--FIN VER DETALLE-->
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar':modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none; overflow-y: scroll;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content" style="position: absolute; width: 120%">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titulo_modal">  </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModal()">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio_articulo">
                                        <option value="nombre">Nombre</option>
                                        <option value="codigo">Codigo</option>
                                    </select>
                                    <input type="text" v-model="txt_buscar_articulo" @input="txt_buscar_articulo = $event.target.value.toUpperCase()" @keyup.enter="getArticulos(criterio_articulo, txt_buscar_articulo)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="getArticulos(criterio_articulo, txt_buscar_articulo)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Agregar</th>
                                        <th>Codigo</th>
                                        <th>Nombre</th>
                                        <th>Precio de venta</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="articulo in lista_articulo">
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm" @click="agregarDetalleModal(articulo)">
                                                <i class="icon-check"></i>
                                            </button> &nbsp;
                                        </td>
                                        <td v-text="articulo.codigo">  </td>
                                        <td v-text="articulo.nombre">  </td>  
                                        <td v-text="articulo.precio_venta">  </td>  
                                        <td v-text="articulo.stock">  </td>  
                                        <td v-if="articulo.estado == 1">
                                            <span class="badge badge-success">Alta</span>
                                        </td>
                                        <td v-else>
                                            <span class="badge badge-warning">Baja</span>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModal()">Cerrar</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->

    <!--Inicio del modal agregar/actualizar-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar':data_proveedor.modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none; overflow-y: scroll;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content" style="position: absolute; width: 100%">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="data_proveedor.titulo_modal">  </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModalProveedor()">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" v-if="Object.keys(data_proveedor.validaciones).length !== 0">
                        <template v-for="(value, index) in data_proveedor.validaciones">
                            <strong v-bind:key="value[0]">ERROR EN {{ index.toUpperCase() }}:</strong> {{ value[0] }}.
                            <br>
                        </template>
                    </div>

                    <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                            <div class="col-md-9">
                                <input type="text" v-model="data_proveedor.nombre" @input="data_proveedor.nombre = $event.target.value.toUpperCase()" class="form-control" placeholder="Nombre del proveedor">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">NIT</label>
                            <div class="col-md-9">
                                <input type="text" v-model="data_proveedor.numero_documento" @input="data_proveedor.numero_documento = $event.target.value.toUpperCase()" class="form-control" placeholder="Numero de documento">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Direccion</label>
                            <div class="col-md-9">
                                <input type="text" v-model="data_proveedor.direccion" @input="data_proveedor.direccion = $event.target.value.toUpperCase()" class="form-control" placeholder="Direccion">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Telefono</label>
                            <div class="col-md-9">
                                <input type="text" v-model="data_proveedor.telefono" @input="data_proveedor.telefono = $event.target.value.toUpperCase()" class="form-control" placeholder="Telefono">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Email</label>
                            <div class="col-md-9">
                                <input type="text" v-model="data_proveedor.email" @input="data_proveedor.email = $event.target.value.toLowerCase()" class="form-control" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Contacto</label>
                            <div class="col-md-9">
                                <input type="text" v-model="data_proveedor.contacto" @input="data_proveedor.contacto = $event.target.value.toUpperCase()" class="form-control" placeholder="Contacto">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Telefono de contacto</label>
                            <div class="col-md-9">
                                <input type="text" v-model="data_proveedor.telefono_contacto" @input="data_proveedor.telefono_contacto = $event.target.value.toUpperCase()" class="form-control" placeholder="Telefono de contacto">
                            </div>
                        </div>
                        <div v-show="data_proveedor.error_proveedor" class="form-group row" style="display:flex; justify-content: center;">
                            <div v-if="data_proveedor.error_proveedor==1" v-for="error in data_proveedor.errores">
                                <span class="help-block error" v-text="error"></span>
                                <br>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModalProveedor()">Cerrar</button>
                    <button type="button" class="btn btn-primary" @click="agregarProveedor()">Agregar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

    </main>
    <!-- /Fin del contenido principal -->
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
                precio: 0,
                cantidad: 0,
                nombre: '',
                id_ingreso: 0,
                id_proveedor: 0,
                id_articulo: 0,
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
                criterio: 'numero_comprobante',
                txt_buscar: '',
                lista_proveedores: [],
                lista_articulo: [],
                codigo: '',
                articulo: '',
                precio: 0,
                cantidad: 0,
                flag_detalle: 0,
                subtotal : 0.0,

                data_proveedor: {
                    id_proveedor: 0,
                    nombre: '',
                    tipo_documento: '',
                    numero_documento: 'CF',
                    direccion: '',
                    email: '',
                    telefono: '',
                    contacto: '',
                    telefono_contacto: '',
                    lista_proveedor: [],
                    modal: 0,
                    titulo_modal: '',
                    tipo_accion: 0,
                    error_proveedor: 0,
                    errores: [],
                    validaciones: {},
                },
            }
        },
        components: {
            moment,
            vSelect
        },
        computed: {
            paginaActiva: function() {
                return this.paginacion.pagina_actual;
            },
            numeroPaginas: function() {
                if(!this.paginacion.hasta) {
                    return [];
                }
                var desde = this.paginacion.pagina_actual - this.offset;
                if(desde < 1) {
                    desde = 1;
                }
                var hasta = desde + (this.offset * 2);
                if(hasta >= this.paginacion.ultima_pagina) {
                    hasta = this.paginacion.ultima_pagina;
                }
                var paginas_arreglo = [];
                while(desde <= hasta) {
                    paginas_arreglo.push(desde);
                    desde++;
                }
                return paginas_arreglo;
            },
            calcularTotal: function(){
                var resultado = 0.0;
                for(var i=0; i<this.lista_detalle.length; i++)
                    resultado += this.lista_detalle[i].cantidad * this.lista_detalle[i].precio;
                return resultado.toFixed();
            }
        },
        methods: {
            puede_anular(fecha){
                if(moment(new Date()).format('YYYY-MM-DD') == moment(new Date(fecha)).format('YYYY-MM-DD'))
                    return false
                else
                    return true
            },
            cerrarListadoDetalle(){
                this.flag_detalle = 0;
                this.listado = 1;
                
            },
            mostrarDetalle(id) {
                let me = this;
                me.flag_detalle = 1;
                me.listado = 2;
                var ingreso_cabecera = [];
                me.lista_detalle  = [];
                var url_cabecera = '/ingreso/get/cabecera?&id=' + id;
                axios.get( url_cabecera )
                    .then( function(res){
                        ingreso_cabecera = res.data.cabecera;
                        me.proveedor = ingreso_cabecera[0].nombre;
                        me.tipo_comprobante = ingreso_cabecera[0].tipo_comprobante;
                        me.serie_comprobante = ingreso_cabecera[0].serie_comprobante;
                        me.numero_comprobante = ingreso_cabecera[0].numero_comprobante;
                        me.impuesto = ingreso_cabecera[0].impuesto;
                        me.total = ingreso_cabecera[0].total;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
                var url_detalles = '/ingreso/get/detalles?&id=' + id;
                axios.get( url_detalles )
                    .then( function(res){
                        me.lista_detalle = res.data.detalles;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
                me.id_proveedor = 0;
                me.tipo_comprobante = 'Boleta';
                me.numero_comprobante = '';
                me.serie_comprobante = '';
                me.impuesto = 12.5;
                me.total = 0.0;
                me.lista_detalle = [];
                me.precio = 0;
                me.cantidad = 0;
            },
            agregarDetalleModal(data=[]) {
                let me = this;
                if(me.verificarArticuloEnDetalle(data.id)){
                    swal({
                        type: 'error',
                        title: 'Error',
                        text: 'El articulo ya se encuentra agregado!'
                    }); 
                }
                else{
                    me.lista_detalle.push({
                        id_articulo: data.id,
                        articulo: data.nombre,
                        cantidad: 1,
                        precio: ''
                    });
                    me.lista_articulo = [];
                }
            
            },
            getArticulos(criterio, buscar) {
                let me = this;
                var url = '/articulos?&criterio=' + criterio + '&buscar=' + buscar;
                axios.get( url )
                    .then( function(res){
                        me.lista_articulo = res.data.articulos.data;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
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
            agregarDetalleIngreso(){
                let me = this;
                let errors = [];
                let error = false;
                if(me.id_articulo == 0) {
                    error = true;
                    errors.push('&nbspArticulo');
                }
                if(me.precio == 0) {
                    error = true;
                    errors.push('Precio');
                }
                if(me.cantidad == 0) {
                    error = true;
                    errors.push('&nbspCantidad');
                }
                if(error){
                    let cadena = "<div class='row'>";
                    cadena += "<div class='col-5'></div>";
                    cadena += "<div class='col-4'>";
                    cadena += "<table>";
                    for(var i=0; i<errors.length; i++) {
                        cadena += "<tr>";
                        //cadena += "<td>" +  + "." + "</td>";
                        cadena += "<td></td>";
                        cadena += "<td>" + (i+1) + ".</td>" + "<td>" +  errors[i] + "</td>";
                        cadena += "</tr>";
                    }
                    cadena += "</table>";
                    cadena += "</div>";
                    cadena += "<div class='col-3'></div>";
                    cadena += "</div>";
                    swal({
                        type: 'error',
                        title: 'Ingresa:',
                        html: cadena
                    }); 
                }
                else{
                    if(me.verificarArticuloEnDetalle(me.id_articulo)){
                        swal({
                            type: 'error',
                            title: 'Error',
                            text: 'El articulo ya se encuentra agregado!'
                        }); 
                    }
                    else{
                        me.lista_detalle.push({
                            id_articulo: me.id_articulo,
                            articulo: me.articulo,
                            cantidad: me.cantidad,
                            precio: me.precio
                        });
                        me.articulo = '';
                        me.id_articulo = 0;
                        me.cantidad = 0;
                        me.precio = 0;
                        me.lista_articulo = [];
                    }
                }
            },
            buscarArticulo(){
                let me = this;
                axios.get( '/articulo/buscar/filtro?filtro=' + me.id_articulo )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_articulo = respuesta.articulos;
                        if(me.lista_articulo.length > 0){
                            me.articulo = me.lista_articulo[0].nombre;
                            me.id_articulo = me.lista_articulo[0].id;
                        }
                        else{
                            me.articulo = 'No existe articulo';
                            me.id_articulo = 0;
                        }
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            getProveedores(search, loading) {
                let me = this;
                loading( true );
                var url = '/proveedores?filtro=' + search;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        q: search
                        me.lista_proveedores = respuesta.proveedores;
                        loading( false );
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            getDatosProveedor(valor) {
                let me = this;
                me.loading = true;
                me.id_proveedor = valor.id;
            },
            verFormDetalleIngresos(){
                this.validaciones = []
                this.listado = this.listado == 1 ? 0 : 1;
                this.id_proveedor = 0;
                this.lista_detalle = [];
                this.impuesto = 12.5;
                this.tipo_comprobante = 'Boleta';
                this.serie_comprobante = '';
                this.numero_comprobante = '';
            },
            cambiarPagina(pagina, criterio, txt_buscar) {
                let me = this;
                this.paginacion.pagina_actual = pagina;
                me.listarIngreso(pagina, criterio, txt_buscar);
            },
            listarIngreso(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/ingreso?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_ingreso = respuesta.ingresos.data;
                        me.paginacion = respuesta.paginacion;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            agregarIngreso() {
                let me = this;
                me.errores = [];
                me.error_ingreso = 0;
                if(me.id_proveedor == 0) {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingrese el proveedor'); 
                } if(me.serie_comprobante == '') {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingrese la serie de la compra');
                } if(me.numero_comprobante == '') {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingrese el numero de la compra');
                } if(me.fecha == '') {
                    me.error_ingreso = 1;
                    me.errores.push('(*) Ingrese la fecha de la compra');
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
                    //cadena += "<div class='col-2'></div>";
                    cadena += "</div>";
                    swal({
                        type: 'error',
                        title: 'Errores:',
                        html: cadena
                    }); 
                    //alert( me.errores.join() );
                }
                else{
                    axios.post('/ingreso', {
                            'id_proveedor': me.id_proveedor,
                            'tipo_comprobante': me.tipo_comprobante,
                            'numero_comprobante': me.numero_comprobante,
                            'serie_comprobante': me.serie_comprobante,
                            'impuesto': me.impuesto,
                            'fecha': me.fecha,
                            'total': me.total,
                            'data': me.lista_detalle
                        })
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                type: 'success',
                                title: 'El ingreso se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.listarIngreso(1, 'tipo_comprobante', '');
                            me.verFormDetalleIngresos();
                            me.id_proveedor = 0;
                            me.tipo_comprobante = 'Boleta';
                            me.numero_comprobante = '';
                            me.serie_comprobante = '';
                            me.impuesto = 12.5;
                            me.total = 0.0;
                            me.lista_detalle = [];
                            me.precio = 0;
                            me.cantidad = 0;
                        } )
                        .catch( function(error){
                            if(error.response.status == 409)
                            {
                                me.validaciones = error.response.data
                            }
                        } )
                }
            },
            abrirModalArticulo(){
                this.modal = 1;
                this.tipo_accion = 1;
                this.titulo_modal = 'Agregar articulo';
            },
            cerrarModal() {
                this.modal = 0;
                this.titulo_modal = '';
                this.tipo_accion = 0;
                this.error_ingreso = 0;
                this.criterio_articulo = 'codigo';
                this.txt_buscar_articulo = '';
                this.lista_articulo = [];
            },
            anularIngreso(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: "Anular este ingreso?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put( '/ingreso/baja/id' , {
                            'id': id
                        })
                        .then( function(res){
                            me.listarIngreso(1, 'tipo_comprobante', '');
                            swal({
                                type: 'success',
                                title: 'Ingreso anulado!',
                                showConfirmButton: false,
                                timer: 1300
                            })
                        } )
                        .catch( function(error){;
                            console.log(error);
                        } )
                    } else if (
                        result.dismiss === swal.DismissReason.cancel
                    ) { }
                })
            },

            //Proveedor
            agregarProveedor() {
                let me = this;
                me.data_proveedor.errores = [];
                if(!me.data_proveedor.nombre) {
                    me.data_proveedor.error_proveedor = 1;
                    me.data_proveedor.errores.push('(*) Ingrese el nombre del proveedor');
                }
                else{
                    if(me.data_proveedor.numero_documento == '')
                    {
                        me.data_proveedor.numero_documento = 'CF'
                    }
                    axios.post('/proveedor', {
                            'nombre': me.data_proveedor.nombre,
                            'tipo_documento': me.data_proveedor.tipo_documento,
                            'numero_documento': me.data_proveedor.numero_documento,
                            'direccion': me.data_proveedor.direccion,
                            'telefono': me.data_proveedor.telefono,
                            'email': me.data_proveedor.email,
                            'contacto': me.data_proveedor.contacto,
                            'telefono_contacto': me.data_proveedor.telefono_contacto,
                        })
                        .then( function(res){
                            me.cerrarModalProveedor();
                            swal({
                                type: 'success',
                                title: 'El registro se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        } )
                        .catch( function(error){
                            if(error.response.status == 409)
                            {
                                me.data_proveedor.validaciones = error.response.data
                            }
                        } )
                }
            },
            abrirModal(modelo, titulo, accion, data=[]){
                this.data_proveedor.validaciones = {}
                this.data_proveedor.titulo_modal = titulo;
                switch(modelo){
                    case 'proveedor':
                        switch(accion) {
                            case 'Agregar':
                                this.data_proveedor.modal = 1;
                                this.data_proveedor.tipo_accion = 1;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;
                        }
                    break;
                }
            }, 
            cerrarModalProveedor() {
                this.data_proveedor.modal = 0;
                this.data_proveedor.id_proveedor = 0;
                this.data_proveedor.titulo_modal = '';
                this.data_proveedor.nombre = '';
                this.data_proveedor.tipo_documento = '';
                this.data_proveedor.numero_documento = 'CF';
                this.data_proveedor.direccion = '';
                this.data_proveedor.telefono = '';
                this.data_proveedor.email = '';
                this.data_proveedor.contacto = '';
                this.data_proveedor.telefono_contacto = '';
                this.data_proveedor.tipo_accion = 0;
                this.data_proveedor.error_cliente = 0;
            },                       
        },
        mounted() {
            console.log('Component Ingreso cargado.')
            this.listarIngreso(1, this.criterio, this.txt_buscar);
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
