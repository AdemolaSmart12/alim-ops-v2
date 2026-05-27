<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$total_products = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM products
"));

$total_stock = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(quantity) AS total FROM products
"));

$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM products
    WHERE quantity <= low_stock_limit
"));

$pending_issues = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM stock_issues
    WHERE status='pending'
"));
?>

<div class="md:ml-64 p-6">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#07152B]">Dashboard</h1>
        <p class="text-gray-500 mt-2">
            Welcome, <strong><?php echo $_SESSION['name']; ?></strong>
            | Role: <span class="text-blue-600"><?php echo ucfirst($_SESSION['role']); ?></span>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Total Products</p>
            <h2 class="text-3xl font-bold mt-3 text-[#07152B]">
                <?php echo $total_products['total'] ?? 0; ?>
            </h2>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Store Stock Quantity</p>
            <h2 class="text-3xl font-bold mt-3 text-green-600">
                <?php echo $total_stock['total'] ?? 0; ?>
            </h2>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Low Stock Alerts</p>
            <h2 class="text-3xl font-bold mt-3 text-red-600">
                <?php echo $low_stock['total'] ?? 0; ?>
            </h2>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Pending Receipts</p>
            <h2 class="text-3xl font-bold mt-3 text-orange-600">
                <?php echo $pending_issues['total'] ?? 0; ?>
            </h2>
        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>