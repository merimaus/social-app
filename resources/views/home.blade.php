<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veil Social</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-white border-b p-4 mb-6 shadow-sm">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-xl font-bold text-blue-600">Veil</h1>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-4">
        <livewire:main-feed />
    </div>
</body>
</html>