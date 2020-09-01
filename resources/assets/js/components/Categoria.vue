<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Categorías
                    <button type="button" class="btn btn-success" @click="abrirModal('categoria', 'Agregar categoria', 'Agregar')">
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
                                <button type="submit" @click="listarCategoria(1, criterio, txt_buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                &nbsp;&nbsp;
                                <button type="button" class="btn btn-success" @click="listarCategoria(1,'','')"><i class="fa fa-refresh"></i> Recargar</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="categoria in lista_categoria">
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" @click="abrirModal('categoria', 'Actualizar categoria', 'Actualizar', categoria)">
                                        <i class="icon-pencil"></i>
                                    </button> &nbsp;
                                    <!--<button type="button" class="btn btn-danger btn-sm" @click="desactivarCategoria()">
                                        <i class="icon-trash"></i>
                                    </button>-->
                                    <template v-if="categoria.estado">
                                        <button type="button" class="btn btn-danger btn-sm" @click="desactivarCategoria(categoria.id)">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button type="button" class="btn btn-success btn-sm" @click="activarCategoria(categoria.id)">
                                            <i class="icon-check"></i>
                                        </button>
                                    </template>
                                </td>
                                <td v-text="categoria.nombre">  </td>  
                                <td v-text="categoria.descripcion">  </td>  
                                <td v-if="categoria.estado == 1">
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
                                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="nombre" id="nombre" name="nombre" class="form-control" @input="nombre = $event.target.value.toUpperCase()" placeholder="Nombre de categoría">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Descripción</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="descripcion" id="descripcion" name="descripcion" @input="descripcion = $event.target.value.toUpperCase()"  class="form-control" placeholder="Descripcion">
                                </div>
                            </div>
                            <div v-show="error_categoria" class="form-group row" style="display:flex; justify-content: center;">
                                
                                <div v-if="error_categoria==1" v-for="error in errores">
                                    <span class="help-block error" v-text="error"></span>
                                    <br>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==1" @click="agregarCategoria()">Agregar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==2" @click="actualizarCategoria()">Actualizar</button>
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
    export default {
        data() {
            return {
                validaciones: {},
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
                txt_buscar: ''
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
            listarCategoria(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/categoria?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_categoria = respuesta.categorias.data;
                        me.paginacion = respuesta.paginacion;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            agregarCategoria() {
                let me = this;
                me.errores = [];
                me.validaciones = {};
                if(!me.nombre) {
                    me.error_categoria = 1;
                    me.errores.push('(*) Ingrese el nombre de la categoría');
                }
                else{
                    axios.post('/categoria', {
                            'nombre': me.nombre,
                            'descripcion': me.descripcion
                        })
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                type: 'success',
                                title: 'El registro se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.listarCategoria(1, 'nombre', '');
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

                }
            },
            cerrarModal() {
                this.modal = 0;
                this.titulo_modal = '';
                this.nombre = '';
                this.descripcion = '';
                this.tipo_accion = 0;
                this.error_categoria = 0;
            },
            actualizarCategoria() {
                let me = this;
                axios.put('/categoria/' + me.id_categoria, {
                        'id': me.id_categoria,
                        'nombre': me.nombre,
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
                        me.listarCategoria(1, 'nombre', '');
                    } )
                    .catch( function(error){
                        if(error.response.status == 409)
                        {
                            me.validaciones = error.response.data
                        }
                    } )
            },
            desactivarCategoria(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: 'Dar de baja esta categoria?',
                    //text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put('/categoria/baja/id', {
                            'id': id
                        })
                        .then( function(res){
                            me.listarCategoria(1, 'nombre', '');
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
            activarCategoria(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: 'Dar de alta esta categoria?',
                    //text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put('/categoria/alta/id', {
                            'id': id
                        })
                        .then( function(res){
                            me.listarCategoria(1, 'nombre', '');
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
            }
        },
        mounted() {
            console.log('Component Categoria cargado.')
            this.listarCategoria(1, this.criterio, this.txt_buscar);
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
