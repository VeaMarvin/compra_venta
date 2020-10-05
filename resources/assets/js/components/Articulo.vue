<template>
    <!-- Contenido Principal -->
    <main class="main">
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Articulos
                    <button type="button" @click="abrirModal('articulo', 'Agregar articulo', 'Agregar')" class="btn btn-success">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                    <button type="button" @click="cargarPDF()" class="btn btn-info">
                        <i class="icon-doc"></i>&nbsp;Reporte del Inventario actual
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                    <option value="codigo">Código</option>
                                    <option value="nombre">Nombre</option>
                                    <option value="descripcion">Descripción</option>
                                </select>
                                <input type="text" v-model="txt_buscar" @keyup.enter="listarArticulo(1, criterio, txt_buscar)" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listarArticulo(1, criterio, txt_buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                &nbsp;&nbsp;
                                <button type="button" class="btn btn-success" @click="listarArticulo(1,'','')"><i class="fa fa-refresh"></i> Recargar</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Categoria</th>
                                <th>Precio de venta</th>
                                <th>Stock</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="articulo in lista_articulo">
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" @click="abrirModal('articulo', 'Actualizar Articulo', 'Actualizar', articulo)">
                                        <i class="icon-pencil"></i>
                                    </button> &nbsp;
                                    <template v-if="articulo.estado">
                                        <button type="button" class="btn btn-danger btn-sm" @click="desactivarArticulo(articulo.id)">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button type="button" class="btn btn-success btn-sm" @click="activarArticulo(articulo.id)">
                                            <i class="icon-check"></i>
                                        </button>
                                    </template>
                                </td>
                                <td v-text="articulo.codigo">  </td>
                                <td v-text="articulo.nombre">  </td>  
                                <td v-text="articulo.nombre_categoria">  </td>  
                                <td v-text="articulo.precio_venta">  </td>  
                                <td v-text="articulo.stock">  </td>  
                                <td v-text="articulo.descripcion">  </td>  
                                <td v-if="articulo.estado == 1">
                                    <span class="badge badge-success">Alta</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-warning">Baja</span>
                                </td>
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
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar':modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none; overflow-y: scroll;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content" style="position: absolute; width: 100%">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titulo_modal">  </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModal()">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" v-if="Object.keys(validaciones).length !== 0">
                            <template v-for="(value, index) in validaciones">
                                <strong v-bind:key="value[0]">ERROR EN {{ index.toUpperCase() }}:</strong> {{ value[0] }}.
                                <br>
                            </template>
                        </div>

                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Categoria</label>
                                <div class="col-md-9">
                                    <select class="form-control" v-model="id_categoria">
                                        <option value="0" disabled>Selecciona categoria</option>
                                        <option v-for="categoria in lista_categoria" v-text="categoria.nombre" :value="categoria.id"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="nombre" @input="nombre = $event.target.value.toUpperCase()" class="form-control" placeholder="Nombre de articulo">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Precio de venta</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="precio_venta" @input="precio_venta = $event.target.value.toUpperCase()" class="form-control" required min="0" value="0" step="any" placeholder="Precio de venta">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Stock</label>
                                <div class="col-md-9">
                                    <input type="number" v-model="stock" @input="stock = $event.target.value.toUpperCase()" class="form-control" placeholder="Stock">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Descripción</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="descripcion" @input="descripcion = $event.target.value.toUpperCase()" class="form-control" placeholder="Descripcion">
                                </div>
                            </div>
                            <div v-show="error_articulo" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-if="error_articulo==1" v-for="error in errores">
                                        <span class="help-block error" v-text="error"></span>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==1" @click="agregarArticulo()">Agregar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==2" @click="actualizarArticulo()">Actualizar</button>
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
    import VueBarcode from 'vue-barcode';
    export default {
        data() {
            return {
                validaciones: {},
                id_articulo: 0,
                id_categoria: 0,
                nombre_categoria: '',
                codigo: '',
                nombre: '',
                precio_venta: 0,
                stock: 0,
                descripcion: '',
                lista_articulo: [],
                modal: 0,
                titulo_modal: '',
                tipo_accion: 0,
                error_articulo: 0,
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
                criterio: 'codigo',
                txt_buscar: '',
                lista_categoria: []
            }
        },
        components: {
            'barcode': VueBarcode
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
            cargarPDF(){
                window.open('/articulos/listar/pdf', '_blank' );
            },
            cambiarPagina(pagina, criterio, txt_buscar) {
                let me = this;
                this.paginacion.pagina_actual = pagina;
                me.listarArticulo(pagina, criterio, txt_buscar);
            },
            listarArticulo(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/articulo?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_articulo = respuesta.articulos.data;
                        me.paginacion = respuesta.paginacion;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            agregarArticulo() {
                let me = this;
                me.errores = [];
                me.error_articulo = 0;
                if(me.id_categoria == 0) {
                    me.error_articulo = 1;
                    me.errores.push('(*) Selecciona categoria del articulo');
                }
                if(!me.nombre) {
                    me.error_articulo = 1;
                    me.errores.push('(*) Ingrese el nombre del articulo');
                } if(!me.precio_venta) {
                    me.error_articulo = 1;
                    me.errores.push('(*) Ingrese el precio de venta');
                } if(!me.stock) {
                    me.error_articulo = 1;
                    me.errores.push('(*) Ingrese el stock del articulo');
                }
                if(me.error_articulo == 1) return;
                else{
                    axios.post('/articulo', {
                            'id_categoria': me.id_categoria,
                            'codigo': me.codigo,
                            'nombre': me.nombre,
                            'precio_venta': me.precio_venta,
                            'stock': me.stock,
                            'descripcion': me.descripcion,
                        })
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                type: 'success',
                                title: 'El registro se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.listarArticulo(1, 'nombre', '');
                        } )
                        .catch( function(error){
                            if(error.response.status == 409)
                            {
                                me.validaciones = error.response.data
                            }
                        } )
                }
            },
            abrirModal(modelo, titulo, accion, data=[]){
                this.validaciones = {}
                this.titulo_modal = titulo;
                switch(modelo){
                    case 'categoria':
                        switch(accion) {
                            case 'Agregar':
                                this.modal = 1;
                                this.tipo_accion = 1;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;
                            case 'Actualizar': 
                                this.modal = 1;
                                this.tipo_accion = 2;
                                this.nombre = data.nombre;
                                this.descripcion = data.descripcion;
                                this.id_categoria = data.id;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;    
                        }
                    break;
                    case 'articulo':
                        switch(accion) {
                            case 'Agregar':
                                this.modal = 1;
                                this.tipo_accion = 1;
                                this.getListaCategorias();
                                this.id_categoria = 0;
                                this.codigo = '';
                                this.nombre = '';
                                this.precio_venta = 0;
                                this.stock = 0;
                                this.descripcion = '';
                            break;
                            case 'Actualizar': 
                                this.modal = 1;
                                this.tipo_accion = 2;
                                this.getListaCategorias();
                                this.id_articulo = data.id,
                                this.id_categoria = data.id_categoria,
                                this.codigo = data.codigo,
                                this.nombre = data.nombre,
                                this.precio_venta = data.precio_venta,
                                this.stock = data.stock,
                                this.descripcion = data.descripcion;
                            break;    
                        }
                    break;

                }
            },
            cerrarModal() {
                this.modal = 0;
                this.titulo_modal = '';
                this.id_categoria = 0;
                this.codigo = '';
                this.nombre = '';
                this.descripcion = '';
                this.precio_venta = '';
                this.stock = '';
                this.tipo_accion = 0;
                this.error_categoria = 0;
                this.errores = [];
                this.error_articulo = 0;
                
            },
            actualizarArticulo() {
                let me = this;
                me.errores = [];
                me.error_articulo = 0;
                if(me.id_categoria == 0) {
                    me.error_articulo = 1;
                    me.errores.push('(*) Selecciona categoria del articulo');
                }
                if(!me.nombre) {
                    me.error_articulo = 1;
                    me.errores.push('(*) Ingrese el nombre del articulo');
                } if(!me.precio_venta) {
                    me.error_articulo = 1;
                    me.errores.push('(*) Ingrese el precio de venta');
                } if(!me.stock) {
                    me.error_articulo = 1;
                    me.errores.push('(*) Ingrese el stock del articulo');
                }
                if(me.error_articulo == 1) return;
                else{
                    axios.put('/articulo/' + me.id_articulo, {
                        'id': me.id_articulo,
                        'id_categoria': me.id_categoria,
                        'codigo': me.codigo,
                        'nombre': me.nombre,
                        'stock': me.stock,
                        'precio_venta': me.precio_venta,
                        'descripcion': me.descripcion
                    })
                    .then( function(res){
                        me.cerrarModal();
                        swal({
                            title: "Información",
                            text: "El registro se ha actualizado!",
                            icon: "success",
                            button: "Aceptar",
                        });
                        me.listarArticulo(1, 'nombre', '');
                    } )
                    .catch( function(error){
                        if(error.response.status == 409)
                        {
                            me.validaciones = error.response.data
                        }
                    } )
                }
            },
            desactivarArticulo(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: 'Dar de baja este articulo?',
                    //text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put('/articulo/baja/id', {
                            'id': id
                        })
                        .then( function(res){
                            me.listarArticulo(1, 'nombre', '');
                            swal({
                                type: 'success',
                                title: 'Registro dado de baja!',
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
            activarArticulo(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: 'Dar de alta este articulo?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put('/articulo/alta/id', {
                            'id': id
                        })
                        .then( function(res){
                            me.listarArticulo(1, 'nombre', '');
                            swal({
                                type: 'success',
                                title: 'Registro dado de alta!',
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
            getListaCategorias() {
                let me = this;
                var url = '/categorias';
                axios.get( url )
                    .then( function(res){
                        me.lista_categoria = res.data.categorias;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            }
        },
        mounted() {
            console.log('Component Articulo cargado.')
            this.listarArticulo(1, this.criterio, this.txt_buscar);
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
</style>
