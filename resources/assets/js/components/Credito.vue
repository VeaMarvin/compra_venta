<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Créditos
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
                                            <th>Saldo</th>
                                            <th>Total</th>
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
                                            <td v-text="venta.saldo" align="right">  </td>
                                            <td v-text="venta.total" align="right">  </td>
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
                    <template v-if="flag_detalle == 1">
                        <div class="card-body">
                            <div class="form-group row border">
                                <div class="col-md-6" style="margin-top:1%;margin-bottom:1%;">
                                    <div class="form-control">
                                        <b><label for="">Cliente</label></b>
                                        <p v-text="cliente"></p>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:2%;">
                                    <div class="form-control">
                                        <b><label for="">Saldo</label></b>
                                        <p style="font-size: 30px; color:red;">Q. {{saldo.toFixed(2)}}</p>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:2%;">
                                    <div class="form-control">
                                        <b><label for="">Total</label></b>
                                        <p style="font-size: 30px;">Q. {{total}}</p>
                                    </div>
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
                            
                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="input-group col-md-4">
                                    <input type="number" v-model="abono" class="form-control" placeholder="Abonar Q." aria-label="Abono" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <input type="button" @click="abonar()" class="btn btn-primary" value="ABONAR">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row border">
                                <div class="table-responsive col-md-12">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Opciones</th>
                                                <th>No.</th>
                                                <th>Fecha</th>
                                                <th>Abono</th>
                                                <th>Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="lista_abonos.length >= 1">
                                            <tr v-for="(abono, indice ) in lista_abonos">
                                                <td>
                                                    <button type="button" class="btn btn-info btn-sm" @click="getPDF(abono.id)">
                                                        <i class="icon-doc"></i>
                                                    </button> 
                                                    <template v-if="(puede_anular(abono.created_at) == false) && (abono.abono > 0)">
                                                        <button type="button" class="btn btn-danger btn-sm" @click="anularAbono(abono.id)">
                                                            <i class="icon-trash"></i>
                                                        </button>
                                                    </template> &nbsp;
                                                </td>
                                                <td align="center"> {{ indice+1 }} </td>
                                                <td align="center"> {{ abono.fecha_hora }} </td>
                                                <td v-if="abono.abono > 0" align="right"> {{ abono.abono }} </td>
                                                <td v-else align="center">
                                                    <span class="badge badge-warning">Anulado</span>
                                                </td>
                                                <td align="right"> {{ (abono.saldo).toFixed(2) }} </td>
                                            </tr>
                                            <tr style="background-color:#EEEEEE;">
                                                <td colspan="4" align="right"> <strong>Total venta:</strong> </td>
                                                <td align="right">Q {{ total }} </td>
                                            </tr>
                                            
                                        </tbody>
                                        <tbody v-else>
                                            <tr>
                                                <td colspan="4"> No hay articulos agregados </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="input-group col-md-4">
                                    <input type="number" v-model="abono" class="form-control" placeholder="Abonar Q." aria-label="Abono" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <input type="button" @click="abonar()" class="btn btn-primary" value="ABONAR">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <input type="button" class="btn btn-secondary" value="Cerrar" @click="cerrarListadoDetalle()">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
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
                saldo: 0.0,
                total: 0.0,
                total_impuesto: 0.0,
                total_parcial: 0.0,
                lista_venta: [],
                lista_clientes: [],
                lista_detalle: [],
                lista_abonos: [],
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
                stock: 0,

                abono: 0,
            }
        },
        components: {
            moment
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
        },
        methods: {
            abonar(){
                let me = this;
                if(!(parseFloat(me.abono)<=0 || parseFloat(me.abono)>me.saldo)){
                    axios.post('/credito',{
                        'id_venta': me.id_venta,
                        'abono': me.abono
                    })
                    .then(function(){
                        var url_abonos = '/credito/get/abonos?&id=' + me.id_venta;
                        axios.get(url_abonos)
                            .then(function(res){
                                me.lista_abonos = res.data.abonos;
                                me.calcularSaldos();
                            })
                            .catch(function(error){
                                console.log(error);
                            })
                        
                        me.abono = 0;
                        swal({
                            type: 'success',
                            title: 'El abono se ha guardado!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
                    .catch(function(error){
                        console.log(error);
                    });
                }
                else{
                    swal({
                        type: 'error',
                        title: 'Error',
                        text: 'No es posible abonoar Q.' + parseFloat(me.abono).toFixed(2)
                    }); 
                }
                
            },
            calcularSaldos(){
                var registros = this.lista_abonos.length;
                var total = this.total;
                for(var i=0; i<this.lista_abonos.length; i++){
                    if(i==0) this.lista_abonos[i].saldo = total - this.lista_abonos[i].abono;
                    else{
                        this.lista_abonos[i].saldo = this.lista_abonos[i-1].saldo - this.lista_abonos[i].abono;
                    }
                }
                this.saldo = this.lista_abonos[registros-1].saldo;
                this.listarVenta(1, this.criterio, this.txt_buscar);
            },
            puede_anular(fecha){
                if(moment(new Date()).format('YYYY-MM-DD') == moment(new Date(fecha)).format('YYYY-MM-DD'))
                    return false
                else
                    return true
            },
            cerrarListadoDetalle(){
                this.mostrarDetalle(this.id_venta);
                this.flag_detalle = 0;
                this.listado = 1;
            },
            mostrarDetalle(id) {
                let me = this;
                me.flag_detalle = 1;
                me.listado = 2;
                var ingreso_cabecera = [];
                var abonos = [];
                me.lista_detalle  = [];
                me.lista_abonos = [];
                
                var url_cabecera = '/credito/get/cabecera?&id=' + id;
                axios.get( url_cabecera )
                    .then( function(res){
                        ingreso_cabecera = res.data.cabecera;
                        me.id_venta = ingreso_cabecera[0].id;
                        me.cliente = ingreso_cabecera[0].nombre;
                        me.tipo_comprobante = ingreso_cabecera[0].tipo_comprobante;
                        me.serie_comprobante = ingreso_cabecera[0].serie_comprobante;
                        me.numero_comprobante = ingreso_cabecera[0].numero_comprobante;
                        me.impuesto = ingreso_cabecera[0].impuesto;
                        me.descuento = ingreso_cabecera[0].descuento;
                        me.total = ingreso_cabecera[0].total;
                        me.saldo = ingreso_cabecera[0].saldo;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )

                var url_abonos = '/credito/get/abonos?&id=' + id;
                axios.get(url_abonos)
                    .then(function(res){
                        me.lista_abonos = res.data.abonos;
                        me.calcularSaldos();
                    })
                    .catch(function(error){
                        console.log(error);
                    })
                
    
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
            verificarArticuloEnDetalle(id) {
                for (var i=0; i<this.lista_detalle.length; i++) {
                    if(id == this.lista_detalle[i].id_articulo)
                        return true;
                }
                return false;
            },
            cambiarPagina(pagina, criterio, txt_buscar) {
                let me = this;
                this.paginacion.pagina_actual = pagina;
                me.listarVenta(pagina, criterio, txt_buscar);
            },
            listarVenta(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/credito?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_venta = respuesta.ventas.data;
                    })
                    .catch( function(error){
                        console.log(error);
                    })
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
            anularAbono(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: "Anular abono?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put( '/credito/'+id , {
                            'id': id,
                            'id_venta': me.id_venta
                        })
                        .then( function(res){
                            var url_abonos = '/credito/get/abonos?&id=' + me.id_venta;
                            axios.get(url_abonos)
                                .then(function(res){
                                    me.lista_abonos = res.data.abonos;
                                    me.calcularSaldos();
                                })
                                .catch(function(error){
                                    console.log(error);
                                });
                            
                            swal({
                                type: 'success',
                                title: 'Abono anulada!',
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
                console.log(id);
                window.open( '/credito/resumen/pdf/' + id , '_blank' );
                
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
