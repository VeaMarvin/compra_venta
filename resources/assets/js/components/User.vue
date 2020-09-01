<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Usuarios
                    <button type="button" class="btn btn-success" @click="abrirModal('usuario', 'Agregar usuario', 'Agregar')">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                    <option value="nombre">Nombre</option>
                                    <option value="usuario">Usuario</option>
                                    <option value="email">Email</option>
                                    <option value="telefono">Telefono</option>
                                </select>
                                <input type="text" v-model="txt_buscar" @keyup.enter="listarUsuario(1, criterio, txt_buscar)" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listarUsuario(1, criterio, txt_buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                &nbsp;&nbsp;
                                <button type="button" class="btn btn-success" @click="listarUsuario(1,'','')"><i class="fa fa-refresh"></i> Recargar</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Direccion</th>
                                <th>Telefono</th>
                                <th>Email</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="usuario in lista_usuario">
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" @click="abrirModal('usuario', 'Actualizar usuario', 'Actualizar', usuario)">
                                        <i class="icon-pencil"></i>
                                    </button> &nbsp;
                                    <!--<button type="button" class="btn btn-danger btn-sm" @click="desactivarUsuario()">
                                        <i class="icon-trash"></i>
                                    </button>-->
                                    <template v-if="usuario.estado">
                                        <button type="button" class="btn btn-danger btn-sm" @click="activarDesactivarCategoria(usuario.id, 0)">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button type="button" class="btn btn-success btn-sm" @click="activarDesactivarCategoria(usuario.id, 1)">
                                            <i class="icon-check"></i>
                                        </button>
                                    </template>
                                </td>
                                <td v-text="usuario.nombre">  </td>  
                                <td v-text="usuario.usuario">  </td>  
                                <td v-text="usuario.rol">  </td>  
                                <td v-text="usuario.direccion">  </td>  
                                <td v-text="usuario.telefono">  </td>  
                                <td v-text="usuario.email">  </td>  
                                <td v-if="usuario.estado == 1">
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

        <!--MODAL-->
        <div  class="modal fade" :class="{'mostrar':modal}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; overflow-y: scroll;" >
            <div role="document" class="modal-dialog modal-primary modal-lg" style="max-height:85%;  margin-top: 50px; margin-bottom:50px;" > 
            <!--<div class="modal-dialog" role="document">-->
                <div class="modal-content" style="width: 100%; margin-top:20%;">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titulo_modal">  </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModal()">
                            <span aria-hidden="true">&times;</span>
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
                                    <input type="text" v-model="email" @input="email = $event.target.value.toUpperCase()" class="form-control" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Usuario</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="usuario" @input="email = $event.target.value.toLowerCase()" class="form-control" placeholder="Nombre de usuario">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Password</label>
                                <div class="col-md-9">
                                    <input type="password" v-model="password" class="form-control" placeholder="Contraseña de acceso">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Rol</label>
                                <div class="col-md-9">
                                    <select class="form-control" v-model="id_rol">
                                        <option value="0" disabled>Selecciona rol</option>
                                        <option v-for="rol in lista_rol" v-text="rol.nombre" :value="rol.id"></option>
                                    </select>
                                </div>
                            </div>
                            <div v-show="error_usuario" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-if="error_usuario==1" v-for="error in errores" class="text-center text-error">
                                        <span class="help-block error" v-text="error"></span>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==1" @click="agregarUsuario()">Agregar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==2" @click="actualizarUsuario()">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- /Fin del contenido principal -->
</template>

<script>
    export default {
        data() {
            return {
                id_usuario: 0,
                id_rol: 0,
                nombre: '',
                tipo_documento: '',
                numero_documento: 'CF',
                direccion: '',
                email: '',
                usuario: '',
                password: '',
                telefono: '',
                validaciones: {},
                lista_usuario: [],
                modal: 0,
                titulo_modal: '',
                tipo_accion: 0,
                error_usuario: 0,
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
                lista_rol: []
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
                me.listarUsuario(pagina, criterio, txt_buscar);
            },
            listarUsuario(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/user?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_usuario = respuesta.users.data;
                        me.paginacion = respuesta.paginacion;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            agregarUsuario() {
                let me = this;
                me.errores = [];
                me.error_usuario = 0;
                if(!me.nombre) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese el nombre del empleado');
                }
                if(!me.numero_documento) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese NIT del empleado');
                }
                if(!me.usuario) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese el nombre de usuario');
                }
                if(!me.password) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese la contraseña de acceso');
                }
                if(!me.id_rol) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese el rol del empleado');
                }
                if(me.error_usuario == 1) return;
                else{
                    axios.post('/user', {
                            'nombre': me.nombre,
                            'tipo_documento': me.tipo_documento,
                            'numero_documento': me.numero_documento,
                            'direccion': me.direccion,
                            'telefono': me.telefono,
                            'email': me.email,
                            'usuario': me.usuario,
                            'password': me.password,
                            'id_rol': me.id_rol,
                        })
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                type: 'success',
                                title: 'El registro se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.listarUsuario(1, 'nombre', '');
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
                this.getListaRol();
                switch(modelo){
                    case 'usuario':
                        switch(accion) {
                            case 'Agregar':
                                this.modal = 1;
                                this.tipo_accion = 1;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;
                            case 'Actualizar': 
                                this.modal = 1;
                                this.id_usuario = data.id;
                                this.tipo_accion = 2;
                                this.nombre = data.nombre;
                                this.tipo_documento = data.tipo_documento;
                                this.numero_documento = data.numero_documento;
                                this.direccion = data.direccion;
                                this.telefono = data.telefono;
                                this.email = data.email;
                                this.usuario = data.usuario;
                                this.password = data.password;
                                this.id_rol = data.id_rol;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;    
                        }
                    break;

                }
            },
            cerrarModal() {
                this.modal = 0;
                this.id_usuario = 0;
                this.id_rol = 0;
                this.titulo_modal = '';
                this.nombre = '';
                this.tipo_documento = '';
                this.numero_documento = 'CF';
                this.direccion = '';
                this.telefono = '';
                this.email = '';
                this.usuario = '';
                this.tipo_accion = 0;
                this.error_usuario = 0;
            },
            actualizarUsuario() {
                let me = this;
                me.error_usuario = 0;
                me.errores = [];
                if(!me.nombre) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese el nombre del empleado');
                }
                if(!me.numero_documento) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese NIT del empleado');
                }
                if(!me.usuario) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese el nombre de usuario');
                }
                if(!me.password) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese la contraseña de acceso');
                }
                if(!me.id_rol) {
                    me.error_usuario = 1;
                    me.errores.push('(*) Ingrese el rol del empleado');
                }
                if(me.error_usuario == 1) return;
                else{
                    axios.put('/user/' + me.id_usuario, {
                            'id': me.id_usuario,
                            'nombre': me.nombre,
                            'tipo_documento': me.tipo_documento,
                            'numero_documento': me.numero_documento,
                            'direccion': me.direccion,
                            'telefono': me.telefono,
                            'email': me.email,
                            'usuario': me.usuario,
                            'password': me.password,
                            'id_rol': me.id_rol,
                        })
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                title: "Información",
                                text: "El registro se ha actualizado!",
                                icon: "success",
                                button: "Aceptar",
                            });
                            me.listarUsuario(1, 'nombre', '');
                        } )
                        .catch( function(error){
                            if(error.response.status == 409)
                            {
                                me.validaciones = error.response.data
                            }
                        } )
                }
            },
            getListaRol() {
                let me = this;
                axios.get( '/roles' )
                    .then( function(res){
                        me.lista_rol = res.data.roles;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            activarDesactivarCategoria(id, activar) {
                const swalWithBootstrapButtons = swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false,
                })
                let title_modal = activar == 1 ? 'Activar este usuario?' : 'Desactivar este usuario?';
                let url_activar = activar == 1 ? '/user/alta/id' : '/user/baja/id';
                let mensaje = activar == 1 ? 'Registro activado!' : 'Registro desactivado!';
                swalWithBootstrapButtons({
                    title: title_modal,
                    //text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let me = this;
                        axios.put( url_activar , {
                            'id': id
                        })
                        .then( function(res){
                            me.listarUsuario(1, 'nombre', '');
                            swal({
                                type: 'success',
                                title: mensaje,
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
        },
        mounted() {
            console.log('Component Usuario cargado.')
            this.listarUsuario(1, this.criterio, this.txt_buscar);
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
