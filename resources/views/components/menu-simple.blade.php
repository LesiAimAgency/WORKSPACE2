@if($menu && $menu->items->count() > 0)
<ul class="main-menu">
    @foreach($menu->items as $item)
    <li class="menu-item {{ $item->children->count() > 0 ? 'has-children' : '' }}">
        <a href="{{ menu_item_url($item) }}" target="{{ $item->target ?? '_self' }}" class="menu-link">
            {{ $item->title }}
        </a>
        @if($item->children->count() > 0)
        <ul class="sub-menu">
            @foreach($item->children as $child)
            <li class="menu-item">
                <a href="{{ menu_item_url($child) }}" target="{{ $child->target ?? '_self' }}" class="menu-link">
                    {{ $child->title }}
                </a>
            </li>
            @endforeach
        </ul>
        @endif
    </li>
    @endforeach
</ul>
@endif
