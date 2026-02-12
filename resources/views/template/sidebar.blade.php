<ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item active">
        <a href="{{ route('dashboard') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="{{ route('product.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-package"></i>
            <div data-i18n="Products">Data Produk</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="{{ route('blog.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-package"></i>
            <div data-i18n="Products">Blog</div>
        </a>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Halaman Homepage</span>
    </li>
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-collection"></i>
            <div data-i18n="Account Settings">Halaman Homepage</div>
        </a>
        <ul class="menu-sub">

            <li class="menu-item">
                <a href="{{ route('hero.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Hero Section">Hero Section</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('about.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Hero Section">About Section</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('quality.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Hero Section">Quality Section</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('gallery.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Hero Section">Gallery Section</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('why_choose.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Hero Section">why choose us</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('certificate.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Hero Section">Certifikat Section</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('contact.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Hero Section">Contact Section</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('footer.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Hero Section">Footer Section</div>
                </a>
            </li>
        </ul>
    </li>




</ul>
