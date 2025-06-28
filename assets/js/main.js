// assets/js/main.js
class HandmadeShop {
    constructor() {
        this.init();
        this.bindEvents();
        this.updateCartCount();
        this.updateFavoritesCount();
    }
    
    init() {
        // Initialize CSRF token
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        // Get current language
        this.currentLanguage = document.documentElement.lang || 'uk';
        
        // Translation strings
        this.translations = {
            uk: {
                adding: 'Додається...',
                addToCart: 'Додати в кошик',
                productAdded: 'Товар додано в кошик!',
                addError: 'Помилка додавання товару',
                connectionError: 'Помилка з\'єднання',
                addedToFavorites: 'Додано в обране!',
                removedFromFavorites: 'Видалено з обраного',
                removeFromCart: 'Видалити товар з кошика?',
                productRemoved: 'Товар видалено з кошика',
                cartEmpty: 'Кошик порожній',
                cartEmptyMessage: 'Додайте товари до кошика для оформлення замовлення',
                continueShopping: 'Продовжити покупки',
                currency: 'грн'
            },
            en: {
                adding: 'Adding...',
                addToCart: 'Add to Cart',
                productAdded: 'Product added to cart!',
                addError: 'Error adding product',
                connectionError: 'Connection error',
                addedToFavorites: 'Added to favorites!',
                removedFromFavorites: 'Removed from favorites',
                removeFromCart: 'Remove product from cart?',
                productRemoved: 'Product removed from cart',
                cartEmpty: 'Cart is Empty',
                cartEmptyMessage: 'Add products to cart to place an order',
                continueShopping: 'Continue Shopping',
                currency: 'UAH'
            }
        };
        
        if (!this.csrfToken) {
            console.warn('CSRF token not found');
        }
    }
    
    // Get translation for current language
    t(key) {
        return this.translations[this.currentLanguage]?.[key] || this.translations.uk[key] || key;
    }
    
    bindEvents() {
        // Add to cart
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart-btn')) {
                e.preventDefault();
                this.addToCart(e.target);
            }
            
            // Toggle favorites
            if (e.target.closest('.favorite-btn')) {
                e.preventDefault();
                this.toggleFavorite(e.target.closest('.favorite-btn'));
            }
            
            // Quantity controls
            if (e.target.classList.contains('qty-btn')) {
                e.preventDefault();
                this.updateQuantity(e.target);
            }
            
            // Remove from cart
            if (e.target.closest('.remove-item-btn')) {
                e.preventDefault();
                this.removeFromCart(e.target.closest('.remove-item-btn'));
            }
        });
        
        // Quantity input changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                this.updateCartQuantity(e.target);
            }
        });
        
        // Live search
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.liveSearch(e.target.value);
                }, 300);
            });
        }
    }
    
    async addToCart(button) {
        const productId = button.dataset.productId;
        const quantityInput = button.parentElement.querySelector('.quantity-input');
        const quantity = quantityInput ? quantityInput.value : 1;
        
        // Save original button text
        const originalText = button.textContent;
        
        button.disabled = true;
        button.textContent = this.t('adding');
        
        try {
            const response = await fetch('ajax/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    product_id: productId,
                    quantity: quantity,
                    csrf_token: this.csrfToken
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(this.t('productAdded'), 'success');
                this.updateCartCount();
                this.animateAddToCart(button);
            } else {
                this.showNotification(data.error || this.t('addError'), 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showNotification(this.t('connectionError'), 'error');
        } finally {
            button.disabled = false;
            button.textContent = originalText;
        }
    }
    
    async toggleFavorite(button) {
        const productId = button.dataset.productId;
        
        try {
            const response = await fetch('ajax/add_to_favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    product_id: productId,
                    csrf_token: this.csrfToken
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (data.action === 'added') {
                    button.classList.add('active');
                    this.showNotification(this.t('addedToFavorites'), 'success');
                } else {
                    button.classList.remove('active');
                    this.showNotification(this.t('removedFromFavorites'), 'info');
                }
                this.updateFavoritesCount();
            }
        } catch (error) {
            console.error('Error toggling favorite:', error);
            this.showNotification(this.t('connectionError'), 'error');
        }
    }
    
    updateQuantity(button) {
        const action = button.dataset.action;
        const input = button.parentElement.querySelector('.quantity-input');
        const currentValue = parseInt(input.value);
        const max = parseInt(input.max);
        const min = parseInt(input.min) || 1;
        
        if (action === 'increase' && currentValue < max) {
            input.value = currentValue + 1;
        } else if (action === 'decrease' && currentValue > min) {
            input.value = currentValue - 1;
        }
        
        // Update cart if on cart page
        if (input.dataset.productId) {
            this.updateCartQuantity(input);
        }
    }
    
    async updateCartQuantity(input) {
        const productId = input.dataset.productId;
        const quantity = input.value;
        
        try {
            const response = await fetch('ajax/update_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    product_id: productId,
                    quantity: quantity,
                    csrf_token: this.csrfToken
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateCartTotals();
                this.updateCartCount();
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
        }
    }
    
    async removeFromCart(button) {
        const productId = button.dataset.productId;
        const cartItem = button.closest('.cart-item');
        
        if (confirm(this.t('removeFromCart'))) {
            try {
                const response = await fetch('ajax/remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        product_id: productId,
                        csrf_token: this.csrfToken
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    cartItem.style.opacity = '0';
                    cartItem.style.transform = 'translateX(-100%)';
                    setTimeout(() => {
                        cartItem.remove();
                        this.updateCartTotals();
                        this.updateCartCount();
                        this.checkEmptyCart();
                    }, 300);
                    this.showNotification(this.t('productRemoved'), 'info');
                }
            } catch (error) {
                console.error('Error removing from cart:', error);
            }
        }
    }
    
    updateCartCount() {
        fetch('ajax/get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.count || 0;
                    cartCount.style.display = data.count > 0 ? 'flex' : 'none';
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }
    
    updateFavoritesCount() {
        fetch('ajax/get_favorites_count.php')
            .then(response => response.json())
            .then(data => {
                const favoritesCount = document.querySelector('.favorites-count');
                if (favoritesCount) {
                    favoritesCount.textContent = data.count || 0;
                    favoritesCount.style.display = data.count > 0 ? 'flex' : 'none';
                }
            })
            .catch(error => console.error('Error updating favorites count:', error));
    }
    
    updateCartTotals() {
        // Update totals on cart page
        const cartItems = document.querySelectorAll('.cart-item');
        let total = 0;
        
        cartItems.forEach(item => {
            const priceElement = item.querySelector('.item-price');
            const quantityInput = item.querySelector('.quantity-input');
            const subtotalElement = item.querySelector('.item-subtotal');
            
            if (priceElement && quantityInput && subtotalElement) {
                const price = parseFloat(priceElement.textContent.replace(/[^\d.,]/g, '').replace(',', '.'));
                const quantity = parseInt(quantityInput.value);
                const subtotal = price * quantity;
                
                subtotalElement.textContent = subtotal.toFixed(2) + ' ' + this.t('currency');
                total += subtotal;
            }
        });
        
        const totalElement = document.querySelector('.total-price');
        if (totalElement) {
            totalElement.textContent = total.toFixed(2) + ' ' + this.t('currency');
        }
    }
    
    checkEmptyCart() {
        const cartItems = document.querySelectorAll('.cart-item');
        if (cartItems.length === 0) {
            const cartContainer = document.querySelector('.cart-items');
            if (cartContainer) {
                cartContainer.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <h2>${this.t('cartEmpty')}</h2>
                        <p>${this.t('cartEmptyMessage')}</p>
                        <a href="products.php" class="btn btn-primary">${this.t('continueShopping')}</a>
                    </div>
                `;
            }
        }
    }
    
    animateAddToCart(button) {
        const cartIcon = document.querySelector('.cart-icon');
        if (!cartIcon) return;
        
        const buttonRect = button.getBoundingClientRect();
        const cartRect = cartIcon.getBoundingClientRect();
        
        const flyItem = document.createElement('div');
        flyItem.textContent = '🛍️';
        flyItem.style.cssText = `
            position: fixed;
            left: ${buttonRect.left}px;
            top: ${buttonRect.top}px;
            font-size: 24px;
            z-index: 9999;
            pointer-events: none;
            transition: all 0.8s cubic-bezier(0.2, 1, 0.3, 1);
        `;
        
        document.body.appendChild(flyItem);
        
        requestAnimationFrame(() => {
            flyItem.style.left = cartRect.left + 'px';
            flyItem.style.top = cartRect.top + 'px';
            flyItem.style.transform = 'scale(0.1)';
            flyItem.style.opacity = '0';
        });
        
        setTimeout(() => {
            flyItem.remove();
        }, 800);
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
        `;
        
        // Colors for different types
        const colors = {
            success: '#4CAF50',
            error: '#F44336',
            warning: '#FF9800',
            info: '#2196F3'
        };
        
        notification.style.backgroundColor = colors[type] || colors.info;
        
        document.body.appendChild(notification);
        
        // Show animation
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });
        
        // Auto hide
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    async liveSearch(query) {
        // Live search functionality (if needed)
        if (query.length < 2) return;
        
        try {
            const response = await fetch('ajax/live_search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    query: query,
                    csrf_token: this.csrfToken
                })
            });
            
            const data = await response.json();
            // Handle search results
        } catch (error) {
            console.error('Live search error:', error);
        }
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    new HandmadeShop();
});

// Language switching function
function changeLanguage(lang) {
    fetch('ajax/change_language.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'language=' + lang + '&csrf_token=' + document.querySelector('meta[name="csrf-token"]').content
    }).then(() => {
        location.reload();
    });
}