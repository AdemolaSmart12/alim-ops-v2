<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$role = $_SESSION['role'] ?? '';
$department_id = $_SESSION['department_id'] ?? null;
$department_name = $_SESSION['department_name'] ?? 'No Department';

if ($role === 'department_user' && !$department_id) {
    echo "
    <div class='md:ml-64 p-6'>
        <div class='bg-red-100 text-red-700 p-5 rounded-xl'>
            No department assigned to this user.
        </div>
    </div>";

    include "../includes/footer.php";
    exit();
}

if ($role === 'department_user') {
    $stock = mysqli_query($conn, "
        SELECT
            department_stock.*,
            products.name AS product_name,
            products.unit,
            products.unit_price
        FROM department_stock
        LEFT JOIN products ON department_stock.product_id = products.id
        WHERE department_stock.department_id='$department_id'
        ORDER BY products.name ASC
    ");
} else {
    $stock = mysqli_query($conn, "
        SELECT
            department_stock.*,
            departments.name AS department_name,
            products.name AS product_name,
            products.unit,
            products.unit_price
        FROM department_stock
        LEFT JOIN departments ON department_stock.department_id = departments.id
        LEFT JOIN products ON department_stock.product_id = products.id
        ORDER BY departments.name ASC, products.name ASC
    ");
}

if (!$stock) {
    die("Department stock query failed: " . mysqli_error($conn));
}
?>

<div class="md:ml-64 p-6">

    <div class="bg-white rounded-2xl shadow-lg p-6">

        <h1 class="text-3xl font-bold text-[#07152B] mb-2">
            Department Stock
        </h1>

        <p class="text-gray-500 mb-6">
            <?php if ($role === 'department_user'): ?>
                Showing stock for <?php echo htmlspecialchars($department_name); ?>
            <?php else: ?>
                Showing stock for all departments
            <?php endif; ?>
        </p>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>
                    <tr class="bg-slate-100">
                        <?php if ($role !== 'department_user'): ?>
                            <th class="p-3 text-left">Department</th>
                        <?php endif; ?>

                        <th class="p-3 text-left">Product</th>
                        <th class="p-3 text-left">Quantity</th>
                        <th class="p-3 text-left">Unit</th>
                        <th class="p-3 text-left">Unit Price</th>
                        <th class="p-3 text-left">Stock Value</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($stock) > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($stock)): ?>

                            <tr class="border-b">
                                <?php if ($role !== 'department_user'): ?>
                                    <td class="p-3">
                                        <?php echo htmlspecialchars($row['department_name'] ?? 'N/A'); ?>
                                    </td>
                                <?php endif; ?>

                                <td class="p-3 font-semibold">
                                    <?php echo htmlspecialchars($row['product_name'] ?? 'N/A'); ?>
                                </td>

                                <td class="p-3">
                                    <?php echo (int)$row['quantity']; ?>
                                </td>

                                <td class="p-3">
                                    <?php echo htmlspecialchars($row['unit'] ?? ''); ?>
                                </td>

                                <td class="p-3">
                                    ₦<?php echo number_format($row['unit_price'] ?? 0, 2); ?>
                                </td>

                                <td class="p-3 font-bold text-green-600">
                                    ₦<?php echo number_format(($row['quantity'] ?? 0) * ($row['unit_price'] ?? 0), 2); ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td
                                colspan="<?php echo ($role !== 'department_user') ? 6 : 5; ?>"
                                class="p-4 text-center text-gray-500"
                            >
                                No department stock found yet.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>