<?php
$role = $_SESSION['role'] ?? '';
$department_name = $_SESSION['department_name'] ?? 'No Department';
?>

<div class="fixed left-0 top-0 h-screen w-64 bg-[#07152B] text-white p-6 overflow-y-auto hidden md:block">

    <h2 class="text-2xl font-bold text-[#d4af37] mb-2">
        Alim Ops V2
    </h2>

    <p class="text-xs text-gray-300 mb-8">
        <?php echo ucfirst($role); ?> 
        <br>
        <?php echo htmlspecialchars($department_name); ?>
    </p>

    <nav>

        <a class="sidebar-link" href="../dashboard/index.php">
            Dashboard
        </a>

        <?php if (in_array($role, ['chairman', 'admin', 'storekeeper'])): ?>

            <p class="text-xs uppercase text-gray-400 mt-6 mb-3">
                Inventory
            </p>

            <a class="sidebar-link" href="../inventory/products.php">
                Products
            </a>

            <a class="sidebar-link" href="../inventory/add_product.php">
                Add Product
            </a>

            <a class="sidebar-link" href="../inventory/stock_in.php">
                Stock In
            </a>

            <a class="sidebar-link" href="../inventory/issue_stock.php">
                Issue Stock
            </a>

        <?php endif; ?>


        <?php if (in_array($role, ['chairman', 'admin', 'department_user'])): ?>

            <p class="text-xs uppercase text-gray-400 mt-6 mb-3">
                Department
            </p>

            <a class="sidebar-link" href="../departments/pending.php">
                Pending Receipts
            </a>

            <a class="sidebar-link" href="../departments/stock.php">
                Department Stock
            </a>
            <a class="sidebar-link" href="../departments/use_stock.php">
                Use Stock
            </a>
            <a class="sidebar-link" href="../departments/sales.php">
                Department Sales
            </a>
            

        <?php endif; ?>


        <?php if (in_array($role, ['chairman', 'admin', 'accountant'])): ?>

            <p class="text-xs uppercase text-gray-400 mt-6 mb-3">
                Finance
            </p>

            <a class="sidebar-link" href="../expenses/add.php">
                Add Expense
            </a>

            <a class="sidebar-link" href="../expenses/reports.php">
                Expense Reports
            </a>
            <a class="sidebar-link" href="../reports/usage_history.php">
              Usage History
            </a>
            

        <?php endif; ?>


        <?php if (in_array($role, ['chairman', 'admin', 'auditor', 'accountant'])): ?>

            <p class="text-xs uppercase text-gray-400 mt-6 mb-3">
                Reports
            </p>

            <a class="sidebar-link" href="../reports/stock_movements.php">
                Stock Reports
            </a>

        <?php endif; ?>


        <?php if (in_array($role, ['chairman', 'admin', 'auditor'])): ?>

            <p class="text-xs uppercase text-gray-400 mt-6 mb-3">
                System
            </p>

            <a class="sidebar-link" href="../logs/activity.php">
                Activity Logs
            </a>

        <?php endif; ?>

        <a class="sidebar-link mt-8 bg-red-600 hover:bg-red-700" href="../auth/logout.php">
            Logout
        </a>

    </nav>

</div>