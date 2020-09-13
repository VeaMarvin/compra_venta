@extends( 'principal' )

@section( 'contenido' )

    @if(Auth::check())
        @if(Auth::user()->id_rol == 1)
            <template v-if="menu==0">
                <dashboard></dashboard>
            </template>
            <template v-if="menu==1">
                <categoria></categoria>
            </template>
            <template v-if="menu==2">
                <articulo></articulo>
            </template>
            <template v-if="menu==3">
                <ingreso></ingreso>
                <!--<factura></factura>-->
            </template>
            <template v-if="menu==4">
                <proveedor></proveedor>
            </template>
            <template v-if="menu==5">
                <venta></venta>
            </template>
            <template v-if="menu==6">
                <cliente></cliente>
            </template>
            <template v-if="menu==7">
                <user></user>
            </template>
            <template v-if="menu==8">
                <rol></rol>
            </template>
            <template v-if="menu==9">
                <consulta_ingreso></consulta_ingreso>
            </template>
            <template v-if="menu==10">
                <consulta_venta></consulta_venta>
            </template>    
            <template v-if="menu==11">
                <talonario></talonario>
            </template>    
            <template v-if="menu==12">
                <turno></turno>
            </template>     
            <template v-if="menu==13">
                <deposito></deposito>
            </template>      
            <template v-if="menu==14">
                <venta_formulario></venta_formulario>
            </template>       
            <template v-if="menu==15">
                <ingreso_formulario></ingreso_formulario>
            </template>
            <template v-if="menu==16">
                <credito></credito>
            </template>    
        @elseif(Auth::user()->id_rol == 2)
            <template v-if="menu==0">
                <dashboard></dashboard>
            </template>  
            <template v-if="menu==14">
                <venta_formulario></venta_formulario>
            </template>   
            <template v-if="menu==12">
                <turno></turno>
            </template>      
            <template v-if="menu==13">
                <deposito></deposito>
            </template> 
            <template v-if="menu==16">
                <credito></credito>
            </template>    
        @elseif(Auth::user()->id_rol == 3)
            <template v-if="menu==0">
                <dashboard></dashboard>
            </template>
            <template v-if="menu==3">
                <ingreso></ingreso>
            </template>
            <template v-if="menu==4">
                <proveedor></proveedor>
            </template>    
            <template v-if="menu==15">
                <ingreso_formulario></ingreso_formulario>
            </template>       
        @endif
    @endif

@endsection