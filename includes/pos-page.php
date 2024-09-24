<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function woo_nanopay_pos_shortcode() {
    ob_start();
    ?>
    <style>
        #woo-nanopay-pos {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            /* Remove background color to inherit from theme */
        }
        #woo-nanopay-pos h2, #woo-nanopay-pos h3 {
            text-align: center;
        }
        #products, #cart, #payment {
            margin-bottom: 20px;
        }
        .product {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #fff;
            display: flex;
            align-items: center;
        }
        .product img {
            max-width: 100px;
            margin-right: 10px;
        }
        .product h3 {
            margin: 0 0 10px;
        }
        .add-to-cart {
            background-color: #0073aa;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .add-to-cart:hover {
            background-color: #005177;
        }
        #cart-items {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: #fff;
        }
        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .cart-item-info {
            display: flex;
            align-items: center;
            flex-grow: 1;
        }
        .cart-item img {
            max-width: 50px;
            margin-right: 10px;
        }
        .cart-item-details {
            flex-grow: 1;
        }
        .cart-item input {
            width: 50px;
            margin: 0 10px;
        }
        .remove-from-cart {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .remove-from-cart:hover {
            background-color: #c82333;
        }
        #checkout {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }
        #checkout:hover {
            background-color: #218838;
        }
        #payment-qr {
            text-align: center;
        }
        #cart-total {
            text-align: right;
            margin-top: 10px;
        }
        #order-history {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .order-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            margin-bottom: 5px;
        }
        .order-item h4 {
            margin-top: 0;
        }
        .toggle-details {
            width: 100%;
            background-color: #0073aa;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .toggle-details:hover {
            background-color: #005177;
        }
        .order-item-details {
            display: none;
            margin-top: 10px;
            font-size: 0.9em;
        }
        .order-item-details.show {
            display: block;
        }
        .order-item-details ul {
            list-style-type: none;
            padding-left: 0;
        }
        #reset-history {
            background-color: red;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        #reset-history:hover {
            background-color: darkred;
        }
        .quantity-buttons {
            display: flex;
            align-items: center;
        }
        .quantity-button {
            background-color: #0073aa;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
        }
        .quantity-button:hover {
            background-color: #005177;
        }
        @media (max-width: 600px) {
            .cart-item, .order-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .cart-item-info {
                width: 100%;
                margin-bottom: 10px;
            }
            .remove-from-cart {
                align-self: flex-end;
            }
            .order-item {
                font-size: 0.9em;
                padding: 15px;
            }
            .order-item-details {
                margin-top: 5px;
            }
            .order-item-details ul {
                padding-left: 15px;
            }
            .toggle-details {
                width: 100%;
                display: block;
            }
        }
    </style>
    <div id="woo-nanopay-pos">
        <h2>Nano Point of Sale</h2>
        <div id="products">
            <?php
            $args = array('post_type' => 'product', 'posts_per_page' => -1);
            $loop = new WP_Query($args);
            while ($loop->have_posts()) : $loop->the_post();
                global $product;
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                
                // Handle service products
                if ($product->get_type() === 'service') {
                    $base_price = get_post_meta($product->get_id(), '_base_price', true);
                    $current_price = !empty($base_price) ? $base_price : '0.00';
                } else {
                    $current_price = $sale_price ? $sale_price : $regular_price;
                }
                
                // Ensure we have a valid numeric price
                $current_price = is_numeric($current_price) ? $current_price : '0.00';
                ?>
                <div class="product">
                    <?php echo $product->get_image(); ?>
                    <div>
                        <h4><?php the_title(); ?></h4>
                        <p><?php echo $product->get_price_html(); ?></p>
                        <button class="add-to-cart" 
                                data-product-id="<?php echo $product->get_id(); ?>"
                                data-price="<?php echo esc_attr($current_price); ?>"
                                data-product-type="<?php echo esc_attr($product->get_type()); ?>">
                            Add to Cart
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_query(); ?>
        </div>
        <div id="cart">
            <h3>Cart</h3>
            <div id="cart-items"></div>
            <div id="cart-total"></div>
            <button id="checkout">Checkout</button>
        </div>
        <div id="payment">
            <div id="payment-qr"></div>
            <div id="order-history">
                <h3>Order History</h3>
                <div id="order-list"></div>
                <button id="reset-history">Reset Order History</button>
            </div>
        </div>
    </div>
    <script src="https://pay.nano.to/latest.js"></script>
    <script>
        let cart = [];
        const taxRate = 0.10;
        let orderHistory = [];
        let lastUpdatedOrderId = null;

        function loadOrderHistory() {
            try {
                const savedHistory = localStorage.getItem('orderHistory');
                if (savedHistory) {
                    orderHistory = JSON.parse(savedHistory);
                }
            } catch (error) {
                console.error('Error loading order history:', error);
                orderHistory = [];
            }
        }

        function saveOrderHistory() {
            try {
                localStorage.setItem('orderHistory', JSON.stringify(orderHistory));
            } catch (error) {
                console.error('Error saving order history:', error);
            }
        }

        function addOrderToDisplay(newOrder, index) {
            const orderList = document.getElementById('order-list');
            const orderItem = document.createElement('div');
            orderItem.className = 'order-item';
            orderItem.innerHTML = `
                <h4>Order #${orderHistory.length - index}</h4>
                <p>Total: $${newOrder.total.toFixed(2)}</p>
                <button class="toggle-details" data-order-index="${index}">Show Details</button>
                <div class="order-item-details">
                    <p>Date: ${new Date(newOrder.timestamp).toLocaleString()}</p>
                    <p>Transaction ID: <a href="https://blocklattice.io/block/${newOrder.transactionId}" target="_blank">${newOrder.transactionId}</a></p>
                    <p style='display: none;'>Items:</p>
                    <ul style='display: none;'>
                        ${newOrder.items.map(item => `<li>${item.quantity}x ${item.title} - $${(parseFloat(item.price.replace('$', '')) * item.quantity).toFixed(2)}</li>`).join('')}
                    </ul>
                </div>
            `;
            orderList.insertBefore(orderItem, orderList.firstChild);
        }

        let updateOrderHistoryTimeout = null;

        function debounceUpdateOrderHistory() {
            console.log('Debounce update order history called');
            if (updateOrderHistoryTimeout) {
                clearTimeout(updateOrderHistoryTimeout);
            }
            updateOrderHistoryTimeout = setTimeout(() => {
                console.log('Executing delayed update order history');
                updateOrderHistory();
                updateOrderHistoryTimeout = null;
            }, 30000); // 30 seconds
        }

        function addOrder(newOrder) {
            const existingOrderIndex = orderHistory.findIndex(order => order.transactionId === newOrder.transactionId);
            if (existingOrderIndex !== -1) {
                orderHistory[existingOrderIndex] = newOrder;
                // Update the existing order in the display
                const existingOrderElement = document.querySelector(`.order-item:nth-child(${existingOrderIndex + 1})`);
                if (existingOrderElement) {
                    existingOrderElement.outerHTML = '';
                    addOrderToDisplay(newOrder, existingOrderIndex);
                }
            } else {
                orderHistory.unshift(newOrder);
                addOrderToDisplay(newOrder, 0);
            }
            saveOrderHistory();
            debounceUpdateOrderHistory(); // Call the debounced function instead of updateOrderHistory directly
        }

        function updateOrderHistory() {
            console.log('Updating order history');
            const orderList = document.getElementById('order-list');
            orderList.innerHTML = ''; // Clear existing orders before updating
            orderHistory.forEach((order, index) => {
                addOrderToDisplay(order, index);
            });
            console.log('Order history update complete');
        }

        function resetOrderHistory() {
            orderHistory = [];
            saveOrderHistory();
            updateOrderHistory(); // We still want an immediate update here
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadOrderHistory();
            updateOrderHistory();
        });

        function updateCart() {
            const cartItems = document.getElementById('cart-items');
            cartItems.innerHTML = '';
            let total = 0;
            cart.forEach(item => {
                const cartItem = document.createElement('div');
                cartItem.className = 'cart-item';
                let itemPrice = parseFloat(item.price.replace('$', ''));
                
                // For service products, show price per hour
                let priceDisplay = item.type === 'service' ? `$${itemPrice.toFixed(2)}/hour` : item.price;
                
                cartItem.innerHTML = `
                    <div class="cart-item-info">
                        <img src="${item.image}" alt="${item.title}">
                        <div class="cart-item-details">
                            <span>${item.title}</span>
                            <div class="quantity-buttons">
                                <button class="quantity-button decrease-quantity" data-product-id="${item.id}">-</button>
                                <input type="number" value="${item.quantity}" min="1" data-product-id="${item.id}" readonly>
                                <button class="quantity-button increase-quantity" data-product-id="${item.id}">+</button>
                            </div>
                            <span>${priceDisplay}</span>
                        </div>
                    </div>
                    <button class="remove-from-cart" data-product-id="${item.id}">Remove</button>
                `;
                cartItems.appendChild(cartItem);
                total += itemPrice * item.quantity;
            });
            const tax = total * taxRate;
            const totalWithTax = total + tax;
            document.getElementById('cart-total').innerHTML = `
                <p>Subtotal: $${total.toFixed(2)}</p>
                <p>Tax: $${tax.toFixed(2)}</p>
                <p>Total: $${totalWithTax.toFixed(2)}</p>
            `;
        }

        function resetCart() {
            cart = [];
            updateCart();
            document.getElementById('payment-qr').innerHTML = '';
        }

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('add-to-cart')) {
                const productId = event.target.getAttribute('data-product-id');
                const price = event.target.getAttribute('data-price');
                const productType = event.target.getAttribute('data-product-type');
                const productElement = event.target.closest('.product');
                const productTitle = productElement.querySelector('h4').innerText;
                const productImage = productElement.querySelector('img').src;
                const product = {
                    id: productId,
                    title: productTitle,
                    price: `$${(parseFloat(price) || 0).toFixed(2)}`,
                    image: productImage,
                    quantity: 1,
                    type: productType
                };
                const existingProduct = cart.find(item => item.id === productId);
                if (existingProduct) {
                    existingProduct.quantity++;
                } else {
                    cart.push(product);
                }
                updateCart();
            }

            if (event.target.classList.contains('remove-from-cart')) {
                const productId = event.target.getAttribute('data-product-id');
                cart = cart.filter(item => item.id !== productId);
                updateCart();
            }

            if (event.target.classList.contains('increase-quantity')) {
                const productId = event.target.getAttribute('data-product-id');
                const product = cart.find(item => item.id === productId);
                if (product) {
                    product.quantity++;
                    updateCart();
                }
            }

            if (event.target.classList.contains('decrease-quantity')) {
                const productId = event.target.getAttribute('data-product-id');
                const product = cart.find(item => item.id === productId);
                if (product && product.quantity > 1) {
                    product.quantity--;
                    updateCart();
                }
            }

            if (event.target.classList.contains('toggle-details')) {
                event.preventDefault(); // Prevent any default action
                event.stopPropagation(); // Stop the event from bubbling up
                console.log('Toggle details clicked');
                
                const orderItem = event.target.closest('.order-item');
                const detailsDiv = orderItem.querySelector('.order-item-details');
                const allDetailsDivs = document.querySelectorAll('.order-item-details');
                const allToggleButtons = document.querySelectorAll('.toggle-details');

                // Hide all other detail divs
                allDetailsDivs.forEach((div, index) => {
                    if (div !== detailsDiv) {
                        div.classList.remove('show');
                        allToggleButtons[index].textContent = 'Show Details';
                    }
                });

                // Toggle the clicked order's details
                detailsDiv.classList.toggle('show');
                event.target.textContent = detailsDiv.classList.contains('show') ? 'Hide Details' : 'Show Details';
                console.log('Details toggled:', detailsDiv.classList.contains('show'));
            }
        });

        document.getElementById('checkout').addEventListener('click', function() {
            const total = cart.reduce((total, item) => total + parseFloat(item.price.replace('$', '')) * item.quantity, 0);
            const tax = total * taxRate;
            const totalAmount = total + tax;
            const nanoAddress = '<?php echo get_option('woo_nanopay_pos_nano_address'); ?>';
            NanoPay.open({
                title: "Purchase",
                address: nanoAddress,
                amount: totalAmount,
                currency: 'USD',
                position: 'bottom',
                qrcode: true,
                success: (block) => {
                    console.log(block);
                    const transactionLink = `https://blocklattice.io/block/${block.block}`;
                    const paymentQr = document.getElementById('payment-qr');
                    if (paymentQr) {
                        paymentQr.innerHTML = `
                            <p>Payment successful!</p>
                            <p>Transaction ID: <a href="${transactionLink}" target="_blank">${block.block}</a></p>
                        `;
                    }
                    // Create a single order with all items
                    const newOrder = {
                        total: totalAmount,
                        transactionId: block.block,
                        items: cart.map(item => ({...item})), // Create a new array with copies of each item
                        timestamp: new Date().toISOString()
                    };
                    addOrder(newOrder);
                    resetCart();
                },
                cancel: () => {
                    console.log("User cancelled");
                }
            });
        });

        document.getElementById('reset-history').addEventListener('click', function() {
            if (confirm('Are you sure you want to reset the order history? This action cannot be undone.')) {
                resetOrderHistory();
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('nanopay_pos', 'woo_nanopay_pos_shortcode');
?>