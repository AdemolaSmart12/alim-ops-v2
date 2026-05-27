<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

function getSingleValue($conn, $query) {
    $result = mysqli_query($conn, $query);

    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_assoc($result);

    return $row['total'] ?? 0;
}

$role = $_SESSION['role'] ?? '';
$department_id = $_SESSION['department_id'] ?? null;
$department_name = $_SESSION['department_name'] ?? 'No Department';

$is_department_user = ($role === 'department_user');

if ($is_department_user && $department_id) {

    $total_department_items = getSingleValue($conn, "
        SELECT COUNT(*) AS total
        FROM department_stock
        WHERE department_id = '$department_id'
    ");

    $total_department_qty = getSingleValue($conn, "
        SELECT COALESCE(SUM(quantity), 0) AS total
        FROM department_stock
        WHERE department_id = '$department_id'
    ");

    $department_stock_value = getSingleValue($conn, "
        SELECT COALESCE(SUM(department_stock.quantity * products.unit_price), 0) AS total
        FROM department_stock
        LEFT JOIN products ON department_stock.product_id = products.id
        WHERE department_stock.department_id = '$department_id'
    ");

    $my_pending_receipts = getSingleValue($conn, "
        SELECT COUNT(*) AS total
        FROM stock_issues
        WHERE status='pending'
        AND department_id = '$department_id'
    ");

} else {

    $total_products = getSingleValue($conn, "
        SELECT COUNT(*) AS total
        FROM products
    ");

    $total_stock = getSingleValue($conn, "
        SELECT COALESCE(SUM(quantity), 0) AS total
        FROM products
    ");

    $low_stock = getSingleValue($conn, "
        SELECT COUNT(*) AS total
        FROM products
        WHERE quantity <= low_stock_limit
    ");

    $pending_issues = getSingleValue($conn, "
        SELECT COUNT(*) AS total
        FROM stock_issues
        WHERE status='pending'
    ");

    $inventory_value = getSingleValue($conn, "
        SELECT COALESCE(SUM(quantity * unit_price), 0) AS total
        FROM products
    ");
}
?>

<div class="md:ml-64 p-6">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#07152B]">
            Dashboard
        </h1>

        <p class="text-gray-500 mt-2">
            Welcome,
            <strong><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></strong>
            | Role:
            <span class="text-blue-600">
                <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($role))); ?>
            </span>
            | Department:
            <span class="text-green-600">
                <?php echo htmlspecialchars($department_name); ?>
            </span>
        </p>
    </div>

    <?php if ($is_department_user && $department_id): ?>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">My Department</p>
                <h2 class="text-2xl font-bold mt-3 text-[#07152B]">
                    <?php echo htmlspecialchars($department_name); ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Department Stock Items</p>
                <h2 class="text-3xl font-bold mt-3 text-green-600">
                    <?php echo number_format($total_department_items); ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Department Stock Qty</p>
                <h2 class="text-3xl font-bold mt-3 text-blue-600">
                    <?php echo number_format($total_department_qty); ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">My Pending Receipts</p>
                <h2 class="text-3xl font-bold mt-3 text-orange-600">
                    <?php echo number_format($my_pending_receipts); ?>
                </h2>
            </div>

        </div>

        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">My Department Stock Value</p>
            <h2 class="text-3xl font-bold mt-3 text-purple-600">
                ₦<?php echo number_format($department_stock_value, 2); ?>
            </h2>
        </div>

    <?php else: ?>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Total Products</p>
                <h2 class="text-3xl font-bold mt-3 text-[#07152B]">
                    <?php echo number_format($total_products); ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Store Stock Quantity</p>
                <h2 class="text-3xl font-bold mt-3 text-green-600">
                    <?php echo number_format($total_stock); ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Inventory Value</p>
                <h2 class="text-3xl font-bold mt-3 text-blue-600">
                    ₦<?php echo number_format($inventory_value, 2); ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Low Stock Alerts</p>
                <h2 class="text-3xl font-bold mt-3 text-red-600">
                    <?php echo number_format($low_stock); ?>
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Pending Receipts</p>
                <h2 class="text-3xl font-bold mt-3 text-orange-600">
                    <?php echo number_format($pending_issues); ?>
                </h2>
            </div>

        </div>

    <?php endif; ?>

</div>

<?php include "../includes/footer.php"; ?>