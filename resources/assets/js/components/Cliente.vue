<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Clientes
                    <button type="button" class="btn btn-success" @click="abrirModal('cliente', 'Agregar cliente', 'Agregar')">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                    <option value="nombre">Nombre</option>
                                    <option value="numero_documento">Numero de documento</option>
                                    <option value="email">Email</option>
                                    <option value="telefono">Telefono</option>
                                </select>
                                <input type="text" v-model="txt_buscar" @keyup.enter="listarCliente(1, criterio, txt_buscar)" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listarCliente(1, criterio, txt_buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
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
                                <th>Documento</th>
                                <th>Folio</th>
                                <th>Direccion</th>
                                <th>Telefono</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="cliente in lista_cliente">
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" @click="abrirModal('cliente', 'Actualizar cliente', 'Actualizar', cliente)">
                                        <i class="icon-pencil"></i>
                                    </button> &nbsp;
                                </td>
                                <td v-text="cliente.nombre">  </td>  
                                <td v-text="cliente.tipo_documento">  </td>  
                                <td v-text="cliente.numero_documento">  </td>  
                                <td v-text="cliente.direccion">  </td>  
                                <td v-text="cliente.telefono">  </td>  
                                <td v-text="cliente.email">  </td>  
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
                                    <input type="text" v-model="nombre" @input="nombre = $event.target.value.toUpperCase()" class="form-control" placeholder="Nombre de cliente">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">NIT</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="numero_documento" @input="numero_documento = $event.target.value.toUpperCase()" class="form-control" placeholder="Numero de documento">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Direccion</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="direccion" @input="direccion = $event.target.value.toUpperCase()" class="form-control" placeholder="Direccion">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Telefono</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="telefono" @input="telefono = $event.target.value.toUpperCase()" class="form-control" placeholder="Telefono">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Email</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="email" @input="email = $event.target.value.toUpperCase()" class="form-control" placeholder="email">
                                </div>
                            </div>
                            <div v-show="error_cliente" class="form-group row" style="display:flex; justify-content: center;">
                                <div v-if="error_cliente==1" v-for="error in errores">
                                    <span class="help-block error" v-text="error"></span>
                                    <br>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==1" @click="agregarCliente()">Agregar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==2" @click="actualizarCliente()">Actualizar</button>
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
                id_cliente: 0,
                nombre: '',
                tipo_documento: '',
                numero_documento: 'CF',
                email: '',
                telefono: '',
                direccion: '',
                lista_cliente: [],
                modal: 0,
                titulo_modal: '',
                tipo_accion: 0,
                error_cliente: 0,
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
                me.listarCliente(pagina, criterio, txt_buscar);
            },
            listarCliente(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/cliente?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_cliente = respuesta.personas.data;
                        me.paginacion = respuesta.paginacion;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            agregarCliente() {
                let me = this;
                me.errores = [];
                if(!me.nombre) {
                    me.error_cliente = 1;
                    me.errores.push('(*) Ingrese el Nombre del cliente');
                }
                if(!me.numero_documento) {
                    me.error_cliente = 1;
                    me.errores.push('(*) Ingrese el NIT del cliente');
                }                
                else{
                    axios.post('/cliente', {
                            'nombre': me.nombre,
                            'tipo_documento': me.tipo_documento,
                            'numero_documento': me.numero_documento,
                            'direccion': me.direccion,
                            'telefono': me.telefono,
                            'email': me.email,
                        })
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                type: 'success',
                                title: 'El registro se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.listarCliente(1, 'nombre', '');
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
                    case 'cliente':
                        switch(accion) {
                            case 'Agregar':
                                this.modal = 1;
                                this.tipo_accion = 1;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;
                            case 'Actualizar': 
                                this.modal = 1;
                                this.id_cliente = data.id;
                                this.tipo_accion = 2;
                                this.nombre = data.nombre;
                                this.tipo_documento = data.tipo_documento;
                                this.numero_documento = data.numero_documento;
                                this.direccion = data.direccion;
                                this.telefono = data.telefono;
                                this.email = data.email;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;    
                        }
                    break;

                }
            },
            cerrarModal() {
                this.modal = 0;
                this.id_cliente = 0;
                this.titulo_modal = '';
                this.nombre = '';
                this.tipo_documento = '';
                this.numero_documento = '';
                this.direccion = '';
                this.telefono = '';
                this.email = '';
                this.tipo_accion = 0;
                this.error_cliente = 0;
            },
            actualizarCliente() {
                let me = this;
                me.errores = [];
                if(!me.nombre) {
                    me.error_cliente = 1;
                    me.errores.push('(*) Ingrese el Nombre del cliente');
                }
                if(!me.numero_documento) {
                    me.error_cliente = 1;
                    me.errores.push('(*) Ingrese el NIT del cliente');
                } 
                else{
                    axios.put('/cliente/' + me.id_cliente, {
                        'id': me.id_cliente,
                        'nombre': me.nombre,
                        'tipo_documento': me.tipo_documento,
                        'numero_documento': me.numero_documento,
                        'direccion': me.direccion,
                        'telefono': me.telefono,
                        'email': me.email,
                    })
                    .then( function(res){
                        me.cerrarModal();
                        swal({
                            title: "Información",
                            text: "El registro se ha actualizado!",
                            icon: "success",
                            button: "Aceptar",
                        });
                        me.listarCliente(1, 'nombre', '');
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
            console.log('Component Cliente cargado.')
            this.listarCliente(1, this.criterio, this.txt_buscar);
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
