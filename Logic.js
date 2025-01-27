let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Function to add products to the cart
function addToCart(productId, productName, price) {
    const product = { id: productId, name: productName, price: price, quantity: 1 };
    console.log("Adding product to cart:", product); // Debugging line

    // Check if the product already exists in the cart
    let found = cart.find(item => item.id === productId);

    if (found) {
        found.quantity += 1;
    } else {
        cart.push(product);
    }

    // Save the updated cart to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));

    // Update cart count
    updateCartCount();

    // Sync the cart with the server
    syncCartWithServer();

    // Show alert that item is added to the cart
    alert(`Added ${productName} to the cart!`);
}

function syncCartWithServer(cartData = cart) {
    const jsonData = JSON.stringify(cartData);
    console.log("Syncing cart with server:", jsonData); // Debugging line
    fetch('synic_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: jsonData
    })
    .then(response => response.text()) // Read response as text to catch non-JSON errors
    .then(text => {
        try {
            const data = JSON.parse(text); // Attempt to parse JSON
            console.log("Cart synced:", data);
        } catch (error) {
            console.error("Failed to parse JSON:", text); // Log non-JSON response
        }
    })
    .catch(error => console.error("Sync failed:", error));
}

// Function to update the cart count on the page
function updateCartCount() {
    const cartCount = cart.reduce((acc, item) => acc + item.quantity, 0);
    document.getElementById("cart-count").textContent = cartCount;
}

// Call updateCartCount on page load to initialize the cart count
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
});

// Function to clear the cart with confirmation
function clearCart() {
    if (confirm("Are you sure you don't want to buy?")) {
        // Clear the cart in localStorage and session
        localStorage.removeItem('cart');
        cart = []; // Clear the cart array

        // Sync with the server to update the session
        syncCartWithServer([]);

        // Update cart count
        updateCartCount();

        // Clear cart display in the UI
        const cartTable = document.querySelector('.cart tbody');
        const cartSummary = document.querySelector('.cart p');
        if (cartTable) {
            cartTable.innerHTML = '';
        }
        if (cartSummary) {
            cartSummary.innerHTML = 'Total Items: 0<br>Total Price: $0.00';
        }

        // Show alert to reflect changes
        alert("Cart cleared successfully");

        // Optional: reload the page to ensure UI reflects changes
        // location.reload();
    }
}

function placeOrder() {
    console.log("Placing order with cart:", cart); // Debugging line
    fetch('place_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ cart })
    })
    .then(response => response.text()) // Read response as text to catch non-JSON errors
    .then(text => {
        console.log("Raw response:", text); // Log raw response
        try {
            const data = JSON.parse(text); // Attempt to parse JSON
            console.log("Order response:", data); // Debugging line
            if (data.status === 'success') {
                alert("Order added successfully");
                // Clear the cart after placing the order without the confirmation alert
                localStorage.removeItem('cart');
                cart = [];
                syncCartWithServer([]);
                updateCartCount();
                const cartTable = document.querySelector('.cart tbody');
                const cartSummary = document.querySelector('.cart p');
                if (cartTable) {
                    cartTable.innerHTML = '';
                }
                if (cartSummary) {
                    cartSummary.innerHTML = 'Total Items: 0<br>Total Price: $0.00';
                }
                location.reload(); // Reload to reflect changes
            } else {
                alert("Order placement failed: " + data.message);
            }
        } catch (error) {
            console.error("Failed to parse JSON:", text); // Log non-JSON response
            alert("Order placement failed: " + error.message);
        }
    })
    .catch(error => console.error("Order placement failed:", error));
}


// Function to go back to the home page
function goToHome() {
    window.location.href = 'index.php';
}
