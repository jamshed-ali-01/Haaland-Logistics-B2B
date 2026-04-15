<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Haaland Logistics') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- jQuery & DataTables (Professional Theme) -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link href="https://cdn.datatables.net/2.0.3/css/dataTables.tailwindcss.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.3/js/dataTables.tailwindcss.js"></script>
    </head>
    <body x-data="{ sidebarOpen: false }" 
          class="font-sans antialiased text-slate-900 bg-slate-50 h-full overflow-hidden transition-colors duration-300">
        <x-toast />
        @include('layouts.sidebar')

        <!-- Auto Init DataTables -->
        <script>
            $(document).ready(function() {
                if ($('.datatable').length > 0) {
                    $('.datatable').DataTable({
                        responsive: true,
                        pageLength: 25,
                        language: {
                            search: "<span class='text-xs font-bold text-brand-700 uppercase tracking-widest'>Search Database:</span>",
                            lengthMenu: "<span class='text-xs font-bold text-slate-500 uppercase tracking-widest'>Show _MENU_ entries</span>"
                        }
                    });
                }
            });
        </script>
    </body>
</html>
