<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Facturas
                    <button type="button" class="btn btn-success" @click="abrirModal('factura', 'Agregar factura', 'Agregar')">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                    <option value="nombre">Nombre</option>
                                    <option value="descripcion">Descripción</option>
                                </select>
                                <input type="text" v-model="txt_buscar" @keyup.enter="listarCategoria(1, criterio, txt_buscar)" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listarFactura(1, criterio, txt_buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                &nbsp;&nbsp;
                                <button type="button" class="btn btn-success" @click="listarFactura()"><i class="fa fa-refresh"></i> Recargar</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Numero factura</th>
                                <th>Id</th>
                                <th>Receptor</th>
                                <th>Folio</th>
                                <th>Monto</th>
                                <th>Serie</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in lista">
                                <td v-text="item.NumeroFactura">  </td>  
                                <td v-text="item.IdFacturaDoc">  </td>  
                                <td v-text="item.EmailReceptor">  </td>  
                                <td v-text="item.Folio">  </td>  
                                <td v-text="item.Monto">  </td>  
                                <td v-text="item.Serie">  </td>  
                            </tr>

                        </tbody>
                    </table>
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
            </div>
            <!-- Fin ejemplo de tabla Listado -->

            <!--MODAL-->
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">
                Launch demo modal
            </button>

        <!-- Modal -->
            <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            ...
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        <!--MODAL-->
        </div>
    </main>
    <!-- /Fin del contenido principal -->
</template>

<script>
    export default {
        data() {
            return {
                id_categoria: 0,
                nombre: '',
                descripcion: '',
                lista_categoria: [],
                modal: 0,
                titulo_modal: '',
                tipo_accion: 0,
                error_categoria: 0,
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
                criterio: 'nombre',
                txt_buscar: '',
                lista: []
            }
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
            }
        },
        methods: {
            cambiarPagina(pagina, criterio, txt_buscar) {
                let me = this;
                this.paginacion.pagina_actual = pagina;
                me.listarCategoria(pagina, criterio, txt_buscar);
            },
            listarFactura() {
                axios.get('https://a.4cdn.org/a/threads.json', {
                        headers: {
                            'Access-Control-Allow-Origin': '*',
                        },
                        proxy: {
                            host: '104.236.174.88',
                            port: 3128
                        }
                    }).then(function (response) {
                        console.log('GOOOOOOD')
                        console.log('response is : ' + response.data);
                    }).catch(function (error) {
                        if (error.response) {
                            console.log('Error response headers')
                            console.log(error.response.headers);
                        } 
                        else if (error.request) {
                            console.log('Error request')
                            console.log(error.request);
                        } 
                        else {
                            console.log('Error message')
                            console.log(error.message);
                        }
                        console.log(':::::::::::::::::::::')
                            console.log(error.config);
                        console.log(':::::::::::::::::::::')
                });
                return;
                let me = this;
                var config = {
                    headers: {'Access-Control-Allow-Origin': '*'}
                };
                var token = "mytokenasjdfkjsadlkfjsadlkfjslakdjf";
                var url = 'https://amsb2bsite.herokuapp.com/facturadocumento';
                axios.get( url, config) 
                    .then( function(res){
                        console.log('GOOOOOD ');
                        console.log(res)
                        /*var respuesta = res.data;
                        me.lista_categoria = respuesta.categorias.data;
                        me.paginacion = respuesta.paginacion;*/
                    } )
                    .catch( function(error){
                        console.log('::::::::::::Error::::::::::::')
                        console.log(error);
                        console.log(error.status);
                    } )
            },
        },
        mounted() {
            console.log('Component Factura cargado.')

            url = 'https://amsb2bsite.herokuapp.com/FacturaDocumento';
            var axiosCrossDomain = axios;
            delete axiosCrossDomain.defaults.headers.common["X-CSRF-TOKEN"];
            var me = this;
            axios.get(url)
                .then( function(res){
                    me.lista = res.data;
                    console.log(res.data)
                } )
                .catch( function(error){
                    console.log('::::::::::::Error::::::::::::')
                    console.log(error);
                    console.log(error.status);
                } )
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
</style>
