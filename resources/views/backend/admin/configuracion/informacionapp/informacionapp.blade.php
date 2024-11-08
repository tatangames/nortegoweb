@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }

    .widget-user-image2{
        left:50%;margin-left:-45px;
        position:absolute;
        top:80px
    }


    .widget-user-image2>img{
        border:3px solid #fff;
        height:auto;
    }

</style>


<div id="divcontenedor" style="display: none">


    <section class="content" style="margin-top: 20px">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-6">
                    <!-- general form elements -->
                    <div class="card card-red">
                        <div class="card-header">
                            <h3 class="card-title">BLOQUEO DE APLICACIÓN</h3>
                        </div>
                        <form>
                            <div style="margin: 16px">

                            <div class="form-group">
                                <label>No permite iniciar sesión, mostrara ventana en modo desarrollo</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="toggle-bloqueo">
                                    <div class="slider round">
                                        <span class="on">Bloqueado</span>
                                        <span class="off">Desactivado</span>
                                    </div>
                                </label>
                            </div>

                            <div class="card-footer">
                                <button type="button" onclick="verificarBloqueo()" class="btn btn-primary">Guardar</button>
                            </div>

                            </div>
                        </form>
                    </div>
                    <!-- /.card -->

                </div>



            </div>

        </div>
    </section>



</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            var info = {{ $desarrollo }};
            if(info == 1){
                $("#toggle-bloqueo").prop("checked", true);
            }else{
                $("#toggle-bloqueo").prop("checked", false);
            }

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function verificarBloqueo(){

            var t = document.getElementById('toggle-bloqueo').checked;
            var toggle = t ? 1 : 0;

            openLoading();
            var formData = new FormData();
            formData.append('toggle', toggle);

            axios.post('/admin/informacion/bloqueoapp', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                    }
                    else {
                        toastr.error('Error al actualizar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }


    </script>

@endsection
