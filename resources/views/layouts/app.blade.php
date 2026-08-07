<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Inventory Desk is a real Laravel journey built to exercise GyosJS reactive HTML and MPA Boost.">
    <title>{{ $title }} · Inventory Desk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body g-boost>
<div id="app" g-outlet g-snapshot>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('dashboard') }}">
                <span class="brand-mark">ID</span>
                <span class="brand-copy"><strong>Inventory Desk</strong><span>GyosJS field test</span></span>
            </a>

            <nav>
                <div class="nav-kicker">Workspace</div>
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <span class="nav-index">01</span> Overview
                </a>
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" g-preload>
                    <span class="nav-index">02</span> Products
                </a>
                <a class="nav-link {{ request()->routeIs('products.create') ? 'active' : '' }}" href="{{ route('products.create') }}">
                    <span class="nav-index">03</span> Add product
                </a>
            </nav>

            <form class="reset-form" method="post" action="{{ route('demo.reset') }}">
                @csrf
                <button class="reset-button" type="submit">Reset my demo data</button>
            </form>
        </aside>

        <section class="page">
            <header class="topbar">
                <div><span class="eyebrow">Operations / {{ now()->format('Y.m.d') }}</span><h1>{{ $title }}</h1></div>
                <div class="topbar-status"><span class="status-dot"></span> Session workspace isolated</div>
            </header>

            @if (session('success'))
                <div class="flash" g-scope="{ visible: true }" g-show="visible" g-transition>
                    <strong>{{ session('success') }}</strong>
                    <button type="button" @click="visible = false" aria-label="Dismiss notification">×</button>
                </div>
            @endif

            <main class="content">@yield('content')</main>
        </section>
    </div>

    <div id="modal-shell"></div>

    <section class="scratchpad" g-persist="ops-scratchpad" g-scope="Scratchpad">
        <div class="scratchpad-panel" *if="open" g-transition>
            <div class="scratchpad-head"><strong>Ops scratchpad</strong><span>{elapsed}</span></div>
            <textarea g-model="note" placeholder="Type a note, then navigate. This exact DOM island survives."></textarea>
            <div class="scratchpad-foot">{note.length} characters · persisted DOM</div>
        </div>
        <button class="scratchpad-toggle" type="button" @click="open = !open">{open ? 'Close notes' : 'Open ops notes'}</button>
    </section>
</div>
</body>
</html>
