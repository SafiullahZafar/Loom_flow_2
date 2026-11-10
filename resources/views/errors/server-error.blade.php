<!DOCTYPE html>
<html lang="en" class="h-full bg-zinc-50 text-zinc-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - LoomFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-full items-center justify-center p-6">
    <main class="w-full max-w-lg rounded-3xl border border-zinc-200 bg-white p-8 text-center shadow-sm">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-700" aria-hidden="true">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M5.06 19h13.88c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.33 16c-.77 1.33.19 3 1.73 3z" />
            </svg>
        </div>
        <p class="mt-5 text-xs font-bold uppercase tracking-widest text-zinc-400">LoomFlow</p>
        <h1 class="mt-2 text-2xl font-extrabold text-zinc-900">The server couldn’t complete that request</h1>
        <p class="mt-3 text-sm leading-6 text-zinc-500">Your sign-in or dashboard request ran into a server problem. Please try again. The technical details have been recorded for troubleshooting.</p>
        <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
            <button type="button" onclick="window.location.reload()" class="rounded-xl bg-zinc-900 px-5 py-3 text-sm font-bold text-white hover:bg-zinc-700">Try again</button>
            <a href="/login" class="rounded-xl border border-zinc-200 px-5 py-3 text-sm font-bold text-zinc-700 hover:bg-zinc-50">Back to sign in</a>
        </div>
        <p class="mt-6 text-xs text-zinc-400">If it keeps happening, ask your administrator to check the Laravel log.</p>
    </main>
</body>
</html>
