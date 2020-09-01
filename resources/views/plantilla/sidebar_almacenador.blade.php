<div class="sidebar">
        <nav class="sidebar-nav">
            <ul class="nav">
                <li @click="menu=0" class="nav-item">
                    <a class="nav-link active" href=""><i class="icon-speedometer"></i> Escritorio</a>
                </li>
                <li class="nav-title">
                    MENU PARA BODEGUERO
                </li>
                <li @click="menu=3" class="nav-item">
                    <a class="nav-link" href="#"><i class="icon-wallet"></i> Ingresos</a>
                </li>
                <li @click="menu=15" class="nav-item">
                    <a class="nav-link" href="#"><i class="icon-wallet"></i> Compras</a>
                </li>
                <li @click="menu=4" class="nav-item">
                    <a class="nav-link" href="#"><i class="icon-notebook"></i> Proveedores</a>
                </li>
            </ul>
        </nav>
        <button class="sidebar-minimizer brand-minimizer" type="button"></button>
    </div>