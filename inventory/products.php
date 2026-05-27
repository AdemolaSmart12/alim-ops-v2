<?php
include "../includes/auth.php";
include "../includes/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$products=mysqli_query(
$conn,
"
SELECT
products.*,
categories.name AS category_name

FROM products

LEFT JOIN categories
ON products.category_id=categories.id

ORDER BY products.id DESC
"
);
?>

<div class="md:ml-64 p-6">

<div class="flex justify-between mb-6">

<div>

<h1 class="text-3xl font-bold text-[#07152B]">

Products

</h1>

<p class="text-gray-500">

Store Inventory List

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
<th class="p-3 text-left">Price</th>

</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($products)>0): ?>

<?php while(
$row=mysqli_fetch_assoc(
$products
)
): ?>

<tr class="border-b">

<td class="p-3 font-semibold">

<?php echo $row['name']; ?>

</td>

<td class="p-3">

<?php echo $row['category_name']; ?>

</td>

<td class="p-3">

<?php echo $row['quantity']; ?>

</td>

<td class="p-3">

<?php echo $row['unit']; ?>

</td>

<td class="p-3">

₦<?php
echo number_format(
$row['unit_price'],
2
);
?>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="5"
class="p-4 text-center">

No products found

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<?php include "../includes/footer.php"; ?>