<link rel="stylesheet" href="./css/sidebar.css">

<nav class="sidebar">
    <a href="dashboard.php" class="brand">
        <div class="brand-logo">
            <i class="fas fa-egg"></i>
        </div>
        EggTrack
    </a>

    <button class="sidebar-close">
        <i class="fas fa-times"></i>
    </button>

    <ul class="nav-menu">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="inventory.php" class="nav-link">
                <i class="fas fa-warehouse"></i>
                Inventory
            </a>
        </li>
        <li class="nav-item">
            <a href="analytics.php" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                Analytics
            </a>
        </li>
        <li class="nav-item">
            <a href="expenses.php" class="nav-link">
                <i class="fas fa-coins"></i>
                Expenses
            </a>
        </li>
        <li class="nav-item">
            <a href="sales.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i>
                Sales
            </a>
        </li>
        <li class="nav-item">
            <a href="return-stocks.php" class="nav-link">
                <i class="fas fa-undo"></i>
                Return Stocks
            </a>
        </li>
        <li class="nav-item">
            <a href="consumer_management.php" class="nav-link">
                <i class="fas fa-truck"></i>
                Consumer Management
            </a>
        </li>
        <li class="nav-item has-submenu">
            <a href="#" class="nav-link" onclick="toggleUserManagement(event)">
                <i class="fa fa-user-circle"></i> User Management
                <i class="fas fa-chevron-down submenu-icon"></i>
            </a>
            <ul class="nav-submenu bg-light">
                <li><a class="nav-sublink" href="loginhistory.php"><i class="fas fa-clock"></i> Login History</a></li>
                <li><a class="nav-sublink" href="user_management.php"><i class="fas fa-user"></i> User Accounts</a></li>
                <li><a class="nav-sublink" href="admin-accounts.php"><i class="fas fa-user-shield"></i> Admin Accounts</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a href="activity-log.php" class="nav-link">
                <i class="fas fa-history"></i>
                Activity Log
            </a>
        </li>
    </ul>
</nav>

<button class="menu-toggler">
    <i class="fas fa-bars"></i>
</button>

<script src="./js/sidebar.js"></script>
<script src="./js/navbar.js"></script>