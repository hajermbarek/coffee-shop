-- ============================================================
-- Cozy Café – menu_items seed data
-- Run with:  php bin/console dbal:run-sql "$(cat menu_items_seed.sql)"
-- Or paste directly into your DB client / phpMyAdmin
-- ============================================================

TRUNCATE TABLE menu_items;

-- ----------------------------------------------------------------
-- HOT DRINKS
-- ----------------------------------------------------------------
INSERT INTO menu_items (category_slug, name, description, price, image_url, is_popular) VALUES
('hot-drinks', 'Espresso',         'Strong & rich coffee shot',                              '3.5 TND',              'https://www.sharmispassions.com/wp-content/uploads/2012/07/espresso-coffee-recipe04-500x500.jpg', 1),
('hot-drinks', 'Americano',        'Lighter Espresso (long black)',                           'Sm: 4 TND | La: 4.5 TND', 'https://myeverydaytable.com/wp-content/uploads/AmericanoHotandIced-3.jpg', 0),
('hot-drinks', 'Cappuccino',       'Espresso with milk foam and chocolate',                   'Sm: 5.5 TND | La: 7 TND', 'https://www.livingnorth.com/images/media/articles/food-and-drink/eat-and-drink/coffee.png?fm=webp&w=1000', 1),
('hot-drinks', 'Latte',            'Espresso with steamed milk',                              'Sm: 5 TND | La: 6.5 TND', 'https://www.brighteyedbaker.com/wp-content/uploads/2024/07/Dulce-de-Leche-Latte-Recipe-500x500.jpg', 0),
('hot-drinks', 'Flat White',       'Double ristretto with velvety microfoam',                 'Sm: 5 TND | La: 6.5 TND', 'https://images.arla.com/recordid/8763AA65-2EDD-4328-80C50FD4BB9B9EFE/picture.jpg?width=375&height=469&mode=crop&format=webp', 0),
('hot-drinks', 'Macchiato',        'Espresso "stained" with a dash of milk foam',             'Sm: 5 TND | La: 6.5 TND', 'https://thelittlestcrumb.com/wp-content/uploads/salted-caramel-macchiato-featured-image-1.jpg', 0),
('hot-drinks', 'Mocha',            'Espresso with chocolate and steamed milk',                'Sm: 6 TND | La: 6.5 TND', 'https://www.pamperedchef.com/iceberg/com/recipe/2131269-lg.jpg', 0),
('hot-drinks', 'Hot Chocolate',    'Rich and creamy Belgian chocolate drink',                 '6 TND',                'https://dfjx2uxqg3cgi.cloudfront.net/img/photo/219501/219501_00_2x.jpg?20201015061052', 0),
('hot-drinks', 'Green Mint Tea',   'Fresh brewed Moroccan mint tea',                          'Sm: 2 TND | La: 3 TND', 'https://www.popsci.com/wp-content/uploads/2024/02/15/steeping-tea.png?quality=85&w=2000', 0),
('hot-drinks', 'Black Tea',        'Classic bold black tea',                                  'Sm: 2 TND | La: 3 TND', 'https://cdn.shopify.com/s/files/1/0022/1393/7252/articles/20221114103112-dark-tea-recipe-blog.jpg?v=1668422229', 0),
('hot-drinks', 'Chamomile Tea',    'Calming herbal chamomile infusion',                       'Sm: 5 TND | La: 7 TND', 'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae17_Chamomile%2520Tea-p-500.png', 0),
('hot-drinks', 'London Fog',       'Earl Grey tea with steamed milk and vanilla',             'Sm: 6 TND | La: 7 TND', 'https://cdn.shopify.com/s/files/1/0659/8043/2572/files/20241203194106-london-fog-latte_001.jpg?v=1733254868&width=600&height=900', 0),
('hot-drinks', 'Chai Latte',       'Spiced masala tea with steamed milk',                     'Sm: 5.5 TND | La: 7 TND', 'https://www.livingnorth.com/images/media/articles/food-and-drink/eat-and-drink/coffee.png?fm=webp&w=1000', 0),
('hot-drinks', 'Dirty Chai Latte', 'Chai latte with a shot of espresso',                     'Sm: 10 TND | La: 12 TND', 'https://midwestniceblog.com/wp-content/uploads/2022/09/homemade-dirty-chai-latte-recipe.jpg', 1),
('hot-drinks', 'Matcha Latte',     'Japanese ceremonial matcha with steamed milk',            'Sm: 10 TND | La: 13 TND', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSFDf6BgVL_nG-3RscVS7bMUhD7-RjVgaGQiQ&s', 1),
('hot-drinks', 'Ube Latte',        'Purple yam latte – a taste of the Philippines',          'Sm: 16 TND | La: 19 TND', 'https://www.coffeemakers.de/wpcm/wp-content/uploads/2026/01/ube-latte.jpg', 1);

-- ----------------------------------------------------------------
-- COLD DRINKS
-- ----------------------------------------------------------------
INSERT INTO menu_items (category_slug, name, description, price, image_url, is_popular) VALUES
('cold-drinks', 'Iced Espresso',          'Chilled espresso over ice',                           '3.8 TND',               'https://coffeegeek.com/wp-content/uploads/2023/10/icedespressostepsoops-1-1024x683.jpg', 0),
('cold-drinks', 'Iced Americano',         'Espresso diluted with cold water over ice',            'Sm: 4.3 TND | La: 4.8 TND', 'https://images.ctfassets.net/v601h1fyjgba/1vlXSpBbgUo9yLzh71tnOT/a1afdbe54a383d064576b5e628035f04/Iced_Americano.jpg', 0),
('cold-drinks', 'Iced Cappuccino',        'Cold cappuccino with milk foam',                       'Sm: 6 TND | La: 7.3 TND', 'https://images.ctfassets.net/v601h1fyjgba/1eje3eJjFrd8FkYxT2jniv/1bd8c902f5e48d95fdd3183c9993ed04/Lite_Iced_Cappuccino_Hi__1_.jpg', 0),
('cold-drinks', 'Iced Cafe Latte',        'Espresso with cold milk over ice',                     'Sm: 6 TND | La: 7.5 TND', 'https://images.ctfassets.net/v601h1fyjgba/71VWCR6Oclk14tsdM9gTyM/6921cc6b21746f62846c99fa6a872c35/Iced_Latte.jpg', 1),
('cold-drinks', 'Blueberry Iced Latte',   'Espresso with blueberry syrup and cold milk',          'Sm: 7 TND | La: 8 TND',  'https://www.simplyorganic.com/media/wysiwyg/tmp/blueberry-iced-latte-thumb_1.jpg', 1),
('cold-drinks', 'Iced Lavender Latte',    'Floral lavender syrup with espresso and cold milk',    'Sm: 8.5 TND | La: 10 TND', 'https://images.arla.com/recordid/8763AA65-2EDD-4328-80C50FD4BB9B9EFE/picture.jpg?width=375&height=469&mode=crop&format=webp', 0),
('cold-drinks', 'Iced Caramel Macchiato', 'Vanilla milk with espresso and caramel drizzle',      'Sm: 8 TND | La: 9.5 TND', 'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae0b_Iced%2520Caramel%2520Macchiato-p-500.png', 1),
('cold-drinks', 'Iced Cafe Mocha',        'Chocolate espresso drink over ice',                    'Sm: 8.5 TND | La: 10 TND', 'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae14_Copy%2520of%2520Iced%2520Cafe%2520Mocha-p-500.png', 0),
('cold-drinks', 'Iced Chocolate',         'Rich chilled chocolate drink',                         '9 TND',                 'https://images.mummypages.co.uk/images/1842/92/4/9_3/cold%2Brink%2Bchocolate.jpg', 0),
('cold-drinks', 'Iced Green Mint Tea',    'Chilled Moroccan mint tea over ice',                   'Sm: 5 TND | La: 7 TND',  'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae2d_Iced%2520Green%2520Tea-p-500.png', 0),
('cold-drinks', 'Iced Black Tea',         'Cold brewed black tea over ice',                       'Sm: 5 TND | La: 7 TND',  'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae2f_Iced%2520Black%2520Tea-p-500.png', 0),
('cold-drinks', 'Iced Chai Latte',        'Spiced chai with cold milk over ice',                  'Sm: 7 TND | La: 9 TND',  'https://midwestniceblog.com/wp-content/uploads/2023/06/starbucks-iced-vanilla-chai.jpg', 0),
('cold-drinks', 'Iced Dirty Chai Latte',  'Chai latte shot with espresso, served cold',           'Sm: 14 TND | La: 16 TND', 'https://www.orchidsandsweettea.com/wp-content/uploads/2022/06/Dirty-Chai-Maple-Iced-Latte.jpg', 0);

-- ----------------------------------------------------------------
-- BLENDED
-- ----------------------------------------------------------------
INSERT INTO menu_items (category_slug, name, description, price, image_url, is_popular) VALUES
('blended', 'Strawberry Smoothie',  'Fresh blended strawberry, banana and milk',     '8 TND',              'https://www.livveganstrong.com/wp-content/uploads/2023/07/strawberry-smoothie-6.jpg', 1),
('blended', 'Fruit Sorbet',         'Blended seasonal fruit sorbet',                 '8 TND',              'https://i.ytimg.com/vi/1wVS8h33ttU/hq720.jpg', 0),
('blended', 'Mango Smoothie',       'Tropical mango blended smooth',                 '10 TND',             'https://www.purelykaylie.com/wp-content/uploads/2021/07/mango-banana-smoothie-3.jpg', 0),
('blended', 'Blueberry Smoothie',   'Blueberry and raspberry blended fresh',         '10 TND',             'https://evergreenkitchen.ca/wp-content/uploads/2024/03/Blueberry-Raspberry-Smoothie-Evergreen-Kitchen-1.jpg', 0),
('blended', 'Our Milkshakes',       'Creamy handcrafted milkshakes',                 '9 TND',              'https://magicalbutter.com/cdn/shop/articles/vkkaghljdtrftxdlzhxa.jpg?v=1692647211', 0),
('blended', 'Smoothie',             'Mixed fruit smoothie of the day',               '10 TND',             'https://cascadeeyeskin.com/wp-content/uploads/2025/07/6-smoothie-recipes-that-support-healthy-eyes.jpg', 0),
('blended', 'Caramel Frappe',       'Blended caramel coffee frappe',                 '11 TND',             'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae1b_Copy%2520of%2520Matcha%2520Frappe-p-500.png', 1),
('blended', 'Chocolate Frappe',     'Blended chocolate coffee frappe',               '10 TND',             'https://t3.ftcdn.net/jpg/05/14/49/48/360_F_514494803_JVTzwZZAdbiKOF9AAegiPQNclu8mlfJI.jpg', 0),
('blended', 'Iced Green Mint Tea',  'Blended mint tea over crushed ice',             'Sm: 5 TND | La: 7 TND', 'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae2d_Iced%2520Green%2520Tea-p-500.png', 0),
('blended', 'Iced Black Tea',       'Blended cold black tea',                        'Sm: 5 TND | La: 7 TND', 'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae2f_Iced%2520Black%2520Tea-p-500.png', 0),
('blended', 'Iced Chai Latte',      'Blended spiced chai with ice',                  'Sm: 7 TND | La: 9 TND', 'https://midwestniceblog.com/wp-content/uploads/2023/06/starbucks-iced-vanilla-chai.jpg', 0),
('blended', 'Matcha Frappe',        'Japanese matcha blended with ice',              'Sm: 15 TND',         'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae1b_Copy%2520of%2520Matcha%2520Frappe-p-500.png', 1),
('blended', 'Mocha Frappe',         'Coffee and chocolate blended frappe',           '12.5 TND',           'https://cdn.prod.website-files.com/649249d29a20bd6bc3deac48/649249d29a20bd6bc3deae19_Copy%2520of%2520Mocha%2520Frappe-p-500.png', 0);

-- ----------------------------------------------------------------
-- BISCUITS, COOKIES & CREPES
-- ----------------------------------------------------------------
INSERT INTO menu_items (category_slug, name, description, price, image_url, is_popular) VALUES
('biscuit', 'Sablé Breton',                     'Classic French buttery shortbread',                    '(1Kg) 13 TND', 'https://www.abakingjourney.com/wp-content/uploads/2021/12/Sables-Bretons-Feature.jpg', 0),
('biscuit', 'Fingerprint Biscuits',              'Jam-filled thumbprint cookies',                        '(1Kg) 13 TND', 'https://www.allrecipes.com/thmb/VSQ2cUCwvGZQFOEl22BoSfwCOd0=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/ALR-269703-perfect-thumbprint-cookies-VAT-2x1-5bfbb5237bba467c86138847fb46ab8b.jpg', 0),
('biscuit', 'Sablé',                             'Classic French shortbread biscuit',                    '(1Kg) 13 TND', 'https://www.arosteguy.com/img/modules/oh_recipes/recipes/60_picture-large.jpg?20260311214740', 0),
('biscuit', 'Dates Maamoul',                     'Traditional stuffed date cookies',                     '(1Kg) 15 TND', 'https://www.lebonsweets.com/wp-content/uploads/2021/11/Dates.jpg', 0),
('biscuit', 'Carrot Cake Cookies',               'Soft spiced cookies with carrot cake flavour',         '7 TND',        'https://www.deliciousmagazine.co.uk/wp-content/uploads/2022/03/DEL_2022_Q2_TOBY_SCOTT_CARROT-CAKE-BISCUITS-1_960x1200-623x500.jpg', 0),
('biscuit', 'Classic Chocolate Chip Cookie',     'Timeless golden cookie packed with chocolate chips',   '5 TND',        'https://freshaprilflours.com/wp-content/uploads/2020/12/classic-ccc-RECIPE-CARD.jpg', 1),
('biscuit', 'Chocolate Hazelnut Cookie',         'Rich hazelnut praline chocolate cookie',               '8 TND',        'https://static01.nyt.com/images/2022/11/30/dining/GK-Chocolate-Hazelnut-Cookies-COOKIEWEEK/merlin_216761652_25bd8833-b27f-4609-8035-03644ff28bba-mediumSquareAt3X.jpg', 1),
('biscuit', 'Cashew Nut & Raisin Oatmeal Cookie','Hearty oatmeal cookie with cashews and raisins',       '8 TND',        'https://images.squarespace-cdn.com/content/v1/5e7cc7c6d8321064ea1385da/1617899483464-PCPT2LM2TBPHJHBFRTH1/oatmealraisincookies-4.jpg', 0),
('biscuit', 'Basic Sugar Crepe',                 'Light thin crepe dusted with sugar',                   '6 TND',        'https://hips.hearstapps.com/hmg-prod/images/crepes-index-64347419e3c7a.jpg?crop=0.503xw:1.00xh;0.234xw,0&resize=1200:*', 0),
('biscuit', 'Chocolate Crepe',                   'Thin crepe with chocolate filling',                    '6 TND',        'https://www.tasteofhome.com/wp-content/uploads/2025/02/Chocolate-Crepes_EXPS_TOHD24_33392_ChristineMa_11.jpg', 0),
('biscuit', 'Chocolate Filled Crepe',            'Crepe generously filled with Nutella',                 '8 TND',        'https://www.chelseasmessyapron.com/wp-content/uploads/2019/05/Nutella-Crepes-ChelseasMessyApron-1200-3.jpg', 1);

-- ----------------------------------------------------------------
-- CAKES
-- ----------------------------------------------------------------
INSERT INTO menu_items (category_slug, name, description, price, image_url, is_popular) VALUES
('cake', 'Fraisier',                 'French strawberry cream cake',                          '7 TND',  'https://www.delscookingtwist.com/wp-content/uploads/2023/05/Fraisier-Cake_French-Strawberry-Cake_6.jpg', 0),
('cake', 'Red Velvet',               'Classic red velvet with cream cheese frosting',         '8 TND',  'https://www.recipetineats.com/tachyon/2016/06/Red-Velvet-Layer-Cake_4.jpg?resize=500%2C500', 1),
('cake', 'Chocolate Cake',           'Rich layered chocolate cake',                           '8 TND',  'https://alpineella.com/wp-content/uploads/2024/11/chocolate-cake-with-chocolate-ganache-13.jpg', 0),
('cake', 'Choco-Loco',               'Double chocolate ganache indulgence',                   '10 TND', 'https://foodal.com/wp-content/uploads/2020/09/Chocolate-Cake-and-Cream-Filling-Recipe.jpg', 1),
('cake', 'Chocolate Strawberry Cake','Chocolate sponge with fresh strawberries',              '9 TND',  'https://www.mybakingaddiction.com/wp-content/uploads/2023/06/slice-of-chocolate-strawberry-cake-700x1050.jpg', 0),
('cake', 'Blueberry Chiffon Cake',   'Light airy chiffon cake with blueberry compote',       '12 TND', 'https://teakandthyme.com/wp-content/uploads/2024/10/blueberry-chiffon-cake-DSC_2684-1600.jpg', 0),
('cake', 'Tuxedo Truffle Cake',      'Elegant white and dark chocolate truffle cake',         '10 TND', 'https://d2lnr5mha7bycj.cloudfront.net/product-image/file/large_63ba1e3e-daeb-4c54-bed1-9adbcd3040e0.jpeg', 0),
('cake', 'Cheese Cake',              'Creamy no-bake cheesecake',                             '12 TND', 'https://www.recipetineats.com/tachyon/2024/09/No-bake-cheesecake_8.jpg', 1),
('cake', 'Tiramisu',                 'Classic Italian espresso and mascarpone dessert',       '10 TND', 'https://www.jiffymix.com/wp-content/uploads/2020/06/Tiramisu-Cake-2.jpg', 1),
('cake', 'Carrot Cake',              'Spiced carrot cake with cream cheese frosting',         '10 TND', 'https://www.allrecipes.com/thmb/FdnjmAgpd-a2Df99LIY6wRsRrFQ=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/AR-17393-Best-Carrot-Cake-Ever-ddmfs-4x3-724b5c5584b04426852addcf85ac72af.jpg', 0),
('cake', 'Lemon Curd Cake',          'Tangy lemon curd layered sponge cake',                 '8 TND',  'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRfQxPUhvu2eyo0xuy75rtIIlQKs9Z3Ibfd2A&s', 0);

-- ----------------------------------------------------------------
-- PASTRY
-- ----------------------------------------------------------------
INSERT INTO menu_items (category_slug, name, description, price, image_url, is_popular) VALUES
('pastry', 'Croissant',                   'Classic buttery French croissant',                         '2 TND',   'https://cdn.tasteatlas.com/images/dishes/c743bfff128340e5b1a24f1e333f59f2.jpg', 0),
('pastry', 'Almond Croissant',            'Flaky croissant filled with almond cream',                 '3.5 TND', 'https://www.simplyrecipes.com/thmb/UijSGX9q71RpSUkS3yfOloFL27I=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/Simply-Recipes-Almond-Croissant-LEAD-8-07f51557bda7499aa6d1ccb4d079ea52.jpg', 1),
('pastry', 'Strawberry Croissant',        'Croissant with fresh strawberry jam filling',              '3.5 TND', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS6oE69AMPaG3sm2LJE3Mw8DnsCAjiXmxMylg&s', 0),
('pastry', 'Chocolate Croissant',         'Buttery croissant with dark chocolate filling',            '3.5 TND', 'https://bakeandsavor.com/wp-content/uploads/2025/10/Homemade-Chocolate-Croissant-500x500.jpg', 1),
('pastry', 'Pain au Chocolat',            'Classic French chocolate-filled pastry',                   '2 TND',   'https://www.theflavorbender.com/wp-content/uploads/2025/03/Pain-au-chocolat-1.jpg', 0),
('pastry', 'Caramel Apple Pie',           'Warm spiced apple pie with caramel drizzle',               '7.5 TND', 'https://www.allrecipes.com/thmb/ubu5gglb663tX77WBVQlVdx65Mk=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/283215-salted-caramel-apple-pie-VAT-2x1-60d2b3c288eb42eba9b92325472461ed.jpg', 0),
('pastry', 'Strawberry Chocolate Pie',    'Chocolate pastry base with fresh strawberries',            '8.5 TND', 'https://prettypies.com/wp-content/uploads/2018/02/Chocolate-Covered-Strawberry-Pie-5-Pretty-Pies.png', 0),
('pastry', 'Cheese Danish',              'Flaky pastry with creamy cheese filling',                   '5 TND',   'https://www.foodnetwork.com/content/dam/images/food/fullset/2018/11/20/0/YW1306_Cheese-Danish_s4x3.jpg', 0),
('pastry', 'Fruit and Cream Cheese Danish','Puff pastry topped with fruit and cream cheese',          '6 TND',   'https://thecozyplum.com/wp-content/uploads/2022/10/1x1-fruit-cream-cheese-puff-pastry-danish-1.jpg', 0),
('pastry', 'Mini Fruit Tart',            'Buttery tart shell with pastry cream and fresh fruit',      '6 TND',   'https://www.abakingjourney.com/wp-content/uploads/2022/04/Mini-Fruit-Tarts-Feature.jpg', 1),
('pastry', 'Blueberry Jam Montblanc',    'Elegant Montblanc with blueberry jam filling',              '9 TND',   'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSkPB-hEWTy608uWyjnRoY8g8lB_tw18CFUGQ&s', 0),
('pastry', 'Strawberry Montblanc',       'Delicate Montblanc with fresh strawberry',                  '9 TND',   'https://static.wixstatic.com/media/b8697b_7493c62230624398ae0f64363001cd1b~mv2.jpg/v1/fill/w_568,h_852,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/b8697b_7493c62230624398ae0f64363001cd1b~mv2.jpg', 0),
('pastry', 'Apple Jam Montblanc',        'Classic Montblanc with sweet apple jam',                   '9 TND',   'https://cdn.trendhunterstatic.com/thumbs/541/apple-jam-mont-blanc.jpeg?auto=webp', 0),
('pastry', 'Cinnamon Roll',              'Warm soft roll with cinnamon sugar and cream cheese glaze', '8 TND',   'https://www.allrecipes.com/thmb/MjuSC7L1sNlk8UDrddQZ8g-z1fI=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/275744-homemade-cinnamon-rolls-VAT-006-4x3-04singlerollmoreicing-b9dad55293644d4bb576d7ca649e2043.jpg', 1);
