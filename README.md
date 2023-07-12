## Details
- Run <code>composer install</code>
- Enable env and run <code>php artisan key:generate</code>
- Run <code>php artisan test</code> to run all tests

## Others
-Added <code>->andReturn($affiliate);</code> at line 59 in<code>OrderServiceTest.php</code> to make it pass because affiliate register function was returning affiliate and it was missing while mocking the function in test.

-Added one additional attribute <code>external_order_id</code> inside <code>Order</code> migration/model because according to the logic inside <code>OrderSeviceTest.php</code> and to make it pass, this was a must attirbute.