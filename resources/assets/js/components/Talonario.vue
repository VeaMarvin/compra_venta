<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Talonarios
                    <button type="button" class="btn btn-success" @click="abrirModal('talonario', 'Agregar talonario', 'Agregar')">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Correlativo de Inicio</th>
                                <th>Correlativo de Finalización</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="talonario in lista_talonario">
                                <td v-text="talonario.serie">  </td>  
                                <td v-text="talonario.inicio">  </td>  
                                <td v-text="talonario.fin">  </td>   
                                <td v-if="talonario.activo == 1">
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
                                <label class="col-md-3 form-control-label" for="text-input">Serie</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="serie" id="serie" name="serie" class="form-control" @input="serie = $event.target.value.toUpperCase()" placeholder="Número de Serie">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Correlativo de Inicio</label>
                                <div class="col-md-9">
                                    <input type="number" v-model="inicio" id="inicio" name="inicio" class="form-control" @input="inicio = $event.target.value.toUpperCase()" placeholder="Primer número del talonario">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Correlativo de Finalización</label>
                                <div class="col-md-9">
                                    <input type="number" v-model="fin" id="fin" name="fin" class="form-control" @input="fin = $event.target.value.toUpperCase()" placeholder="Último número del talonario">
                                </div>
                            </div>
                            <div v-show="error_talonario" class="form-group row" style="display:flex; justify-content: center;">
                                
                                <div v-if="error_talonario==1" v-for="error in errores">
                                    <span class="help-block error" v-text="error"></span>
                                    <br>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==1" @click="agregartalonario()">Agregar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==2" @click="actualizartalonario()">Actualizar</button>
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
                id_talonario: 0,
                serie: '',
                inicio: '',
                fin: '',
                lista_talonario: [],
                modal: 0,
                titulo_modal: '',
                tipo_accion: 0,
                error_talonario: 0,
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
                criterio: 'serie',
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
                me.listartalonario(pagina, criterio, txt_buscar);
            },
            listartalonario(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/talonario?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_talonario = respuesta.talonarios.data;
                        me.paginacion = respuesta.paginacion;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            agregartalonario() {
                let me = this;
                me.errores = [];
                me.validaciones = {};
                if(!me.serie) {
                    me.error_talonario = 1;
                    me.errores.push('(*) Ingrese la serie del talonario');
                }
                else{
                    axios.post('/talonario', {
                            'serie': me.serie,
                            'inicio': me.inicio,
                            'fin': me.fin
                        })
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                type: 'success',
                                title: 'El registro se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.listartalonario(1, 'serie', '');
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
                    case 'talonario':
                        switch(accion) {
                            case 'Agregar':
                                this.modal = 1;
                                this.tipo_accion = 1;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;
                            case 'Actualizar': 
                                this.modal = 1;
                                this.tipo_accion = 2;
                                this.serie = data.serie;
                                this.inicio = data.inicio;
                                this.fin = data.fin;
                                this.id_talonario = data.id;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;    
                        }
                    break;

                }
            },
            cerrarModal() {
                this.modal = 0;
                this.titulo_modal = '';
                this.serie = '';
                this.inicio = '';
                this.fin = '';
                this.tipo_accion = 0;
                this.error_talonario = 0;
            },
            actualizartalonario() {
                let me = this;
                axios.put('/talonario/' + me.id_talonario, {
                        'id': me.id_talonario,
                        'serie': me.serie,
                        'inicio': me.inicio,
                        'fin': me.fin
                    })
                    .then( function(res){
                        me.cerrarModal();
                        swal({
                            title: "Información",
                            text: "El registro se ha actualizado!",
                            icon: "success",
                            button: "Aceptar",
                        });
                        me.listartalonario(1, 'serie', '');
                    } )
                    .catch( function(error){
                        if(error.response.status == 409)
                        {
                            me.validaciones = error.response.data
                        }
                    } )
            },
            desactivartalonario(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: 'Dar de baja esta talonario?',
                    //text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put('/talonario/baja/id', {
                            'id': id
                        })
                        .then( function(res){
                            me.listartalonario(1, 'serie', '');
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
            activartalonario(id) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: 'Dar de alta esta talonario?',
                    //text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put('/talonario/alta/id', {
                            'id': id
                        })
                        .then( function(res){
                            me.listartalonario(1, 'serie', '');
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
            console.log('Component talonario cargado.')
            this.listartalonario(1, this.criterio, this.txt_buscar);
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
