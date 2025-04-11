<?php include("header.php"); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <!-- Products Table -->
    <div class="container mx-auto px-4 mt-6">
        <!-- Alert container -->
        <div id="alert-container" class="hidden fixed top-0 left-1/2 transform -translate-x-1/2 mt-4 w-1/2 sm:w-3/4 md:w-1/3 bg-green-100 text-green-800 p-3 rounded-lg shadow-lg z-50">
            <p id="alert-message" class="font-semibold text-sm"></p>
            <button onclick="closeAlert()" class="absolute top-2 right-2 text-green-800">×</button>
        </div>
        <br>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6 flex-wrap">
                <h2 class="text-2xl font-bold mb-4">Product List</h2>
                <button onclick="openAddProductModal()" class="bg-green-500 text-white py-2 px-4 rounded w-full md:w-auto mt-4 md:mt-0">Add Product</button>
            </div>
            <div class="overflow-x-auto">
            <div class="flex justify-between items-center mb-4">
    <input 
        type="text" 
        id="search-input" 
        class="border px-4 py-2 rounded w-full md:w-1/3" 
        placeholder="Search products..." 
        oninput="searchProducts(this.value)"
    >


</div>



                <table id="product-table" class="min-w-full bg-white border">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 text-left">Image</th>
                            <th class="py-3 px-4 text-left">Product Name</th>
                            <th class="py-3 px-4 text-left">Price</th>
                            <th class="py-3 px-4 text-left">Stock Quantity</th>
                            <th class="py-3 px-4 text-left">Expiry Date</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="product-body" class="text-gray-600 text-sm font-light">
                        <!-- Dynamic content will be injected here -->
                    </tbody>
                </table>
                <div id="pagination" class="flex justify-center items-center mt-4 "></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal Structure -->
<div id="addProductModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-1/2 mt-12">
        <h2 class="text-2xl mb-4">Add New Product</h2>
        <form id="addProductForm">
            <div class="mb-4">
                <label for="addProductName" class="block text-sm font-medium">Name</label>
                <input type="text" id="addProductName" name="product_name" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="addProductPrice" class="block text-sm font-medium">Price</label>
                <input type="text" id="addProductPrice" name="price" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="addProductStock" class="block text-sm font-medium">Stock</label>
                <input type="number" id="addProductStock" name="stock" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="addProductExpire" class="block text-sm font-medium">Expiration Date</label>
                <input type="date" id="addProductExpire" name="expire" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
    <label for="addProductImage" class="block text-sm font-medium">Image</label>
    <input type="file" name="product_image" id="addProductImage" 
           class="w-full px-3 py-2 border border-gray-300 rounded"
           onchange="previewImage(this, 'addImagePreview')">
</div>
<div id="addImagePreviewContainer" class="mb-4 hidden">
    <label class="block text-sm font-medium">Preview</label>
    <img id="addImagePreview" src="" 
         alt="Product Image" class="w-32 h-32 object-cover rounded border">
</div>

            <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded">Add Product</button>
            <button type="button" id="closeAddProductModal" class="mt-4 bg-gray-500 text-white py-2 px-4 rounded">Close</button>
        </form>
    </div>
</div>




<!-- Modal Structure -->
<div id="editProductModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-1/2 mt-12 max-h-screen overflow-y-auto">
        <h2 class="text-2xl mb-4">Edit Product</h2>
        <form id="editProductForm" enctype="multipart/form-data">
            <input type="hidden" id="productId" name="product_id">
            <input type="hidden" id="existingImage" name="existing_image">

            <div class="mb-4">
                <label for="productName" class="block text-sm font-medium">Name</label>
                <input type="text" name="product_name" id="productName" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="productPrice" class="block text-sm font-medium">Price</label>
                <input type="text" name="price" id="productPrice" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="productStock" class="block text-sm font-medium">Stock</label>
                <input type="number" name="stock" id="productStock" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="productExpiry" class="block text-sm font-medium">Expiration Date</label>
                <input type="date" name="expiry" id="productExpiry" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="productImage" class="block text-sm font-medium">Image</label>
                <input type="file" name="product_image" id="productImage" class="w-full px-3 py-2 border border-gray-300 rounded" onchange="previewImage(this, 'imagePreview')">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Current Image</label>
                <img id="imagePreview" src="" alt="Product Image" class="w-32 h-32 object-cover rounded border">
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Save</button>
            <button type="button" id="closeEditModal" class="ml-2 bg-gray-500 text-white py-2 px-4 rounded">Close</button>
        </form>
    </div>
</div>


<script src="script.js"></script>

</body>


</html>

