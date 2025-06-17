<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>{{ $title }}</title>
</head>

<body>
    {{ $slot }}
    <footer>
        <small>&copy; 2025 My App</small>
    </footer>
</body>

</html>
