<template>
    <!-- Contenido Principal -->
    <main class="main">
        <!-- Breadcrumb -->
        <br><br>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Deposito
                    <button type="button" class="btn btn-success" @click="abrirModal('deposito', 'Agregar deposito', 'Agregar')">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                    <option value="fecha">Fecha</option>
                                </select>
                                <input type="date" v-model="txt_buscar" @keyup.enter="listardeposito(1, criterio, txt_buscar)" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listardeposito(1, criterio, txt_buscar)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                &nbsp;&nbsp;
                                <button type="button" class="btn btn-success" @click="listardeposito(1,'','')"><i class="fa fa-refresh"></i> Recargar</button>
                                <button type="button" @click="cargarPDF()" class="btn btn-info">
                                    <i class="icon-doc"></i>&nbsp;Reporte PDF
                                </button>                            
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Opcion</th>
                                <th>Boleta</th>
                                <th>Imagen</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Empleado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="deposito in lista_deposito">
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" @click="abrirModal('deposito', 'Agregar deposito', 'Eliminar', deposito)">
                                        <i class="icon-trash"></i>
                                    </button> &nbsp;
                                </td>
                                <td v-text="deposito.boleta">  </td>  
                                <td> <img :src="deposito.foto" width="25%" height="25%" alt="deposito.foto"> </td>  
                                <td v-text="deposito.fecha">  </td>  
                                <td v-text="deposito.monto">  </td>   
                                <td v-text="deposito.nombre">  </td>   
                                <td v-if="deposito.compra_venta == 0">VENTA</td>  
                                <td v-if="deposito.compra_venta == 1">COMPRA</td> 
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
                                <label class="col-md-3 form-control-label" for="text-input">Número de Boleta</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="boleta" id="boleta" name="boleta" class="form-control" @input="boleta = $event.target.value.toUpperCase()" placeholder="número de boleta">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Fecha del Deposito</label>
                                <div class="col-md-9">
                                    <input type="date" v-model="fecha" id="fecha" name="fecha" class="form-control" @input="fecha = $event.target.value.toUpperCase()" placeholder="fecha del deposito">
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Monto del Deposito</label>
                                <div class="col-md-9">
                                    <input type="text" v-model="monto" id="monto" name="monto" class="form-control" @input="monto = $event.target.value.toUpperCase()" placeholder="monto del deposito">
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Imagen de la Boleta</label>
                                <div class="col-md-9">
                                  <input id="foto" type="file" ref="foto" name="foto" @change="addFile()">                                   
                                </div>
                            </div>     
                            <div class="form-group row">
                                <div class="col-md-9">
                                    <section>
                                        <input type="radio" v-model="compra_venta" value="1"> BOLETA DE COMPRA
                                        &nbsp; &nbsp;
                                        <input type="radio" v-model="compra_venta" value="0"> BOELTA DE VENTA
                                    </section>                                  
                                </div>
                            </div>                              
                            <div v-show="error_deposito" class="form-group row" style="display:flex; justify-content: center;">
                                
                                <div v-if="error_deposito==1" v-for="error in errores">
                                    <span class="help-block error" v-text="error"></span>
                                    <br>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==1" @click="agregardeposito()">Agregar</button>
                        <button type="button" class="btn btn-primary" v-if="tipo_accion==2" @click="actualizardeposito()">Actualizar</button>
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
                id_deposito: 0,
                boleta: '',
                fecha: '',
                monto: '',
                compra_venta: '',
                lista_deposito: [],
                modal: 0,
                titulo_modal: '',
                tipo_accion: 0,
                error_deposito: 0,
                errores: [],
                foto: '',
                paginacion: {
                    'total': 0,
                    'pagina_actual': 0,
                    'por_pagina': 0,
                    'ultima_pagina': 0,
                    'desde': 0,
                    'hasta': 0
                },
                offset: 3,
                criterio: 'fecha',
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
            cargarPDF(){
                window.open('/deposito/listar/pdf?criterio=' + this.criterio + '&buscar=' + this.txt_buscar, '_blank' );
            },

            addFile() {
                this.foto = this.$refs.foto.files[0];
            },
          
            cambiarPagina(pagina, criterio, txt_buscar) {
                let me = this;
                this.paginacion.pagina_actual = pagina;
                me.listardeposito(pagina, criterio, txt_buscar);
            },
            listardeposito(pagina, criterio, txt_buscar) {
                let me = this;
                var url = '/deposito?page=' + pagina + '&criterio=' + criterio + '&buscar=' + txt_buscar;
                axios.get( url )
                    .then( function(res){
                        var respuesta = res.data;
                        me.lista_deposito = respuesta.depositos.data;
                        me.paginacion = respuesta.paginacion;
                    } )
                    .catch( function(error){
                        console.log(error);
                    } )
            },
            agregardeposito() {
                let me = this;
                me.errores = [];
                me.validaciones = {};
                if(!me.boleta) {
                    me.error_deposito = 1;
                    me.errores.push('(*) Ingrese el número de boleta');
                }
                else{
                    let formData = new FormData();
                    formData.append('boleta', this.boleta);
                    formData.append('foto', this.foto);
                    formData.append('fecha', this.fecha);
                    formData.append('monto', this.monto);
                    formData.append('compra_venta', this.compra_venta);

                    axios.post('/deposito', formData, {headers: {'Content-Type': 'multipart/form-data'}})
                        .then( function(res){
                            me.cerrarModal();
                            swal({
                                type: 'success',
                                title: 'El registro se ha guardado!',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            me.listardeposito(1, 'fecha', '');
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
                    case 'deposito':
                        switch(accion) {
                            case 'Agregar':
                                this.modal = 1;
                                this.tipo_accion = 1;
                                //console.log(accion + " " + this.modal + " / " + titulo + " " + modelo);
                            break;
                            case 'Eliminar': 
                                const swalWithBootstrapButtons = swal.mixin({
                                    confirmButtonClass: 'btn btn-success',
                                    cancelButtonClass: 'btn btn-danger',
                                    buttonsStyling: false,
                                })
                                swalWithBootstrapButtons({
                                    title: 'Eliminar deposito?',
                                    //text: "You won't be able to revert this!",
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Aceptar',
                                    cancelButtonText: 'Cancelar',
                                    reverseButtons: true
                                    }).then((result) => {
                                    if (result.value) {
                                        let me = this;
                                        axios.put('/deposito/' + data.id, {
                                                'id': data.id,
                                            })             
                                            .then( function(res){
                                                swal({
                                                    title: "Información",
                                                    text: "Deposito eliminado!",
                                                    icon: "success",
                                                    button: "Aceptar",
                                                });
                                                me.listardeposito(1, 'fecha', '');
                                            } )
                                            .catch( function(error){
                                                if(error.response.status == 409)
                                                {
                                                    me.validaciones = error.response.data
                                                }
                                            } )
                                    } else if (
                                        result.dismiss === swal.DismissReason.cancel
                                    ) { }
                                })
                            break;    
                        }
                    break;

                }
            },
            cerrarModal() {
                this.modal = 0;
                this.titulo_modal = '';
                this.boleta = '';
                this.foto = '';
                this.fecha = '';
                this.monto = '';
                this.compra_venta = '';
                this.tipo_accion = 0;
                this.error_deposito = 0;
            },
            actualizardeposito() {
                let me = this;

                axios.put('/deposito/' + me.id_deposito, {
                        'id': me.id_categoria,
                        'boleta': me.boleta,
                        'foto': me.foto,
                        'fecha': me.fecha,
                        'monto': me.monto,
                        'compra_venta': me.compra_venta
                    })             
                    .then( function(res){
                        me.cerrarModal();
                        swal({
                            title: "Información",
                            text: "El registro se ha actualizado!",
                            icon: "success",
                            button: "Aceptar",
                        });
                        me.listardeposito(1, 'fecha', '');
                    } )
                    .catch( function(error){
                        if(error.response.status == 409)
                        {
                            me.validaciones = error.response.data
                        }
                    } )
            },
        },
        mounted() {
            console.log('Component deposito cargado.')
            this.listardeposito(1, this.criterio, this.txt_buscar);
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
