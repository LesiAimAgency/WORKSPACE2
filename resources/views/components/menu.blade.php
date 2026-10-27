@if($menu && $menu->items->count() > 0)
<nav class="main-navigation" data-menu-location="{{ $location }}">
    {!! $renderItems($menu->items) !!}
</nav>

<style>
.main-navigation .main-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 2rem;
}

.main-navigation .menu-item {
    position: relative;
}

.main-navigation .menu-link {
    display: block;
    padding: 0.75rem 1rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
}

.main-navigation .menu-link:hover {
    color: #98191F;
}

.main-navigation .has-children > .menu-link::after {
    content: '▼';
    font-size: 0.75rem;
    margin-left: 0.5rem;
    transition: transform 0.3s ease;
}

.main-navigation .sub-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    padding: 0.5rem 0;
    min-width: 200px;
    list-style: none;
    margin: 0;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.main-navigation .has-children:hover .sub-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.main-navigation .sub-menu .menu-link {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.main-navigation .sub-menu .menu-item:last-child .menu-link {
    border-bottom: none;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .main-navigation .main-menu {
        flex-direction: column;
        gap: 0;
    }
    
    .main-navigation .sub-menu {
        position: static;
        box-shadow: none;
        background: #f9fafb;
        margin-left: 1rem;
        opacity: 1;
        visibility: visible;
        transform: none;
    }
}
</style>
@endif
