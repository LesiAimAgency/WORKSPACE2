{{-- MODIFIED: 2025-01-21 --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    @php
        $adminFavicon = setting_string('site_favicon') ?: setting_string('seo_favicon');
    @endphp
    @if(!empty($adminFavicon))
        @php
            $adminFaviconUrl = Str::startsWith($adminFavicon, ['http://', 'https://']) ? $adminFavicon : asset(ltrim($adminFavicon, '/'));
        @endphp
        <link rel="icon" type="image/x-icon" href="{{ $adminFaviconUrl }}">
        <link rel="shortcut icon" href="{{ $adminFaviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $adminFaviconUrl }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('vendor/ckeditor5/ckeditor5.css') }}">
    <style>
        /* CKEditor UI & Wrapper Styling */
        :root {
            --ck-z-default: 1;
            --ck-z-panel: 20;
            --ck-z-dialog: 50;
        }
        .ckeditor-container,
        .ck.ck-editor {
            position: relative;
            z-index: 1;
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .ck.ck-editor:focus-within {
            z-index: 10;
        }
        .ck.ck-editor .ck-editor__top .ck-sticky-panel .ck-toolbar {
            z-index: 10 !important;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            border-color: #d1d5db;
            background-color: #f9fafb;
        }
        .ck-editor__editable {
            font-size: 15px;
            color: #1f2937;
            background-color: #ffffff;
            line-height: 1.65;
        }
        .ck-editor__editable_inline {
            min-height: 200px;
            padding: 1rem 1.25rem;
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: #d1d5db;
        }
        .ck-editor__editable:focus,
        .ck-editor__editable.ck-focused {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }
        .ck.ck-balloon-panel,
        .ck.ck-dropdown__panel,
        .ck-body-wrapper {
            z-index: 30 !important;
        }
        .ck-source-editing-area textarea {
            min-height: 240px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 13px;
            line-height: 1.6;
            padding: 1rem;
            color: #0f172a;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 0.375rem;
        }

        /* Fix Tailwind Preflight CSS Reset inside CKEditor content */
        .ck-content h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
            line-height: 1.3;
            color: #111827;
        }
        .ck-content h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
            line-height: 1.35;
            color: #111827;
        }
        .ck-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            line-height: 1.4;
            color: #1f2937;
        }
        .ck-content h4 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-top: 0.75rem;
            margin-bottom: 0.5rem;
            color: #374151;
        }
        .ck-content p {
            margin-bottom: 0.75rem;
            line-height: 1.65;
        }
        .ck-content ul {
            list-style-type: disc;
            margin: 0.75rem 0 0.75rem 1.5rem;
            padding-left: 0.5rem;
        }
        .ck-content ol {
            list-style-type: decimal;
            margin: 0.75rem 0 0.75rem 1.5rem;
            padding-left: 0.5rem;
        }
        .ck-content li {
            margin-bottom: 0.25rem;
            display: list-item;
        }
        .ck-content blockquote {
            border-left: 4px solid #2563eb;
            background-color: #f8fafc;
            padding: 0.75rem 1rem;
            margin: 1rem 0;
            color: #4b5563;
            font-style: italic;
            border-radius: 0 0.375rem 0.375rem 0;
        }
        .ck-content figure.table {
            margin: 1rem 0;
            width: 100%;
        }
        .ck-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.5rem 0;
        }
        .ck-content table th,
        .ck-content table td {
            border: 1px solid #d1d5db;
            padding: 0.5rem 0.75rem;
            vertical-align: top;
        }
        .ck-content table th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #111827;
        }
        .ck-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
            margin: 0.5rem 0;
        }
        .ck-content strong, .ck-content b {
            font-weight: 700;
        }
        .ck-content em, .ck-content i {
            font-style: italic;
        }
        .ck-content a {
            color: #2563eb;
            text-decoration: underline;
        }
    </style>
    <script type="importmap">
    {
        "imports": {
            "ckeditor5": "{{ asset('vendor/ckeditor5/ckeditor5.js') }}",
            "ckeditor5/": "{{ asset('vendor/ckeditor5') }}/"
        }
    }
    </script>
    @livewireStyles
    @stack('styles')
    @stack('head-scripts')
</head>
<body class="bg-gray-100" x-data="{ showAlert: false, alertMessage: '', alertType: 'success' }">
    <!-- Global Alert -->
    <div x-show="showAlert" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 z-50 max-w-md"
         style="display: none;">
        <div :class="{
            'bg-green-50 border-green-500 text-green-800': alertType === 'success',
            'bg-red-50 border-red-500 text-red-800': alertType === 'error',
            'bg-blue-50 border-[#98191F] text-blue-800': alertType === 'info',
            'bg-yellow-50 border-yellow-500 text-yellow-800': alertType === 'warning'
        }" class="border-l-4 p-4 rounded-lg shadow-lg flex items-start gap-3">
            <svg x-show="alertType === 'success'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="alertType === 'error'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="alertType === 'info'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="alertType === 'warning'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-medium" x-text="alertMessage"></p>
            </div>
            <button @click="showAlert = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    @php
        $rawProject = $currentProject ?? (request()->attributes->get('project') ?? (function_exists('current_project') ? current_project() : session('current_project')));
        if (is_string($rawProject) && class_exists(\App\Models\Project::class)) {
            $currentProject = \App\Models\Project::where('code', $rawProject)->orWhere('id', $rawProject)->first()
                ?? ((object) ['code' => $rawProject, 'name' => $rawProject, 'id' => session('current_project_id')]);
        } elseif (is_array($rawProject)) {
            $currentProject = (object) $rawProject;
        } else {
            $currentProject = $rawProject;
        }
        $projectCode = is_object($currentProject) ? ($currentProject->code ?? 'inbetween_v2') : (is_string($currentProject) ? $currentProject : 'inbetween_v2');
        $hasCustomSidebar = true;
    @endphp

    <div class="min-h-screen flex w-full">
        <div class="fixed top-0 left-0 h-screen z-40">
            @include('admin.layouts.sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-72 min-w-0">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                        <div class="ml-4 px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                            Online
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        
                        
                        <!-- User Menu -->
                        @php
                            $currentUser = $authUser ?? auth()->user();
                        @endphp
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $currentUser->name ?? 'User' }}</p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                            <div class="h-8 w-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr($currentUser->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6 bg-gray-50 min-w-0">
                @if(session('alert'))
                    <div class="mb-4 p-4 rounded-lg {{ session('alert')['type'] === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : (session('alert')['type'] === 'warning' ? 'bg-yellow-100 border border-yellow-400 text-yellow-700' : 'bg-green-100 border border-green-400 text-green-700') }}" 
                         x-data="{ show: true }" 
                         x-show="show"
                         x-transition>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if(session('alert')['type'] === 'error')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif(session('alert')['type'] === 'warning')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                <span class="font-medium">{{ session('alert')['message'] }}</span>
                            </div>
                            <button @click="show = false" class="text-current opacity-70 hover:opacity-100">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

<script>
// Global Alert Function
window.showAlert = function(message, type = 'success') {
    const event = new CustomEvent('show-alert', {
        detail: { message, type }
    });
    window.dispatchEvent(event);
};

document.addEventListener('alpine:init', () => {
    window.addEventListener('show-alert', (e) => {
        const body = document.querySelector('body');
        if (body.__x) {
            body.__x.$data.alertMessage = e.detail.message;
            body.__x.$data.alertType = e.detail.type;
            body.__x.$data.showAlert = true;
            
            setTimeout(() => {
                body.__x.$data.showAlert = false;
            }, 3000);
        }
    });
});

// Sidebar Toggle
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarTexts = document.querySelectorAll('.sidebar-text, .nav-text');
const navIcons = document.querySelectorAll('.nav-icon');
const mainContent = document.querySelector('.flex-1.flex.flex-col.ml-72') || document.querySelector('.flex-1.flex.flex-col');

if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('w-72');
        sidebar.classList.toggle('w-16');
        
        if (mainContent) {
            mainContent.classList.toggle('ml-72');
            mainContent.classList.toggle('ml-16');
        }
        
        sidebarTexts.forEach(text => {
            text.classList.toggle('hidden');
        });
        
        navIcons.forEach(icon => {
            icon.classList.toggle('mr-3');
            icon.classList.toggle('mx-auto');
        });
    });
}

// Dropdown Menus
document.querySelectorAll('.dropdown-parent').forEach(parent => {
    const button = parent.querySelector('button');
    const menu = parent.querySelector('.dropdown-menu');
    const arrow = parent.querySelector('.dropdown-arrow');
    
    button.addEventListener('click', function() {
        const isOpen = menu.style.maxHeight && menu.style.maxHeight !== '0px';
        
        // Close all other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
            if (otherMenu !== menu) {
                otherMenu.style.maxHeight = '0px';
                otherMenu.parentElement.querySelector('.dropdown-arrow').style.transform = 'rotate(0deg)';
            }
        });
        
        if (isOpen) {
            menu.style.maxHeight = '0px';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            menu.style.maxHeight = menu.scrollHeight + 'px';
            arrow.style.transform = 'rotate(180deg)';
        }
    });
});

// Global Media URL Formatter
window.formatMediaUrl = function(url) {
    if (!url) return '';
    url = url.toString().trim();
    if (!url) return '';
    if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//') || url.startsWith('data:') || url.startsWith('blob:')) {
        return url;
    }
    const base = '{{ rtrim(asset('/'), '/') }}';
    return base + '/' + url.replace(/^\/+/, '');
};

// Global Repeatable Field Functions (for widget field rendering)
function addRepeatableItem(fieldId, fieldName) {
    const container = document.getElementById(fieldId + '_container');
    if (!container) {
        console.error('Container not found:', fieldId + '_container');
        return;
    }
    
    const maxItemsAttr = container.parentElement?.dataset?.maxItems;
    const maxItems = (maxItemsAttr !== undefined && maxItemsAttr !== '' && maxItemsAttr !== null) ? parseInt(maxItemsAttr, 10) : 0;
    const currentItems = container.children.length;

    if (maxItems > 0 && currentItems >= maxItems) {
        alert('Tối đa ' + maxItems + ' mục');
        return;
    }

    // Get template from data attribute or create basic one
    const template = container.dataset.template || '';
    if (!template) {
        console.error('No template found for repeatable field');
        return;
    }
    
    const index = currentItems;
    let itemHtml = template.replace(/__INDEX__/g, index);
    // Fix the display text
    itemHtml = itemHtml.replace(/Mục __INDEX__/g, 'Mục ' + (index + 1));
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    
    // Auto-select unoccupied position/zone for fields having position select
    const newItem = container.lastElementChild;
    if (newItem) {
        const posSelect = newItem.querySelector('select[name*="[position]"]');
        if (posSelect) {
            const usedPositions = new Set();
            container.querySelectorAll('.repeatable-item').forEach(el => {
                if (el !== newItem) {
                    const sel = el.querySelector('select[name*="[position]"]');
                    if (sel && sel.value && sel.value !== 'auto') {
                        usedPositions.add(sel.value.toString());
                    }
                }
            });
            for (let i = 1; i <= 15; i++) {
                if (!usedPositions.has(i.toString())) {
                    posSelect.value = i.toString();
                    break;
                }
            }
        }
    }

    // Initialize any new TinyMCE editors
    if (typeof window.initTinyMCE === 'function') {
        window.initTinyMCE();
    }
    if (typeof window.initCKEditor === 'function') {
        window.initCKEditor(newItem);
    }

    // Initialize Alpine.js on new content if available
    if (window.Alpine) {
        Alpine.initTree(newItem);
    }
    
    // Re-index all items & re-init sortable
    reindexRepeatableItems(container, fieldName);
    initRepeatableSortable(container.parentElement);
}

function removeRepeatableItem(button) {
    const item = button.closest('.repeatable-item');
    const container = button.closest('.repeatable-field');
    
    if (!container) {
        item?.remove();
        return;
    }
    
    const minItems = parseInt(container.dataset?.minItems || 0);
    const currentItems = container.querySelectorAll('.repeatable-item').length;

    if (currentItems <= minItems) {
        alert('Tối thiểu ' + minItems + ' mục');
        return;
    }

    // Get field name from container's data or first input
    const firstInput = container.querySelector('input[name], select[name], textarea[name]');
    let fieldName = '';
    if (firstInput) {
        const match = firstInput.name.match(/^([^\[]+)/);
        if (match) fieldName = match[1];
    }

    const itemsContainer = container.querySelector('[id$="_container"]');
    item.remove();
    
    // Re-index remaining items & re-init sortable
    if (itemsContainer) {
        reindexRepeatableItems(itemsContainer, fieldName);
        initRepeatableSortable(container);
    }
}

function reindexRepeatableItems(container, fieldName) {
    if (!container) return;

    if (!fieldName) {
        const firstInput = container.querySelector('input[name], select[name], textarea[name]');
        if (firstInput) {
            const match = firstInput.name.match(/^([^\[]+)/);
            if (match) fieldName = match[1];
        }
    }

    const items = Array.from(container.children).filter(el => el.classList.contains('repeatable-item'));
    items.forEach((item, index) => {
        item.dataset.index = index;

        // Update header title without removing SVG arrow or custom subtitles
        const titleEl = item.querySelector('.repeatable-item-title');
        if (titleEl) {
            titleEl.textContent = 'Mục ' + (index + 1);
        } else {
            const header = item.querySelector('h4');
            if (header) {
                const svg = header.querySelector('svg');
                header.innerHTML = (svg ? svg.outerHTML + ' ' : '') + 'Mục ' + (index + 1);
            }
        }

        if (fieldName) {
            // Update all input, select, textarea names and IDs
            const formElements = item.querySelectorAll('input, select, textarea');
            formElements.forEach(input => {
                if (input.name) {
                    const regex = new RegExp(`^${fieldName}\\[[^\\]]+\\]`);
                    if (regex.test(input.name)) {
                        input.name = input.name.replace(regex, `${fieldName}[${index}]`);
                    }
                }
                if (input.id) {
                    const regexId = new RegExp(`^field_${fieldName}\\[[^\\]]+\\]`);
                    if (regexId.test(input.id)) {
                        input.id = input.id.replace(regexId, `field_${fieldName}[${index}]`);
                    }
                }
            });

            // Update Alpine.js component states (e.g. ImageField fieldName)
            item.querySelectorAll('[x-data]').forEach(el => {
                try {
                    if (window.Alpine) {
                        const data = window.Alpine.$data(el);
                        if (data && data.fieldName) {
                            const regex = new RegExp(`^${fieldName}\\[[^\\]]+\\]`);
                            if (regex.test(data.fieldName)) {
                                data.fieldName = data.fieldName.replace(regex, `${fieldName}[${index}]`);
                            }
                        }
                    }
                } catch(e) {}
            });
        }
    });
}

function initRepeatableSortable(scope) {
    if (typeof Sortable === 'undefined') {
        return;
    }
    const root = scope || document;
    const containers = root.querySelectorAll('.repeatable-field [id$="_container"]');

    containers.forEach(function(container) {
        if (container._sortable) {
            container._sortable.destroy();
        }

        container._sortable = new Sortable(container, {
            handle: '.repeatable-drag-handle',
            animation: 180,
            ghostClass: 'bg-blue-50/80',
            chosenClass: 'bg-blue-100/70',
            dragClass: 'shadow-xl',
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: function(evt) {
                if (evt.oldIndex === evt.newIndex) return;

                const firstInput = container.querySelector('input[name], select[name], textarea[name]');
                let fieldName = '';
                if (firstInput) {
                    const match = firstInput.name.match(/^([^\[]+)/);
                    if (match) fieldName = match[1];
                }

                reindexRepeatableItems(container, fieldName);
                container.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
}
window.initRepeatableSortable = initRepeatableSortable;
window.reindexRepeatableItems = reindexRepeatableItems;

document.addEventListener('DOMContentLoaded', function() {
    initRepeatableSortable();
});
document.addEventListener('widget-form-loaded', function() {
    initRepeatableSortable();
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
window.initTinyMCE = function() {
    if (typeof tinymce !== 'undefined') {
        const uninitialized = document.querySelectorAll('.tinymce-editor:not([data-tinymce-initialized])');
        if (uninitialized.length === 0) return;
        
        uninitialized.forEach(el => {
            el.setAttribute('data-tinymce-initialized', 'true');
            const editorHeight = parseInt(el.getAttribute('data-height')) || 380;
            
            tinymce.init({
                target: el,
                height: editorHeight,
                menubar: 'file edit view insert format tools table',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media code | fullscreen',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; padding: 10px; }',
                branding: false,
                promotion: false,
                setup: function (editor) {
                    editor.on('change keyup blur', function () {
                        editor.save();
                    });
                }
            });
        });
    }
};

document.addEventListener('DOMContentLoaded', function() {
    window.initTinyMCE();
});
</script>

<script type="module">
import {
    ClassicEditor,
    Plugin,
    ButtonView,
    icons,
    Essentials,
    Bold,
    Italic,
    Font,
    Paragraph,
    Heading,
    Link,
    List,
    BlockQuote,
    Image,
    ImageToolbar,
    ImageUpload,
    ImageCaption,
    ImageStyle,
    ImageResize,
    Table,
    TableToolbar,
    MediaEmbed,
    Alignment,
    Indent,
    Underline,
    Strikethrough,
    Code,
    Subscript,
    Superscript,
    RemoveFormat,
    SourceEditing,
    GeneralHtmlSupport
} from 'ckeditor5';

class CmsMediaPickerPlugin extends Plugin {
    static get pluginName() {
        return 'CmsMediaPicker';
    }

    init() {
        const editor = this.editor;
        editor.ui.componentFactory.add('cmsMediaPicker', (locale) => {
            const view = new ButtonView(locale);
            view.set({
                label: 'Chèn ảnh từ thư viện Media',
                icon: icons.image,
                tooltip: true
            });

            view.on('execute', () => {
                window.activeCKEditorForMedia = editor;
                window.dispatchEvent(new CustomEvent('open-media-picker', {
                    detail: {
                        field: 'ckeditor',
                        multiple: true,
                        mediaType: 'image'
                    }
                }));
            });

            return view;
        });
    }
}

window.ClassicEditor = ClassicEditor;
window.CKEditorPlugins = [
    CmsMediaPickerPlugin,
    Essentials, Bold, Italic, Font, Paragraph, Heading, Link, List, BlockQuote,
    Image, ImageToolbar, ImageUpload, ImageCaption, ImageStyle, ImageResize,
    Table, TableToolbar, MediaEmbed, Alignment, Indent,
    Underline, Strikethrough, Code, Subscript, Superscript, RemoveFormat,
    SourceEditing, GeneralHtmlSupport
];
window.dispatchEvent(new CustomEvent('ckeditor-module-ready'));
</script>

<script>
window.activeCKEditors = window.activeCKEditors || {};

// Client-side HTML Sanitizer: Strictly allow CSS & HTML, block all JavaScript (<script>, on* events, javascript: URLs)
window.sanitizeEditorHtml = function (html) {
    if (!html || typeof html !== 'string') return '';
    var clean = html;
    // 1. Remove script tags and content within them
    clean = clean.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
    // 2. Remove any remaining stray script tags
    clean = clean.replace(/<\/?script\b[^>]*>/gi, '');
    // 3. Remove dangerous executable/embed tags
    clean = clean.replace(/<\/?(iframe|object|embed|applet|noscript|base|meta)\b[^>]*>/gi, '');
    // 4. Remove inline event handlers (onclick, onload, onerror, onmouseover, onfocus, etc.)
    clean = clean.replace(/\s+on[a-zA-Z]+\s*=\s*(["'])[\s\S]*?\1/gi, '');
    clean = clean.replace(/\s+on[a-zA-Z]+\s*=\s*[^ >\t\r\n]+/gi, '');
    // 5. Remove javascript: pseudo-protocol in URLs
    clean = clean.replace(/(href|src|action|data)\s*=\s*(["'])\s*javascript:[^"'\r\n]*\2/gi, '$1="#"');
    clean = clean.replace(/(href|src|action|data)\s*=\s*javascript:[^ >\t\r\n]+/gi, '$1="#"');
    return clean;
};

window.destroyCKEditors = function () {
    if (window.activeCKEditors) {
        Object.keys(window.activeCKEditors).forEach(function (key) {
            try {
                if (window.activeCKEditors[key]) {
                    window.activeCKEditors[key].destroy();
                }
            } catch (e) {}
            delete window.activeCKEditors[key];
        });
    }
};

window.syncCKEditors = function () {
    if (window.activeCKEditors) {
        Object.keys(window.activeCKEditors).forEach(function (key) {
            try {
                var inst = window.activeCKEditors[key];
                var textarea = document.getElementById(key);
                if (inst && textarea) {
                    var data = inst.getData();
                    textarea.value = typeof window.sanitizeEditorHtml === 'function' ? window.sanitizeEditorHtml(data) : data;
                }
            } catch (e) {}
        });
    }
};

window.initCKEditor = function (container, attempts) {
    attempts = attempts || 0;
    container = container || document;

    if (!window.ClassicEditor || !window.CKEditorPlugins) {
        if (attempts < 100) {
            setTimeout(function () {
                window.initCKEditor(container, attempts + 1);
            }, 100);
        }
        return;
    }

    var targets = container.querySelectorAll('.ckeditor-editor, textarea.ckeditor, [data-editor="ckeditor"]');
    targets.forEach(function (el) {
        if (!el.id) {
            el.id = 'ckeditor_' + Math.random().toString(36).substring(2, 9);
        }
        if (window.activeCKEditors[el.id]) {
            return;
        }

        var minHeight = el.getAttribute('data-height') || '200px';

        window.ClassicEditor
            .create(el, {
                plugins: window.CKEditorPlugins,
                toolbar: {
                    items: [
                        'undo', 'redo', '|',
                        'sourceEditing', '|',
                        'heading', '|',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'alignment', '|',
                        'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                        'link', 'cmsMediaPicker', 'insertTable', 'blockQuote', 'mediaEmbed', '|',
                        'code', 'removeFormat'
                    ],
                    shouldNotGroupWhenFull: true
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Tiêu đề 1 (H1)', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Tiêu đề 2 (H2)', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Tiêu đề 3 (H3)', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Tiêu đề 4 (H4)', class: 'ck-heading_heading4' }
                    ]
                },
                // Chỉ cho phép định dạng HTML & CSS Styling/Classes; Triệt để chặn/loại bỏ JavaScript
                htmlSupport: {
                    allow: [
                        {
                            name: /.*/,
                            attributes: true,
                            classes: true,
                            styles: true
                        }
                    ],
                    disallow: [
                        {
                            name: /^(script|iframe|embed|object|noscript|applet|meta|link)$/i
                        },
                        {
                            name: /.*/,
                            attributes: {
                                key: /^on/i
                            }
                        },
                        {
                            name: /.*/,
                            attributes: {
                                key: /^(href|src|action|data)$/i,
                                value: /^javascript:/i
                            }
                        }
                    ]
                },
                image: {
                    toolbar: [
                        'imageStyle:inline',
                        'imageStyle:block',
                        'imageStyle:side',
                        '|',
                        'toggleImageCaption',
                        'imageTextAlternative'
                    ]
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells'
                    ]
                }
            })
            .then(function (editor) {
                window.activeCKEditors[el.id] = editor;

                var editable = editor.ui.view.editable.element;
                if (editable) {
                    editable.style.minHeight = minHeight;
                }

                editor.model.document.on('change:data', function () {
                    var rawData = editor.getData();
                    var cleanData = typeof window.sanitizeEditorHtml === 'function' ? window.sanitizeEditorHtml(rawData) : rawData;
                    el.value = cleanData;
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                    window.dispatchEvent(new CustomEvent('ckeditor-change', { detail: { id: el.id, value: cleanData } }));
                });
            })
            .catch(function (err) {
                console.error('CKEditor init error on #' + el.id, err);
            });
    });
};

document.addEventListener('DOMContentLoaded', function () {
    window.initCKEditor(document);
});

window.addEventListener('ckeditor-module-ready', function () {
    window.initCKEditor(document);
});

// Auto-sync all CKEditors before ANY form submits
document.addEventListener('submit', function (e) {
    if (typeof window.syncCKEditors === 'function') {
        window.syncCKEditors();
    }
}, true);

// Handle media picker selection for CKEditor
window.addEventListener('media-selected', function (e) {
    if (e.detail && e.detail.field === 'ckeditor') {
        var editor = window.activeCKEditorForMedia;
        if (!editor) return;

        var rawUrls = e.detail.urls;
        if (!rawUrls) return;
        var urls = Array.isArray(rawUrls) ? rawUrls : [rawUrls];

        urls = urls.map(function (u) {
            if (typeof u !== 'string') return '';
            u = u.trim();
            if (!u) return '';
            if (!u.startsWith('http://') && !u.startsWith('https://') && !u.startsWith('/')) {
                return '/' + u;
            }
            return u;
        }).filter(Boolean);

        if (urls.length === 0) return;

        try {
            editor.editing.view.focus();
        } catch (fErr) {}

        urls.forEach(function (url) {
            try {
                if (editor.commands.get('insertImage') && editor.commands.get('insertImage').isEnabled) {
                    editor.execute('insertImage', { source: url });
                } else {
                    editor.model.change(function (writer) {
                        var imageElement = writer.createElement('imageBlock', { src: url });
                        editor.model.insertContent(imageElement, editor.model.document.selection);
                    });
                }
            } catch (err) {
                console.error('Failed to insert image into CKEditor:', err);
                try {
                    editor.model.change(function (writer) {
                        var imageElement = writer.createElement('imageBlock', { src: url });
                        editor.model.insertContent(imageElement, editor.model.document.selection);
                    });
                } catch (modelErr) {
                    console.error('Fallback image insertion error:', modelErr);
                }
            }
        });
    }
});
</script>
@if(!request()->routeIs('admin.media.*'))
    @include('cms.components.media-manager', ['hideTrigger' => true])
@endif
@livewireScripts
@stack('scripts')
</body>
</html>



