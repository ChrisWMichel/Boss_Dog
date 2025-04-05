@props(['categorySorting'])
<div class="mt-16">
    <div {{ $attributes->merge(['class' => 'category-list flex text-white bg-slate-600']) }}>
        @if (!empty($categorySorting))
            @foreach ($categorySorting as $category)
                <div class="relative pt-2 category-item">
                    <a href="{{ route('by-category', $category) }}"
                        class="block px-6 py-3 cursor-pointer hover:bg-slate-800">
                        {{ $category->name }}
                    </a>
                    @if (!empty($category->children))
                        <div class="absolute left-0 z-50 flex-col hidden text-white top-full sub-menu bg-slate-600">
                            @foreach ($category->children as $child)
                                <a href="{{ route('by-category', $child) }}"
                                    class="block px-6 py-3 cursor-pointer hover:bg-slate-700">
                                    {{ $child['name'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                </div>
            @endforeach
        @endif
    </div>
</div>
<style>
    .sub-menu {
        opacity: 0;
        transform: translateY(-10px);
        position: absolute;
        pointer-events: none;
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    .category-item:hover>.sub-menu {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
        z-index: 100;
    }
</style>
<script>
    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('mouseenter', () => {
            const subMenu = item.querySelector('.sub-menu');
            if (subMenu) {
                subMenu.style.display = 'flex';
                setTimeout(() => {
                    subMenu.style.opacity = '1';
                    subMenu.style.transform = 'translateY(0)';
                }, 10);
            }
        });

        item.addEventListener('mouseleave', () => {
            const subMenu = item.querySelector('.sub-menu');
            if (subMenu) {
                subMenu.style.opacity = '0';
                subMenu.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    subMenu.style.display = 'none';
                }, 300);
            }
        });

        // Child Menu Animation
        item.querySelectorAll('.sub-menu .category-item').forEach(childItem => {
            childItem.addEventListener('mouseenter', () => {
                const childSubMenu = childItem.querySelector('.sub-menu');
                if (childSubMenu) {
                    childSubMenu.style.display = 'flex';
                    setTimeout(() => {
                        childSubMenu.style.opacity = '1';
                        childSubMenu.style.transform = 'translateY(0)';
                    }, 10);
                }
            });

            childItem.addEventListener('mouseleave', () => {
                const childSubMenu = childItem.querySelector('.sub-menu');
                if (childSubMenu) {
                    childSubMenu.style.opacity = '0';
                    childSubMenu.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        childSubMenu.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
</script>
