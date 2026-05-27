<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$message = "";

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
$suppliers = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $unit_price = $_POST['unit_price'];
    $low_stock_limit = $_POST['low_stock_limit'];

    $insert = mysqli_query($conn, "
        INSERT INTO products (
            category_id,
            supplier_id,
            name,
            unit,
            unit_price,
            low_stock_limit
        ) VALUES (
            '$category_id',
            '$supplier_id',
            '$name',
            '$unit',
            '$unit_price',
            '$low_stock_limit'
        )
    ");

    if ($insert) {
        $message = "Product added successfully.";
    } else {
        $message = "Error adding product.";
    }
}
?>

<div class="md:ml-64 p-6">

    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-3xl">

        <h1 class="text-2xl font-bold mb-6 text-[#07152B]">
            Add Product
        </h1>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-5">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <input
                name="name"
                placeholder="Product Name"
                class="w-full border p-3 rounded-xl"
                required
            >

            <select name="category_id" class="w-full border p-3 rounded-xl" required>
                <option value="">Select Category</option>
                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo $cat['id']; ?>">
                        <?php echo $cat['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="supplier_id" class="w-full border p-3 rounded-xl" required>
                <option value="">Select Supplier</option>
                <?php while ($sup = mysqli_fetch_assoc($suppliers)): ?>
                    <option value="<?php echo $sup['id']; ?>">
                        <?php echo $sup['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input
                name="unit"
                placeholder="Unit e.g. bottle, carton, bag, pcs"
                class="w-full border p-3 rounded-xl"
                required
            >

            <input
                type="number"
                step="0.01"
                name="unit_price"
                placeholder="Unit Price"
                class="w-full border p-3 rounded-xl"
                required
            >

            <input
                type="number"
                name="low_stock_limit"
                placeholder="Low Stock Limit"
                class="w-full border p-3 rounded-xl"
                required
            >

            <button class="bg-[#07152B] text-white px-6 py-3 rounded-xl w-full">
                Save Product
            </button>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>