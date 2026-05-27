<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$products = mysqli_query($conn, "
    SELECT
        products.*,
        categories.name AS category_name
    FROM products
    LEFT JOIN categories
        ON products.category_id = categories.id
    ORDER BY products.id DESC
");

if (!$products) {
    die("Products query failed: " . mysqli_error($conn));
}
?>

<div class="md:ml-64 p-6">

    <div class="flex justify-between items-center mb-6">

        <div>
            <h1 class="text-3xl font-bold text-[#07152B]">
                Products
            </h1>

            <p class="text-gray-500">
                General Store Inventory List
            </p>
        </div>

        <a
            href="add_product.php"
            class="bg-[#07152B] text-white px-5 py-3 rounded-xl"
        >
            Add Product
        </a>

    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 overflow-x-auto">

        <table class="w-full">

            <thead>
                <tr class="bg-slate-100">
                    <th class="p-3 text-left">Product</th>
                    <th class="p-3 text-left">Category</th>
                    <th class="p-3 text-left">Qty</th>
                    <th class="p-3 text-left">Unit</th>
                    <th class="p-3 text-left">Unit Price</th>
                    <th class="p-3 text-left">Stock Value</th>
                    <th class="p-3 text-left">Low Stock Limit</th>
                </tr>
            </thead>

            <tbody>

                <?php if (mysqli_num_rows($products) > 0): ?>

                    <?php while ($row = mysqli_fetch_assoc($products)): ?>

                        <tr class="border-b">

                            <td class="p-3 font-semibold">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </td>

                            <td class="p-3">
                                <?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?>
                            </td>

                            <td class="p-3">
                                <?php echo (int)$row['quantity']; ?>
                            </td>

                            <td class="p-3">
                                <?php echo htmlspecialchars($row['unit']); ?>
                            </td>

                            <td class="p-3">
                                ₦<?php echo number_format($row['unit_price'], 2); ?>
                            </td>

                            <td class="p-3 font-semibold text-green-600">
                                ₦<?php echo number_format($row['quantity'] * $row['unit_price'], 2); ?>
                            </td>

                            <td class="p-3">
                                <?php echo (int)$row['low_stock_limit']; ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500">
                            No products found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include "../includes/footer.php"; ?>