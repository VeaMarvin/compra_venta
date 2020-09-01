<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Consultar Compras
                </div>
                <div class="card-body">
                    <!--LISTADO-->
                    <template v-if="listado == 1">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select class="form-control col-md-3" v-model="criterio">
                                            <option value="fecha_hora">Fecha</option>
                                        </select>
                                        <input type="date" v-model="txt_buscar" @keyup.enter="listarIngreso(1, criterio, txt_buscar)" class="form-control" placeholder="Texto a buscar">
                                        <button type="submit" @click="listarIngreso(1, criterio, txt_buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                        <button type="button" @click="cargarPDF()" class="btn btn-info">
                                            <i class="icon-doc"></i>&nbsp;Reporte PDF
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Proveedor</th>
                                            <th>Serie | #</th>
                                            <th>Fecha | Hora</th>
                                            <th>Impuesto</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ingreso in lista_ingreso">
                                            <td v-text="ingreso.usuario">  </td>  
                                            <td v-text="ingreso.nombre">  </td>  
                                            <td v-text="ingreso.serie_comprobante + ' | ' + ingreso.numero_comprobante"> </td>  
                                            <td v-text="ingreso.fecha_hora">  </td>  
                                            <td v-text="ingreso.impuesto">  </td>  
                                            <td v-text="ingreso.total">  </td>  
                                            <td v-text="ingreso.estado"></td>
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
                                    <p v-text="impuesto"></p>
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
                                                <td>  {{ detalle.precio * detalle.cantidad }} </td>
                                            </tr>
                                            <tr style="background-color:#EEE;">
                                                <td colspan="3" align="right"> <strong>Subtotal:</strong> </td>
                                                <td>{{ total - impuesto }}</td>
                                            </tr>
                                            <tr style="background-color:#EEEEEE;">
                                                <td colspan="3" align="right"> <strong>Impuestos:</strong> </td>
                                                <td>{{ impuesto }}</td>
                                            </tr>
                                            <tr style="background-color:#EEEEEE;">
                                                <td colspan="3" align="right"> <strong>Total neto:</strong> </td>
                                                <td>  {{ total }} </td>
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
    </main>
    <!-- /Fin del contenido principal -->
</template>

<script>
    import vSelect from 'vue-select';
    export default {
        data() {
            return {
                proveedor: '',
                txt_buscar_articulo: '',
                criterio_articulo: 'nombre',
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
                impuesto: 0.1,
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
                paginacion: {
                    'total': 0,
                    'pagina_actual': 0,
                    'por_pagina': 0,
                    'ultima_pagina': 0,
                    'desde': 0,
                    'hasta': 0
                },
                offset: 3,
                criterio: 'fecha_hora',
                txt_buscar: '',
                lista_proveedores: [],
                lista_articulo: [],
                codigo: '',
                articulo: '',
                precio: 0,
                cantidad: 0,
                flag_detalle: 0,
                subtotal : 0.0
            }
        },
        components: {
            vSelect
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
                    resultado += this.lista_detalle[i].cantidad * this.lista_detalle[i].precio;
                return resultado;
            }
        },
        methods: {
            cargarPDF(){
                window.open('/ingreso/listar/pdf?criterio=' + this.criterio + '&buscar=' + this.txt_buscar, '_blank' );
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
                me.impuesto = 0.1;
                me.total = 0.0;
                me.lista_detalle = [];
                me.precio = 0;
                me.cantidad = 0;
            },
            cerrarListadoDetalle(){
                this.flag_detalle = 0;
                this.listado = 1;
                
            },
            verFormDetalleIngresos(){
                this.listado = this.listado == 1 ? 0 : 1;
                this.id_proveedor = 0;
                this.lista_detalle = [];
                this.impuesto = 0.1;
                this.tipo_comprobante = 'Boleta';
                this.serie_comprobante = '';
                this.numero_comprobante = '';
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
