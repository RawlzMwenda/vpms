<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside id="left-panel" class="left-panel">
    <nav class="navbar navbar-expand-sm navbar-default">
        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                    <a href="dashboard.php"><i class="menu-icon fa fa-laptop"></i>Dashboard </a>
                </li>
                
                <li class="<?php echo ($currentPage == 'book-space.php') ? 'active' : ''; ?>">
                    <a href="book-space.php"> <i class="menu-icon ti-truck"></i>Book a space </a>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
</aside>
