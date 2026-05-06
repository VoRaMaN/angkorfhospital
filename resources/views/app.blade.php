<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class([
    'dark' => ($appearance ?? 'system') == 'dark',
    'blue' => ($appearance ?? 'system') == 'blue',
    'green' => ($appearance ?? 'system') == 'green',
    'enterprise' => ($appearance ?? 'system') == 'enterprise',
    'cit' => ($appearance ?? 'system') == 'cit',
    'modern' => ($appearance ?? 'system') == 'modern'
])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                } else if (appearance === 'dark') {
                    document.documentElement.classList.add('dark');
                } else if (appearance === 'blue') {
                    document.documentElement.classList.add('blue');
                } else if (appearance === 'green') {
                    document.documentElement.classList.add('green');
                } else if (appearance === 'enterprise') {
                    document.documentElement.classList.add('enterprise');
                } else if (appearance === 'cit') {
                    document.documentElement.classList.add('cit');
                } else if (appearance === 'modern') {
                    document.documentElement.classList.add('modern');
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }

            html.blue {
                background-color: hsl(210 40% 98%);
            }

            html.green {
                background-color: hsl(120 40% 98%);
            }

            html.enterprise {
                background-color: hsl(0 0% 92%);
            }

            html.cit {
                background-color: hsl(330 35% 96%);
            }

            html.modern {
                background-color: hsl(228 50% 95%);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/images/logo.png" type="image/png">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
