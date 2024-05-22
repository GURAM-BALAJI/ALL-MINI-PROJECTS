$(document).ready(function() {
    // Fetch products and display on page load
    fetchProducts();

    // Add product form submit
    $('#product-form').submit(function(event) {
        event.preventDefault();
        addProduct();
    });
});

// Fetch products
function fetchProducts() {
    $.ajax({
        url: 'products.php', // URL to fetch products from
        type: 'GET',
        success: function(response) {
            try {
                var products = JSON.parse(response);
                $('#products-list').empty();
                products.forEach(function(product) {
                    $('#products-list').append(`
                        <div class="product-item">
                        <div style='width:23%;'>
                            <img src="${product.image}" alt="${product.name}">
                            </div>
                            <div style='width:43%;'>
                                <h3>${product.name}</h3>
                                <p>${product.description}</p>
                                <p>Price: ${product.price}</p>
                            </div>
                            <div style='width:33%;'>
                            <button style='background-color:gray;' onclick="editImage(${product.id})">Edit Image</button>
                            <button style='background-color:orange;' onclick="openEditModal(${product.id}, '${product.name}', ${product.price}, '${product.description}')">Edit</button>
                            <button onclick="deleteProduct(${product.id})">Delete</button>
                            </div>
                        </div>
                    `);
                });
            } catch (error) {
                displayPopup("Error parsing JSON: " + error, false);
            }
        },
        error: function(xhr, status, error) {
            displayPopup("Error fetching products: " + xhr.responseText, false);

        }
    });
}


function addProduct() {
    // Retrieve values from form fields
    var name = document.getElementById("name").value;
    var price = document.getElementById("price").value;
    var description = document.getElementById("description").value;
    var image = document.getElementById("image").files[0]; // Get the first selected file

    // Create FormData object to send form data to server
    var formData = new FormData();
    formData.append('name', name);
    formData.append('price', price);
    formData.append('description', description);
    formData.append('image', image);

    // Send AJAX request to add_product.php
    $.ajax({
        url: 'add_product.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            // Reset form fields and display success message
            document.getElementById("name").value = "";
            document.getElementById("price").value = "";
            document.getElementById("description").value = "";
            document.getElementById("image").value = "";
            fetchProducts(); // Update product list
            displayPopup(response, true);

        },
        error: function(xhr, status, error) {
            displayPopup(xhr.responseText, false);
        }
    });
}



function deleteProduct(productId) {
    $.ajax({
        url: 'delete_product.php',
        type: 'POST',
        data: { product_id: productId },
        success: function(response) {
            displayPopup(response, true);
            fetchProducts();
        }
    });
}


// Open edit product modal
function openEditModal(id, name, price, description) {
    $('#edit-product-id').val(id);
    $('#edit-name').val(name);
    $('#edit-price').val(price);
    $('#edit-description').val(description);
    $('#edit-product-modal').show();
}

// Close edit product modal
$('.close').click(function() {
    $('#edit-product-modal').hide();
    $('#edit-product-img-modal').hide();

});

// Update product
$('#edit-product-form').submit(function(event) {
    event.preventDefault();
    var id = $('#edit-product-id').val();
    var name = $('#edit-name').val();
    var price = $('#edit-price').val();
    var description = $('#edit-description').val();

    $.ajax({
        url: 'update_product.php',
        type: 'POST',
        data: {
            id: id,
            name: name,
            price: price,
            description: description
        },
        success: function(response) {
            // Handle success
            displayPopup(response, true);

            fetchProducts();
            $('#edit-product-modal').hide();
        },
        error: function(xhr, status, error) {
            // Handle error
            displayPopup(error, false);
        }
    });
});

// Function to display a popup message
function displayPopup(message, isSuccess) {
    var popup = $('#popup-message');
    popup.text(message);
    if (isSuccess) {
        popup.removeClass('error').addClass('success').fadeIn();
    } else {
        popup.removeClass('success').addClass('error').fadeIn();
    }
    // Hide the popup after a certain time (e.g., 3 seconds)
    setTimeout(function() {
        popup.fadeOut();
    }, 3000);
}

// Function to handle editing product image
function editImage(productId) {
    // Set the product id in the hidden input field
    $('#edit-product-img-id').val(productId);
    
    // Show the edit product modal
    $('#edit-product-img-modal').css('display', 'block');
}

// Function to handle form submission for updating product image
$('#edit-image-form').submit(function(event) {
    event.preventDefault();
    var productId = $('#edit-product-img-id').val();
    var fileInput = $('#edit-image');
    var formData = new FormData();
    formData.append('image', fileInput[0].files[0]);
    formData.append('product_id', productId);
    $.ajax({
        url: 'edit_image.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {            
            $('#edit-product-img-modal').css('display', 'none');
            displayPopup(response, true);
            fetchProducts();
        },
        error: function(xhr, status, error) {
            
            displayPopup("Error updating image: " + xhr.responseText, false);
        }
    });
});