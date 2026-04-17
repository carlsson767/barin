$(document).ready(function() {

    function updateSubtotal() {
        let subtotal = 0;
        $('.item').each(function() {
            const pricePerItem = parseFloat($(this).find('.total-price').data('price-per-item'));
            const quantity = parseInt($(this).find('.quantity input').val());
            if (!isNaN(pricePerItem) && !isNaN(quantity)) {
                subtotal += pricePerItem * quantity;
            }
        });
        $('#cart-subtotal').text(Number(subtotal.toFixed(2)));
    }

    function updateItemPrice(itemElement) {
        const pricePerItem = parseFloat(itemElement.find('.total-price').data('price-per-item'));
        const quantity = parseInt(itemElement.find('.quantity input').val());
        const totalPriceElement = itemElement.find('.total-price');
        
        if (!isNaN(pricePerItem) && !isNaN(quantity)) {
            const newTotal = Number((pricePerItem * quantity).toFixed(2));
            totalPriceElement.text(newTotal + ' руб.');
        }
    }

    function updateCart(productId, quantity, inputElement) {
        $.post('z1_cart_handler.php', { action: 'update', id: productId, quantity: quantity }, function(response) {
            if (response.total_items > 0) {
                $('.cart-count').text(response.total_items).show();
            } else {
                $('.cart-count').hide();
            }
            if (!response.success) {
                alert(response.message);
                if(inputElement) {
                    // Если на сервере количество уменьшилось, обновляем его в поле
                    const serverQty = response.message.match(/\d+/);
                    if(serverQty) {
                        inputElement.val(serverQty[0]);
                        updateItemPrice(inputElement.closest('.item'));
                        updateSubtotal();
                    }
                }
            }
        }, 'json').fail(() => alert('Ошибка обновления корзины.'));
    }

    $('.minus-btn').on('click', function(e) {
        e.preventDefault();
        const $this = $(this);
        const $input = $this.prev('input');
        let value = parseInt($input.val());

        if (value > 1) {
            value = value - 1;
        } else {
            value = 1;
        }

        $input.val(value);
        const itemElement = $this.closest('.item');
        updateItemPrice(itemElement);
        updateSubtotal();
        updateCart(itemElement.data('product-id'), value);
    });

    $('.plus-btn').on('click', function(e) {
        e.preventDefault();
        const $this = $(this);
        const $input = $this.next('input');
        let value = parseInt($input.val());
        const stock = parseInt($input.data('stock'));

        if (value < stock) {
            value = value + 1;
            $input.val(value);
            const itemElement = $this.closest('.item');
            updateItemPrice(itemElement);
            updateSubtotal();
            updateCart(itemElement.data('product-id'), value, $input);
        } else {
            alert('Больше нет в наличии');
        }
    });

    $('.delete-btn').on('click', function() {
        const itemElement = $(this).closest('.item');
        const productId = itemElement.data('product-id');

        $.post('z1_cart_handler.php', { action: 'remove', id: productId }, function(response) {
            if (response.success) {
                itemElement.remove();
                updateSubtotal();
                if (response.total_items > 0) {
                    $('.cart-count').text(response.total_items).show();
                } else {
                    $('.cart-count').hide();
                    $('.shopping-cart').html('<div class="item"><p style="width: 100%; text-align: center; padding: 40px 0;">Ваша корзина пуста.</p></div><div class="cart-footer" style="justify-content: center;"><a href="z1_shop.php" class="btn-checkout">Перейти в магазин</a></div>');
                }
            } else {
                alert(response.message);
            }
        }, 'json').fail(() => alert('Ошибка при удалении товара.'));
    });

    // --- Логика очистки корзины ---
    // Используем делегирование событий, чтобы кнопка работала даже после динамических изменений
    $('.shopping-cart').on('click', '.btn-clear-cart', function() {
        if (confirm('Вы уверены, что хотите полностью очистить корзину?')) {
            $.post('z1_cart_handler.php', { action: 'clear' }, function(response) {
                if (response.success) {
                    // Скрываем счетчик в навигации
                    $('.cart-count').hide();
                    // Заменяем содержимое корзины на сообщение о том, что она пуста
                    $('.shopping-cart').html('<div class="title">Корзина</div><div class="item"><p style="width: 100%; text-align: center; padding: 40px 0;">Ваша корзина пуста.</p></div><div class="cart-footer" style="justify-content: center;"><a href="menu.php" class="btn-checkout">Перейти в меню</a></div>');
                } else {
                    alert(response.message || 'Произошла ошибка при очистке корзины.');
                }
            }, 'json').fail(() => alert('Ошибка при отправке запроса на очистку корзины.'));
        }
    });

    $('.like-btn').on('click', function() {
        $(this).toggleClass('is-active');
    });
});