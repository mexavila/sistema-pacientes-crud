<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Pacientes')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @if(session('api_token'))
    <meta name="api-token" content="{{ session('api_token') }}">
    <script>
        window.apiToken = '{{ session('api_token') }}';
    </script>
    @endif
    @stack('styles')
</head>
<body>
    @yield('content')
    <script>const returnURL = '{{ route("login") }}'; const apiURL = "{{ config('app.api_url') }}";</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Configurar Axios globalmente
        if (window.apiToken) {
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + window.apiToken;
        }
        
        // Interceptor para manejar errores 401
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response.status === 401) {
                    //window.location.href = returnURL +'?expired=1';
                }
                return Promise.reject(error);
            }
        );
    </script>
    @stack('scripts')
</body>
</html>