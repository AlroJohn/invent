let currentPage = 1;
const itemsPerPage = 10; // Number of items to display per page
let searchQuery = ""; // Store the current search query

function fetchProducts(page = 1, query = "") {
    fetch(`fetch_products.php?page=${page}&limit=${itemsPerPage}&search=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const productBody = document.getElementById('product-body');
            productBody.innerHTML = ''; // Clear the existing rows

            // Get today's date in YYYY-MM-DD format
            const today = new Date().toISOString().split('T')[0];

            // Populate product rows
            if (data.products && data.products.length > 0) {
                data.products.forEach((product) => {
                    // Check if the product is expired
                    let expirationLabel = `<span>${product.expiration_date}</span>`;
                    if (product.expiration_date < today) {
                        expirationLabel = `<span class="text-red-500">${product.expiration_date} (Expired)</span>`;
                    }

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="py-3 px-4">
                            <img src="${product.image_path}" alt="${product.name}" class="w-16 h-16 object-cover">
                        </td>
                        <td class="py-3 px-4">${product.name}</td>
                        <td class="py-3 px-4">${product.price}</td>
                        <td class="py-3 px-4">${product.stock}</td>
                        <td class="py-3 px-4">${expirationLabel}</td>
                        <td class="py-3 px-4 text-center">
                            <button onclick='openEditProductModal(${JSON.stringify(product)})' class="bg-yellow-500 text-white py-2 px-4 rounded">Edit</button>
                            <button onclick="deleteProduct(${product.id})" class="bg-red-500 text-white py-2 px-4 rounded">Delete</button>
                        </td>
                    `;
                    productBody.appendChild(row);
                });
            } else {
                productBody.innerHTML = `<tr><td colspan="6" class="text-center py-4">No products found</td></tr>`;
            }

            // Update pagination controls
            updatePagination(data.total, data.page, data.limit);
        })
        .catch((error) => console.error('Error fetching products:', error));
}


function searchProducts(query) {
    searchQuery = query; // Update the global search query
    fetchProducts(1, searchQuery); // Fetch results for the first page based on the query
}

function updatePagination(totalItems, currentPage, itemsPerPage) {
    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = ''; // Clear existing pagination

    const totalPages = Math.ceil(totalItems / itemsPerPage);

    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.classList.add('px-4', 'py-2', 'border', 'border-gray-300', 'rounded', 'mx-1');
        if (i === currentPage) {
            button.classList.add('bg-gray-500', 'text-white');
        } else {
            button.classList.add('hover:bg-gray-200');
        }

        button.addEventListener('click', () => {
            fetchProducts(i, searchQuery); // Include the search query when fetching pages
        });

        paginationContainer.appendChild(button);
    }
}

// Initial fetch
fetchProducts();



function openEditProductModal(product) {
    console.log("Opening Edit Modal:", product);

    const modal = document.getElementById('editProductModal');
    
    if (!modal) {
        console.error('Edit Product Modal not found');
        return;
    }

    // Set values in the modal
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productPrice').value = product.price;
    document.getElementById('productStock').value = product.stock;
    document.getElementById('productImage').value = '';
    document.getElementById('existingImage').value = product.image_path;

    modal.classList.remove('hidden'); // Show the modal

    // Close modal event
    const closeModalButton = document.getElementById('closeModalButton');
    closeModalButton.addEventListener('click', function() {
        modal.classList.add('hidden'); // Hide modal
    });
}

function openAddProductModal() {
    const modal = document.getElementById('addProductModal');

    if (!modal) {
        console.error('Add Product Modal not found');
        return;
    }

    modal.classList.remove('hidden'); // Show the modal

    // Close modal event
    const closeModalButton = document.getElementById('closeAddModalButton');
    closeModalButton.addEventListener('click', function() {
        modal.classList.add('hidden'); // Hide modal
    });
}











// Close the Add Product Modal
document.getElementById('closeAddProductModal').addEventListener('click', function() {
    const modal = document.getElementById('addProductModal');
    modal.classList.add('hidden'); // Hide the modal
});


document.getElementById('closeEditModal').addEventListener('click', closeEditModal);

    function closeEditModal() {
        console.log('Closing modal...');
        document.getElementById('editProductModal').classList.add('hidden');
        document.getElementById('editProductForm').reset();
    }

document.getElementById('addProductForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    
    const formData = new FormData(this); // Gather form data

    // Send the data via AJAX
    fetch('add_product.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Product added successfully');
            // Optionally, reset the form
            document.getElementById('addProductForm').reset();
            // Hide modal
            document.getElementById('addProductModal').classList.add('hidden');
            // Refresh the page
            window.location.reload();
        } else {
            // Show error message
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error submitting the form');
    });
});



// Handle form submission for editing a product
document.getElementById('editProductForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this); // Gather form data

    // Log the form data to ensure it's being collected properly
    formData.forEach((value, key) => {
        console.log(key + ": " + value);  // Check if all fields are in the form data
    });

    fetch('edit_product.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Server returned an error');
        }
        return response.json(); // Attempt to parse JSON
    })
    .then(data => {
        if (data.success) {
            alert('Product updated successfully');
            document.getElementById('editProductModal').classList.add('hidden');
            
            // Refresh the page after success
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error submitting the form');
    });
});


// Open and populate the edit product modal
function openEditProductModal(product) {
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productPrice').value = product.price;
    document.getElementById('productStock').value = product.stock;

    // Format and set the expiration date
    if (product.expiration_date) {
        const formattedDate = new Date(product.expiration_date).toISOString().split('T')[0];
        document.getElementById('productExpiry').value = formattedDate;
    } else {
        document.getElementById('productExpiry').value = ''; // Clear if no date
    }
     // Set existing image preview
     const imagePreview = document.getElementById("imagePreview");
     if (product.image_path) {
         imagePreview.src = product.image_path;
     } else {
         imagePreview.src = ""; // Clear if no image
     }
    document.getElementById('existingImage').value = product.image_path;

    // Show the modal
    document.getElementById('editProductModal').classList.remove('hidden');
}




// Show alert messages
function showAlert(message, type) {
    const alertContainer = document.getElementById('alert-container');
    const alertMessage = document.getElementById('alert-message');
    alertMessage.textContent = message;

    if (type === 'success') {
        alertContainer.classList.remove('bg-red-100', 'text-red-800');
        alertContainer.classList.add('bg-green-100', 'text-green-800');
    } else {
        alertContainer.classList.remove('bg-green-100', 'text-green-800');
        alertContainer.classList.add('bg-red-100', 'text-red-800');
    }

    alertContainer.classList.remove('hidden');
    setTimeout(() => {
        alertContainer.classList.add('hidden');
    }, 5000);
}

// Close the alert
function closeAlert() {
    document.getElementById('alert-container').classList.add('hidden');
}

// Delete product
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch('delete_product.php', {
            method: 'POST',
            body: JSON.stringify({ id: productId }),
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                fetchProducts(); // Refresh the product list
                showAlert('Product deleted successfully!', 'success');
            } else {
                showAlert('Failed to delete product. Please try again.', 'error');
            }
        })
        .catch((error) => {
            showAlert('An error occurred. Please try again.', 'error');
        });
    }
}


function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Add a title to the PDF
    doc.text('Product List', 14, 10);

    // Get the table data
    const rows = [];
    const headers = ['Product Name', 'Price', 'Stock Quantity', 'Expiry Date'];
    const productBody = document.querySelectorAll('#product-body tr');

    productBody.forEach(row => {
        const columns = row.querySelectorAll('td');
        if (columns.length >= 4) {
            rows.push([
                columns[1].textContent.trim(), // Product Name
                columns[2].textContent.trim(), // Price
                columns[3].textContent.trim(), // Stock Quantity
                columns[4].textContent.trim(), // Expiry Date
            ]);
        }
    });

    // Use AutoTable to create a table in the PDF
    doc.autoTable({
        head: [headers],
        body: rows,
        startY: 20,
    });

    // Save the PDF
    doc.save('product_list.pdf');
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const previewContainer = preview.parentElement;

    const file = input.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = "block"; // Show the image preview
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
        previewContainer.style.display = "none"; // Hide when empty
    }
}



