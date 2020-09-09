<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Ventas
                </div>
                <div class="card-body">
                    <!--LISTADO-->
                    <template v-if="listado == 1">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select class="form-control col-md-3" v-model="criterio">
                                            <option value="numero_comprobante">No. Factura</option>
                                        </select>
                                        <input type="text" v-model="txt_buscar" @keyup.enter="listarVenta(1, criterio, txt_buscar)" class="form-control" placeholder="Texto a buscar">
                                        <button type="submit" @click="listarVenta(1, criterio, txt_buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                        &nbsp;&nbsp;
                                        <button type="button" class="btn btn-success" @click="listarVenta(1,'','')"><i class="fa fa-refresh"></i> Recargar</button>
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
                                            <th>Cliente</th>
                                            <th>Serie / Factura</th>
                                            <th>Fecha</th>
                                            <th>Descuento</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="venta in lista_venta">
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm" @click="mostrarDetalle(venta.id)">
                                                    <i class="icon-eye"></i>
                                                </button> &nbsp;
                                                <button type="button" class="btn btn-info btn-sm" @click="getPDF(venta.id, venta.tipo_comprobante)">
                                                    <i class="icon-doc"></i>
                                                </button> 
                                                <template v-if="venta.estado == 'FACTURADO' && puede_anular(venta.created_at) == false">
                                                    <button type="button" class="btn btn-danger btn-sm" @click="anularVenta(venta.id)">
                                                        <i class="icon-trash"></i>
                                                    </button>
                                                </template> &nbsp;
                                            </td>
                                            <td v-text="venta.usuario">  </td>  
                                            <td v-text="venta.nombre">  </td>  
                                            <td v-text="venta.serie_comprobante + ' - ' + venta.numero_comprobante"> </td>  
                                            <td v-text="venta.fecha_hora">  </td>  
                                            <td v-if="venta.descuento > 0" v-text="venta.descuento" align="right">  </td>
                                            <td v-else  v-text=" ">  </td>
                                            <td v-text="venta.total" align="right">  </td>
                                            <td v-if="venta.estado == 'FACTURADO'" align="center">
                                                <span class="badge badge-success">Facturado</span>
                                            </td>
                                            <td v-else align="center">
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
                            <div class="alert alert-danger" v-if="Object.keys(validaciones).length !== 0">
                                <template v-for="(value, index) in validaciones">
                                    <strong v-bind:key="value[0]">ERROR EN {{ index.toUpperCase() }}:</strong> {{ value[0] }}.
                                    <br>
                                </template>
                            </div>                            
                            <div class="row border">
                                <div class="col-md-10" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-group">
                                        <b><label for="">Cliente</label></b>
                                        <v-select 
                                            :on-search="getClientes"
                                            label="nombre"
                                            :options="lista_clientes"
                                            placeholder="Buscando clientes..."
                                            :on-change="getDatosCliente">

                                        </v-select>
                                    </div>
                                </div>                               
                                <div class="col-md-2" style="margin-top:1%;margin-bottom:1%;">                                    
                                    <div class="form-group">
                                        &nbsp; 
                                        &nbsp;                                          
                                        &nbsp; 
                                        &nbsp;                                          
                                        <input value="Mostrar Artículos" type="button" @click="abrirModal()" class="btn btn-primary" >
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
                                                <th>Descuento</th>
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
                                                <td align="right">  {{ detalle.precio }}  </td>
                                                <td align="right">  - {{ detalle.descuento }}  </td>
                                                <td align="center">
                                                    <span style="color:red;">Stock: {{detalle.stock-detalle.cantidad}} </span> 
                                                    <input type="number" v-model="detalle.cantidad" step="any" class="form-control" value="0"> 
                                                </td>
                                                <td align="right">  {{ detalle.precio * detalle.cantidad - detalle.descuento }} </td>
                                            </tr>
                                            <tr style="background-color:#EEEEEE;">
                                                <td colspan="4" align="right"> <strong>Total neto:</strong> </td>
                                                <td align="right">Q {{ total = calcularTotal }} </td>
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
                                    <input type="button" class="btn btn-secondary" value="Cerrar" @click="verFormDetalleVentas()">
                                    <input type="button" @click="agregarVenta()" class="btn btn-primary" value="Registrar">
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
                                        <b><label for="">Cliente</label></b>
                                        <p v-text="cliente"></p>
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-top:2%;margin-bottom:2%;">
                                    <b><label for="">Impuesto</label></b>
                                    <p v-text="impuesto">%</p>
                                </div>
                                <div class="col-md-4" style="margin-bottom:2%;">
                                    <div class="form-control">
                                        <b><label for="">Tipo de comprobante</label></b>
                                        <p v-text="tipo_comprobante"></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-control">
                                        <b><label for="">Serie</label></b>
                                        <p v-text="serie_comprobante"></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-control">
                                        <b><label for="">Número</label></b>
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
                                                <th>Descuento</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="lista_detalle.length >= 1">
                                            <tr v-for="detalle in lista_detalle">
                                                <td v-text="detalle.nombre"> </td>
                                                <td align="right" v-text="detalle.precio"> </td>
                                                <td align="right"> - {{ detalle.descuento }} </td>
                                                <td align="center" v-text="detalle.cantidad"> </td>
                                                <td align="right"> {{ Number(((detalle.precio - detalle.descuento) * detalle.cantidad).toFixed(2)) }} </td>
                                            </tr>
                                            <tr v-if="descuento > 0" style="background-color:#EEEEEE;">
                                                <td colspan="3" align="right"> <strong>Descuento:</strong> </td>
                                                <td align="right">- {{ descuento }} </td>
                                            </tr>
                                            <tr style="background-color:#EEEEEE;">
                                                <td colspan="4" align="right"> <strong>Total neto:</strong> </td>
                                                <td align="right">Q  {{ total }} </td>
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
                                    <input type="text" v-model="txt_buscar_articulo" @keyup.enter="getArticulos(criterio_articulo, txt_buscar_articulo)" class="form-control" placeholder="Texto a buscar">
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
                                        <th>Categoria</th>
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
                                        <td v-text="articulo.categoria">  </td>  
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
    </main>
    <!-- /Fin del contenido principal -->
</template>

<script>
    import moment from 'moment'
    export default {
        data() {
            return {
                descuento: 0.0,
                id_venta: 0,
                id_cliente: 0,
                cliente: '',
                tipo_comprobante: 'Boleta',
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
                cantidad: 0,
                flag_detalle: 0,
                subtotal : 0.0,
                stock: 0
            }
        },
        components: {
            moment
        },
        computed: {
            calcularSubtotal: function() {

            },
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
                    resultado += this.lista_detalle[i].cantidad * (this.lista_detalle[i].precio - this.lista_detalle[i].descuento);
                return resultado;
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
                var url_cabecera = '/venta/get/cabecera?&id=' + id;
                axios.get( url_cabecera )
                    .then( function(res){
                        ingreso_cabecera = res.data.cabecera;
                        me.cliente = ingreso_cabecera[0].nombre;
                        me.tipo_comprobante = ingreso_cabecera[0].tipo_comprobante;
                        me.serie_comprobante = ingreso_cabecera[0].serie_comprobante;
                        me.numero_comprobante = ingreso_cabecera[0].numero_comprobante;
                        me.impuesto = ingreso_cabecera[0].impuesto;
                        me.descuento = ingreso_cabecera[0].descuento;
                        me.total = ingreso_cabecera[0].total;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
                var url_detalles = '/venta/get/detalles?&id=' + id;
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
                me.impuesto = 0.1;
                me.descuento = 0.0;
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
                        precio: data.precio_venta,
                        descuento: data.descuento,
                        stock: data.stock,
                    });

                    console.log(me.lista_detalle)
                    //me.lista_articulo = [];
                }
            
            },
            getArticulos(criterio, buscar) {
                let me = this;
                var url = '/articulos/stock/venta?&criterio=' + criterio + '&buscar=' + buscar;
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
            buscarArticulo(){
                let me = this;
                axios.get( '/articulos/stock?filtro=' + me.id_articulo )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_articulo = respuesta.articulos;
                        if(me.lista_articulo.length > 0){
                            me.articulo = me.lista_articulo[0].nombre;
                            me.id_articulo = me.lista_articulo[0].id;
                            me.precio = me.lista_articulo[0].precio_venta;
                            me.stock = me.lista_articulo[0].stock;
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
            getClientes(search, loading) {
                let me = this;
                loading( true );
                var url = '/clientes?filtro=' + search;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        q: search
                        me.lista_clientes = respuesta.clientes;
                        loading( false );
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            getDatosCliente(valor) {
                let me = this;
                me.loading = true;
                me.id_cliente = valor.id;
            },
            verFormDetalleVentas(){
                this.validaciones = []
                this.listado = this.listado == 1 ? 0 : 1;
                this.id_cliente = 0;
                this.lista_detalle = [];
                this.impuesto = 0.1;
                this.tipo_comprobante = 'Boleta';
                this.serie_comprobante = '';
                this.numero_comprobante = '';
            },
            cambiarPagina(pagina, criterio, txt_buscar) {
                let me = this;
                this.paginacion.pagina_actual = pagina;
                me.listarVenta(pagina, criterio, txt_buscar);
            },
            listarVenta(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/venta?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_venta = respuesta.ventas.data;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            agregarVenta() {
                let me = this;
                me.errores = [];
                me.error_venta = 0;
                if(me.id_cliente == 0) {
                    me.error_venta = 1;
                    me.errores.push('(*) Ingrese el cliente'); 
                } if(me.lista_detalle.length <= 0) {
                    me.error_venta = 1;
                    me.errores.push('(*) Ingresa articulos a la venta');
                }
                
                me.lista_detalle.map( function(x) {
                    if(Number(x.cantidad) > Number(x.stock) ) {
                        me.error_venta = 1;
                        me.errores.push('(*) Verifica stock de: ' + x.articulo);
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
                    axios.post('/venta', {
                            'id_cliente': me.id_cliente,
                            'tipo_comprobante': me.tipo_comprobante,
                            'numero_comprobante': me.numero_comprobante,
                            'serie_comprobante': me.serie_comprobante,
                            'impuesto': me.impuesto,
                            'descuento': me.descuento,
                            'total': me.total,
                            'data': me.lista_detalle
                        })
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                type: 'success',
                                title: 'La venta se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.listarVenta(1, 'tipo_comprobante', '');
                            me.verFormDetalleVentas();
                            me.id_cliente = 0;
                            me.tipo_comprobante = 'Boleta';
                            me.numero_comprobante = '';
                            me.serie_comprobante = '';
                            me.impuesto = 0.1;
                            me.total = 0.0;
                            me.lista_detalle = [];
                            me.precio = 0;
                            me.cantidad = 0;
                            me.stock = 0;
                            me.codigo = 0;
                            me.descuento = 0;
                            
                            window.open( '/venta/pdf/' + res.data.data.id);
                        } )
                        .catch( function(error){
                            if(error.response.status == 409)
                            {
                                me.validaciones = error.response.data
                            }
                        } )
                }
            },
            agregarDetalleVenta(){
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
                        if(me.cantidad > me.stock){
                            swal({
                                type: 'error',
                                title: 'Error',
                                text: 'No hay stock suficiente! Stock: ' + me.stock
                            }); 
                        }
                        else{
                            me.lista_detalle.push({
                                id_articulo: me.id_articulo,
                                articulo: me.articulo,
                                cantidad: me.cantidad,
                                precio: me.precio,
                                descuento: me.descuento,
                                stock: me.stock
                            });
                            me.articulo = '';
                            me.id_articulo = 0;
                            me.cantidad = 0;
                            me.precio = 0;
                            me.descuento = 0;
                            me.stock = 0;
                            me.lista_articulo = [];
                        }
                    }
                }
            },
            abrirModal(){
                this.modal = 1;
                this.tipo_accion = 1;
                this.titulo_modal = 'Agregar articulo';
                this.lista_articulo = [];
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
            anularVenta(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: "Anular esta venta?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put( '/venta/baja/id' , {
                            'id': id
                        })
                        .then( function(res){
                            me.listarVenta(1, 'tipo_comprobante', '');
                            swal({
                                type: 'success',
                                title: 'Venta anulada!',
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
            getPDF(id, tipo_comprobante){
                console.log(tipo_comprobante);
                switch(tipo_comprobante){
                    case 'FACTURA': window.open( '/venta/factura/pdf/' + id , '_blank' ); break;
                    case 'RECIBO': window.open( '/venta/recibo/pdf/' + id , '_blank' ); break;
                    default: break;
                }
                
            }
        },
        mounted() {
            console.log('Component Venta cargado.')
            this.listarVenta(1, this.criterio, this.txt_buscar);
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
