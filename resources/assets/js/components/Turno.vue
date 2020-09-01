<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Turnos
                    <button type="button" class="btn btn-success" @click="abrirModal('turno', 'Agregar turno', 'Agregar')">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Entrega</th>
                                <th>Recibe</th>
                                <th>Caja Inicial</th>
                                <th>Caja Final</th>
                                <th>Fecha Recibe</th>
                                <th>Fecha Entrega</th>
                                <th>Tiempo Laborado</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="turno in lista_turno">
                                <td>
                                    <template v-if="turno.fecha_entrega == null && turno.activo == 1">
                                        <button type="button" class="btn btn-success btn-sm"  @click="abrirModalTurno('turno', 'Entregar turno', 'Entregar', turno)">
                                            <i class="icon-check"></i>
                                        </button>
                                    </template>
                                </td>
                                <td v-text="turno.nombre_entrega">  </td> 
                                <td v-text="turno.nombre_recibe">  </td>                                 
                                <td v-text="turno.caja_inicio">  </td>  
                                <td v-text="turno.caja_fin">  </td>  
                                <td v-text="turno.fecha_recibe">  </td>  
                                <td v-text="turno.fecha_entrega">  </td> 
                                <td v-text="turno.tiempo_transcurrido">  </td>  
                                <td v-if="turno.activo == 1 ">
                                    <span class="badge badge-success">Turno Actual</span>
                                </td>
                                <td v-if="turno.activo == 0 && turno.fecha_recibe != null">
                                    <span class="badge badge-warning">Turno Finalizado</span>
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
                              <label class="col-md-3 form-control-label" for="text-input">¿A quién le recibe le turno?</label>
                              <div class="col-md-9">
                                <v-select 
                                    :on-search="getEmpleados"
                                    label="nombre"
                                    :options="lista_empleados"
                                    placeholder="Buscando empleados..."
                                    :on-change="getDatosEmpleados">
                                </v-select>                                
                              </div>
                          </div>                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Caja Inicial</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="caja_inicio" id="caja_inicio" name="caja_inicio" class="form-control" @input="caja_inicio = $event.target.value.toUpperCase()" placeholder="monto de la caja inicial">
                                </div>
                            </div>
                            <div v-show="error_turno" class="form-group row" style="display:flex; justify-content: center;">
                                
                                <div v-if="error_turno==1" v-for="error in errores">
                                    <span class="help-block error" v-text="error"></span>
                                    <br>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==1" @click="agregarturno()">Agregar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==2" @click="actualizarturno()">Actualizar</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>


        <div class="modal fade" tabindex="-1" :class="{'mostrar':modal_turno}" role="dialog" aria-labelledby="myModalLabel" style="display: none; overflow-y: scroll;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content" style="position: absolute; width: 100%">
                    <div class="modal-header">
                        <h4 class="modal-title"> ENTREGAR TURNO </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModalTurno()">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" v-if="Object.keys(validaciones_turno).length !== 0">
                            <template v-for="(value, index) in validaciones_turno">
                                <strong v-bind:key="value[0]">ERROR EN {{ index.toUpperCase() }}:</strong> {{ value[0] }}.
                                <br>
                            </template>
                        </div>

                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Monto del Cierre de Caja</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="caja_fin" id="caja_fin" name="caja_fin" class="form-control" @input="caja_fin = $event.target.value.toUpperCase()" placeholder="Escribir el monto">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModalTurno()">Cancelar</button>
                        <button type="button" class="btn btn-primary"  @click="activarturno()">Entregar Turno</button>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <!-- /Fin del contenido principal -->
</template>

<script>
    import vSelect from 'vue-select';
    export default {
        data() {
            return {
                validaciones: {},
                id_turno: 0,
                caja_inicio: '',
                id_recibe: '',
                lista_turno: [],
                lista_empleados: [],
                modal: 0,
                titulo_modal: '',
                tipo_accion: 0,
                error_turno: 0,
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
                criterio: 'caja_inicio',
                txt_buscar: '',
                modal_turno: '',
                validaciones_turno: {},
                caja_fin: ''
            }
        },
        components: {
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
            }
        },
        methods: {
            cambiarPagina(pagina, criterio, txt_buscar) {
                let me = this;
                this.paginacion.pagina_actual = pagina;
                me.listarturno(pagina, criterio, txt_buscar);
            },
            listarturno(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/turno?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_turno = respuesta.turnos.data;
                        me.paginacion = respuesta.paginacion;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            agregarturno() {
                let me = this;
                me.errores = [];
                me.validaciones = {};
                if(!me.caja_inicio) {
                    me.error_turno = 1;
                    me.errores.push('(*) Ingrese el monto de la caja de inicio');
                }
                else{
                    axios.post('/turno', {
                            'caja_inicio': me.caja_inicio,
                            'id_recibe': me.id_recibe
                        })
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                type: 'success',
                                title: 'El registro se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.listarturno(1, 'caja_inicio', '');
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
                this.titulo_modal = titulo;
                switch(modelo){
                    case 'turno':
                        switch(accion) {
                            case 'Agregar':
                                this.modal = 1;
                                this.tipo_accion = 1;
                            break;
                        }
                    break;

                }
            },
            cerrarModal() {
                this.modal = 0;
                this.titulo_modal = '';
                this.caja_inicio = '';
                this.id_recibe = '';
                this.tipo_accion = 0;
                this.error_turno = 0;
            },
            abrirModalTurno(modelo, titulo, accion, data=[]){
                this.validaciones_turno = {}
                switch(modelo){
                    case 'turno':
                        switch(accion) {
                            case 'Entregar': 
                                this.modal_turno = 1;
                                this.id_turno = data.id
                                this.caja_fin = '';
                            break;    
                        }
                    break;

                }
            }, 
            cerrarModalTurno() {
                this.modal_turno = 0;
                this.id_turno = '';
                this.caja_fin = '';
            },           
            activarturno() {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons({
                    title: 'Entregar el turno?',
                    //text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put('/turno/alta/id', {
                            'id': this.id_turno,
                            'caja_fin': this.caja_fin
                        })
                        .then( function(res){
                            me.listarturno(1, 'caja_inicio', '');
                            swal({
                                type: 'success',
                                title: 'Turno entregado!',
                                showConfirmButton: false,
                                timer: 1300
                            })
                            me.cerrarModalTurno()
                        } )
                        .catch( function(error){;
                            if(error.response.status == 409)
                            {
                                me.validaciones_turno = error.response.data
                            }
                        } )
                    } else if (
                        result.dismiss === swal.DismissReason.cancel
                    ) { }
                })
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
            getDatosEmpleados(valor) {
                let me = this;
                me.loading = true;
                me.id_recibe = valor.id;
            },
        },
        mounted() {
            console.log('Component turno cargado.')
            this.listarturno(1, this.criterio, this.txt_buscar);
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
