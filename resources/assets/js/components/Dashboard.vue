<template>
    <main class="main">
        <br><br>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-chart">
                                <div class="card card-header">
                                    <h4>Ingresos</h4>
                                </div>
                                <div class="card card-content">
                                    <div class="ct-chart">
                                        <canvas id="ingresos">

                                        </canvas>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Compras de los ultimos meses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-chart">
                                <div class="card card-header">
                                    <h4>Ventas</h4>
                                </div>
                                <div class="card card-content">
                                    <div class="ct-chart">
                                        <canvas id="ventas">

                                        </canvas>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Ventas de los ultimos meses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>    
</template>

<script>
    export default {
        data() {
            return {
                var_ingreso: null,
                char_ingreso: null,
                ingresos: [],
                total_ingreso: [],
                mes_ingreso: [],
                var_venta: null,
                char_venta: null,
                ventas: [],
                total_venta: [],
                mes_venta: [],
                lista_meses: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ]
            }
        },
        methods: {
            getData() {
                let me = this;
                axios.get( '/dashboard' )
                    .then( function(res) {
                        me.ingresos = res.data.ingresos;
                        me.ventas = res.data.ventas;
                        me.loadDasboardIngresos();
                        me.loadDasboardVentas();
                    } )
                    .catch( function(error) {
                        console.log( error );
                    } );
            },
            loadDasboardIngresos() {
                let me = this;
                me.ingresos.map( function(x) {
                    me.mes_ingreso.push( me.lista_meses[x.mes-1] );
                    me.total_ingreso.push(x.total);
                } );
                me.var_ingreso = document.getElementById('ingresos').getContext( '2d' );
                me.char_ingreso = new Chart(me.var_ingreso, {
                    type: 'bar',
                    data: {
                        labels: me.mes_ingreso,
                        datasets: [{
                            label: 'Ingresos',
                            data: me.total_ingreso,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor:  'rgba(255,99,132,0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                })
            },
            loadDasboardVentas() {
                let me = this;
                me.ventas.map( function(x) {
                    me.mes_venta.push( me.lista_meses[x.mes-1] );
                    me.total_venta.push(x.total);
                } );
                me.var_venta = document.getElementById('ventas').getContext( '2d' );
                me.char_venta = new Chart(me.var_venta, {
                    type: 'bar',
                    data: {
                        labels: me.mes_venta,
                        datasets: [{
                            label: 'Ventas',
                            data: me.total_venta,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor:  'rgba(54,162,235,0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                })
            },

        },
        mounted() {
            this.getData();
        }
    }
</script>