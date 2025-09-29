    
<nav id="sidebar" class="active">
    <div class="p-3 position-sticky top-0">
        <a class="navbar-brand d-flex align-items-end mb-4" href="<?= __PATH__ ?>">
            <h1 class="fw-bold lh-1 m-0">GG</h1>
            <small class="lh-1 fw-bold text-secondary">v4</small>
        </a>

        <div class="mb-3">
            <input type="text" class="form-control input-dark" id="search-sidebar" placeholder="Pesquisar no menu" onkeyup="searchMenu(this.value);" />
        </div>

        <ul class="navbar-nav justify-content-end flex-grow-1">
            <?php include dirname(__FILE__) . "/component.menu.php"; ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center logout" href="javascript:;" aria-label="Sair">
                    <i class="ti ti-logout"></i>
                    Sair
                </a>
            </li>
        </ul>

    </div>
</nav>