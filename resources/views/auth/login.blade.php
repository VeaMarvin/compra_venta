@extends('auth.contenido')

@section('login')
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-group mb-0">
                <div class="card p-4">
                    <form class="form-horizontal was-validated" method="POST" action="/login">
                        {{ csrf_field() }}
                        <div class="card-body">
                            <h1>Acceder</h1>
                            <p class="text-muted">Control de acceso al sistema</p>
                                                        
                            <div class="form-group mb-2{{$errors->has('usuario' ? 'is-invalid' : '')}}">
                                <span class="input-group-addon"><i class="icon-user"></i> &nbsp;&nbsp;&nbsp;
                                <input type="text" name="usuario" class="form-control" placeholder="Usuario" value="{{ old('usuario') }}"></span>
                                {!! $errors->first('usuario', "<span style='color:red;'> :message </span>") !!}
                            </div>
                            <div class="form-group mb-2{{$errors->has('password' ? 'is-invalid' : '')}}">
                                <span class="input-group-addon"><i class="icon-lock"></i> &nbsp;&nbsp;&nbsp;
                                <input type="password" name="password" class="form-control" placeholder="Password"></span>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary px-4">Acceder</button>
                                </div>
                                <!--<div class="col-6 text-right">
                                    <button type="button" class="btn btn-link px-0">Olvidaste tu password?</button>
                                </div>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
