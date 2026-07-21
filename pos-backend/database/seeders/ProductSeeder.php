<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Products array: [category_id, brand_id, product_name, description, quantity, price, discount_percent, cost_price via price-margin, min_stock_alert, is_featured, weight]
        // category_id: 1=Smartphone, 2=Laptop, 3=Tablet, 4=Accessories, 5=Computer Parts, 6=Network, 7=Desktop, 8=Printer, 9=Monitor, 10=Gaming
        // sub: 11=iPhone, 12=Android, 13=MacBook, 14=Windows Laptop, 15=Cases, 16=Chargers, 17=Earphone, 18=RAM, 19=PowerBank, 20=SmartWatch
        // brand: 1=Apple, 2=Samsung, 3=Xiaomi, 4=Oppo, 5=Vivo, 6=Dell, 7=HP, 8=Lenovo, 9=Asus, 10=Huawei, 11=Sony, 12=Realme, 13=TP-Link, 14=Acer, 15=MSI

        $products = [
            // =================== IPHONES ===================
            [11, 1, 'iPhone 16 Pro Max 256GB',      'Apple iPhone 16 Pro Max with A18 Pro chip, 6.9" Super Retina XDR, Titanium',      25, 1299.00, 0,  10, true,  0.227, 'storage/products/iphone-16-pro-max.jpg'],
            [11, 1, 'iPhone 16 Pro Max 512GB',      'Apple iPhone 16 Pro Max 512GB storage, A18 Pro chip',                             15, 1499.00, 0,  5,  true,  0.227, 'storage/products/iphone-16-pro-max-512.jpg'],
            [11, 1, 'iPhone 16 Pro 256GB',          'Apple iPhone 16 Pro, 6.3" display, A18 Pro chip, 4K120fps video',                 30, 1099.00, 0,  10, true,  0.199, 'storage/products/iphone-16-pro.jpg'],
            [11, 1, 'iPhone 15 Pro Max 256GB',      'Apple iPhone 15 Pro Max with A17 Pro chip, Titanium design',                      20, 1199.00, 5,  10, false, 0.221, 'storage/products/iphone-15-pro-max.jpg'],
            [11, 1, 'iPhone 15 128GB',              'Apple iPhone 15, 6.1" Super Retina XDR, A16 Bionic',                             40, 799.00,  0,  15, false, 0.171, 'storage/products/iphone-15.jpg'],
            [11, 1, 'iPhone 14 128GB',              'Apple iPhone 14, A15 Bionic, Emergency SOS via satellite',                        35, 699.00,  10, 10, false, 0.172, 'storage/products/iphone-14.jpg'],
            [11, 1, 'iPhone SE 3rd Gen 64GB',       'Apple iPhone SE 3rd Gen, A15 Bionic, most affordable iPhone',                     50, 429.00,  0,  20, false, 0.144, 'storage/products/iphone-se3.jpg'],

            // =================== ANDROID PHONES ===================
            [12, 2, 'Samsung Galaxy S25 Ultra 256GB','Samsung Galaxy S25 Ultra, Snapdragon 8 Elite, 200MP camera, S-Pen included',    30, 1299.00, 0,  10, true,  0.218, 'storage/products/samsung-s25-ultra.jpg'],
            [12, 2, 'Samsung Galaxy S25+ 256GB',    'Samsung Galaxy S25+, Snapdragon 8 Elite, 50MP camera, 4900mAh',                  25, 999.00,  0,  10, true,  0.196, 'storage/products/samsung-s25-plus.jpg'],
            [12, 2, 'Samsung Galaxy S25 128GB',     'Samsung Galaxy S25, Snapdragon 8 Elite, 50MP camera',                            35, 799.00,  0,  15, false, 0.162, 'storage/products/samsung-s25.jpg'],
            [12, 2, 'Samsung Galaxy A55 128GB',     'Samsung Galaxy A55, Exynos 1480, 50MP triple camera',                            60, 349.00,  0,  20, false, 0.213, 'storage/products/samsung-a55.jpg'],
            [12, 2, 'Samsung Galaxy A35 128GB',     'Samsung Galaxy A35, Exynos 1380, 50MP camera, 5000mAh',                          80, 279.00,  5,  20, false, 0.201, 'storage/products/samsung-a35.jpg'],
            [12, 3, 'Xiaomi 15 Pro 256GB',          'Xiaomi 15 Pro, Snapdragon 8 Elite, Leica cameras, 6000mAh',                      20, 799.00,  0,  10, true,  0.213, 'storage/products/xiaomi-15-pro.jpg'],
            [12, 3, 'Xiaomi Redmi Note 14 Pro 256GB','Xiaomi Redmi Note 14 Pro, Snapdragon 7s Gen 3, 200MP camera',                   50, 299.00,  0,  20, false, 0.210, 'storage/products/redmi-note-14-pro.jpg'],
            [12, 3, 'Xiaomi Redmi 13C 128GB',       'Xiaomi Redmi 13C, MediaTek Helio G85, 50MP, 5000mAh, budget phone',             100, 139.00,  0,  30, false, 0.192, 'storage/products/redmi-13c.jpg'],
            [12, 4, 'Oppo Reno 13 Pro 256GB',       'Oppo Reno 13 Pro, Dimensity 8350, 50MP Hasselblad portrait camera',              25, 499.00,  0,  10, false, 0.183, 'storage/products/oppo-reno13-pro.jpg'],
            [12, 4, 'Oppo A3 Pro 128GB',            'Oppo A3 Pro, rugged design, Dimensity 7050, 45W fast charge',                    70, 199.00,  0,  25, false, 0.196, 'storage/products/oppo-a3-pro.jpg'],
            [12, 5, 'Vivo X100 Pro 256GB',          'Vivo X100 Pro, Dimensity 9300, ZEISS cameras, 5400mAh',                          15, 699.00,  0,  10, false, 0.215, 'storage/products/vivo-x100-pro.jpg'],
            [12, 5, 'Vivo Y36 128GB',               'Vivo Y36, Snapdragon 680, 50MP, 44W flash charge, 5000mAh',                      90, 179.00,  5,  25, false, 0.188, 'storage/products/vivo-y36.jpg'],
            [12, 10,'Huawei Nova 12 Pro 256GB',     'Huawei Nova 12 Pro, Kirin 8000 5G, 60MP periscope camera',                       20, 399.00,  0,  10, false, 0.189, 'storage/products/huawei-nova12-pro.jpg'],
            [12, 12,'Realme 12 Pro+ 256GB',         'Realme 12 Pro+, Snapdragon 7s Gen 2, 64MP periscope zoom camera',               45, 259.00,  0,  20, false, 0.200, 'storage/products/realme-12-pro-plus.jpg'],

            // =================== TABLETS ===================
            [3, 1, 'iPad Pro M4 11-inch 256GB WiFi', 'Apple iPad Pro M4, 11-inch OLED, M4 chip, thin and powerful',                  10, 999.00,  0,  5,  true,  0.450, 'storage/products/ipad-pro-m4-11.jpg'],
            [3, 1, 'iPad Pro M4 13-inch 256GB WiFi', 'Apple iPad Pro M4, 13-inch OLED, M4 chip, with Ultra Retina display',           8, 1299.00, 0,  5,  true,  0.579, 'storage/products/ipad-pro-m4-13.jpg'],
            [3, 1, 'iPad Air M2 11-inch 128GB WiFi', 'Apple iPad Air M2, 11-inch Liquid Retina, M2 chip',                            15, 599.00,  0,  8,  false, 0.462, 'storage/products/ipad-air-m2.jpg'],
            [3, 1, 'iPad 10th Gen 64GB WiFi',        'Apple iPad 10th generation, 10.9-inch, A14 Bionic',                            20, 349.00,  5,  10, false, 0.477, 'storage/products/ipad-10th.jpg'],
            [3, 2, 'Samsung Galaxy Tab S9 Ultra 256GB','Samsung Galaxy Tab S9 Ultra, 14.6-inch AMOLED, Snapdragon 8 Gen 2',          8, 1099.00, 0,  5,  true,  0.732, 'storage/products/samsung-tab-s9-ultra.jpg'],
            [3, 2, 'Samsung Galaxy Tab A9+ 128GB',   'Samsung Galaxy Tab A9+, 11-inch LCD, Snapdragon 695',                          25, 279.00,  0,  10, false, 0.480, 'storage/products/samsung-tab-a9-plus.jpg'],

            // =================== MACBOOKS ===================
            [13, 1, 'MacBook Pro M4 14-inch 512GB',  'Apple MacBook Pro M4, 14.2-inch Liquid Retina XDR, 16GB RAM, 512GB SSD',      12, 1999.00, 0,  5,  true,  1.55,  'storage/products/macbook-pro-m4-14.jpg'],
            [13, 1, 'MacBook Pro M4 Pro 16-inch 1TB','Apple MacBook Pro M4 Pro, 16-inch, 24GB RAM, 1TB SSD, ProRes video',          8, 2999.00, 0,  3,  true,  2.14,  'storage/products/macbook-pro-m4-pro-16.jpg'],
            [13, 1, 'MacBook Air M3 13-inch 256GB',  'Apple MacBook Air M3, 13.6-inch, 8GB RAM, 256GB SSD, 18hr battery',           20, 1099.00, 0,  8,  true,  1.24,  'storage/products/macbook-air-m3-13.jpg'],
            [13, 1, 'MacBook Air M3 15-inch 256GB',  'Apple MacBook Air M3, 15.3-inch, 8GB RAM, 256GB SSD, larger display',        15, 1299.00, 0,  5,  false, 1.51,  'storage/products/macbook-air-m3-15.jpg'],

            // =================== WINDOWS LAPTOPS ===================
            [14, 6, 'Dell XPS 15 9530 i7 16GB 512GB','Dell XPS 15, Intel Core i7-13700H, 16GB RAM, 512GB SSD, OLED display',       10, 1599.00, 0,  5,  true,  1.86,  'storage/products/dell-xps-15.jpg'],
            [14, 6, 'Dell Latitude 5540 i5 16GB 512GB','Dell Latitude 5540, Intel Core i5-1345U, 16GB RAM, 512GB SSD, business',   15, 999.00,  5,  5,  false, 1.75,  'storage/products/dell-latitude-5540.jpg'],
            [14, 6, 'Dell Inspiron 15 3520 i5 8GB 512GB','Dell Inspiron 15 3520, Core i5-1235U, 8GB RAM, 512GB SSD',               25, 649.00,  0,  10, false, 1.76,  'storage/products/dell-inspiron-15.jpg'],
            [14, 7, 'HP EliteBook 840 G10 i7 16GB 512GB','HP EliteBook 840 G10, Core i7-1355U, 16GB RAM, 512GB SSD, business',    12, 1199.00, 0,  5,  true,  1.33,  'storage/products/hp-elitebook-840.jpg'],
            [14, 7, 'HP Pavilion 15 i5 8GB 512GB',   'HP Pavilion 15, Core i5-1235U, 8GB RAM, 512GB SSD, FHD display',            30, 599.00,  0,  10, false, 1.68,  'storage/products/hp-pavilion-15.jpg'],
            [14, 7, 'HP Victus 16 Gaming i7 16GB 512GB RTX3050','HP Victus 16, i7-12700H, 16GB, 512GB, RTX 3050 Ti',              10, 899.00,  0,  5,  false, 2.20,  'storage/products/hp-victus-16.jpg'],
            [14, 8, 'Lenovo ThinkPad X1 Carbon i7 16GB 512GB','Lenovo ThinkPad X1 Carbon Gen 11, Core i7, 16GB, 512GB',          8, 1499.00, 0,  5,  true,  1.12,  'storage/products/lenovo-thinkpad-x1.jpg'],
            [14, 8, 'Lenovo IdeaPad 5 i5 8GB 512GB', 'Lenovo IdeaPad 5 15ALC7, Ryzen 5, 8GB, 512GB SSD, FHD IPS',               30, 549.00,  5,  10, false, 1.70,  'storage/products/lenovo-ideapad-5.jpg'],
            [14, 9, 'Asus Vivobook 15 i5 8GB 512GB', 'ASUS VivoBook 15, Core i5-1235U, 8GB, 512GB SSD, FHD OLED',               35, 499.00,  0,  15, false, 1.70,  'storage/products/asus-vivobook-15.jpg'],
            [14, 9, 'Asus ROG Strix G16 i7 RTX 4070','ASUS ROG Strix G16, Core i7-13650HX, 16GB, 512GB, RTX 4070',              6, 1799.00, 0,  3,  true,  2.30,  'storage/products/asus-rog-strix-g16.jpg'],
            [14, 14,'Acer Aspire 5 i5 8GB 512GB',    'Acer Aspire 5, Core i5-1235U, 8GB RAM, 512GB SSD, 15.6" FHD',             40, 499.00,  5,  15, false, 1.70,  'storage/products/acer-aspire-5.jpg'],
            [14, 14,'Acer Predator Helios 16 i9 RTX4080','Acer Predator Helios 16, i9-13900HX, 32GB, 1TB, RTX4080',             5, 2499.00, 0,  3,  true,  2.70,  'storage/products/acer-predator-helios-16.jpg'],

            // =================== ACCESSORIES - EARPHONES ===================
            [17, 1, 'Apple AirPods Pro 2nd Gen',     'Apple AirPods Pro (2nd Gen) with USB-C, ANC, 30hr battery case',            50, 249.00,  0,  15, true,  0.061, 'storage/products/airpods-pro-2.jpg'],
            [17, 1, 'Apple AirPods 4',               'Apple AirPods 4, H2 chip, Active Noise Cancellation, USB-C',                40, 129.00,  0,  15, false, 0.046, 'storage/products/airpods-4.jpg'],
            [17, 2, 'Samsung Galaxy Buds3 Pro',      'Samsung Galaxy Buds3 Pro, 360° Audio, ANC, 6hr playback',                   35, 199.00,  0,  15, false, 0.056, 'storage/products/samsung-buds3-pro.jpg'],
            [17, 11,'Sony WH-1000XM5 Headphone',    'Sony WH-1000XM5, Industry-leading ANC, 30hr battery',                        20, 349.00,  5,  8,  true,  0.250, 'storage/products/sony-wh1000xm5.jpg'],
            [17, 3, 'Xiaomi Buds 5 Pro',             'Xiaomi Buds 5 Pro, dual ANC, Hi-Res Audio, 48hr total',                     40, 89.00,   0,  15, false, 0.048, 'storage/products/xiaomi-buds-5-pro.jpg'],

            // =================== ACCESSORIES - CASES & CHARGERS ===================
            [15, 1, 'Apple Silicone Case iPhone 16 Pro Max','Genuine Apple silicone case for iPhone 16 Pro Max',                 100, 49.00,   0,  30, false, 0.030, 'storage/products/apple-case-16pm.jpg'],
            [16, 1, 'Apple 35W Dual USB-C Charger',  'Apple 35W Dual USB-C Power Adapter, charge 2 devices',                     60, 59.00,   0,  20, false, 0.095, 'storage/products/apple-35w-charger.jpg'],
            [16, 2, 'Samsung 45W Super Fast Charger', 'Samsung 45W USB-C Super Fast Charging Adapter',                            80, 29.00,   0,  25, false, 0.040, 'storage/products/samsung-45w-charger.jpg'],
            [16, 3, 'Xiaomi 120W HyperCharge Adapter','Xiaomi 120W Turbo Charge USB-C adapter',                                  70, 19.00,   0,  25, false, 0.055, 'storage/products/xiaomi-120w.jpg'],

            // =================== POWER BANKS ===================
            [19, 3, 'Xiaomi Power Bank 30000mAh 65W', 'Xiaomi 30000mAh 65W PD power bank, 3 ports',                             40, 49.00,   0,  15, false, 0.595, 'storage/products/xiaomi-powerbank-30000.jpg'],
            [19, 2, 'Samsung 20000mAh 45W Power Bank', 'Samsung 20000mAh 45W Super Fast wireless charging power bank',            30, 59.00,   5,  10, false, 0.400, 'storage/products/samsung-powerbank-20000.jpg'],

            // =================== SMART WATCHES ===================
            [20, 1, 'Apple Watch Series 10 GPS 46mm', 'Apple Watch Series 10, 46mm, S10 chip, Advanced Health Sensors',           25, 399.00,  0,  10, true,  0.036, 'storage/products/apple-watch-series10.jpg'],
            [20, 2, 'Samsung Galaxy Watch 7 44mm',    'Samsung Galaxy Watch 7, 44mm, BioActive Sensor, 40hr battery',             20, 279.00,  0,  10, false, 0.033, 'storage/products/samsung-watch7.jpg'],
            [20, 10,'Huawei Watch GT 5 Pro 46mm',    'Huawei Watch GT 5 Pro, 14-day battery, Titanium case, health AI',           15, 249.00,  5,  8,  false, 0.036, 'storage/products/huawei-gt5-pro.jpg'],

            // =================== COMPUTER PARTS (RAM & Storage) ===================
            [18, 1, 'Samsung 970 EVO Plus 1TB NVMe SSD','Samsung 970 EVO Plus 1TB M.2 NVMe SSD, 3500MB/s read',                  30, 89.00,   0,  10, false, 0.050, 'storage/products/samsung-970-evo-1tb.jpg'],
            [18, 8, 'Kingston Fury Beast 16GB DDR4 3200','Kingston FURY Beast 16GB DDR4-3200 Desktop RAM',                        40, 35.00,   0,  15, false, 0.040, 'storage/products/kingston-fury-16gb.jpg'],
            [18, 8, 'Kingston 8GB DDR4 2666',        'Kingston 8GB DDR4 2666MHz SODIMM Laptop RAM',                              50, 19.00,   0,  20, false, 0.020, 'storage/products/kingston-8gb-ddr4.jpg'],
            [5,  2, 'Samsung 860 EVO 1TB SATA SSD',  'Samsung 860 EVO 1TB 2.5" SATA SSD, 550MB/s read',                          25, 79.00,   5,  10, false, 0.060, 'storage/products/samsung-860-evo-1tb.jpg'],
            [5,  7, 'WD Blue 2TB HDD 3.5"',          'WD Blue 2TB 3.5 inch 7200RPM SATA Desktop Hard Drive',                     30, 49.00,   0,  10, false, 0.400, 'storage/products/wd-blue-2tb.jpg'],

            // =================== NETWORK DEVICES ===================
            [6, 13,'TP-Link Archer AXE75 WiFi 6E Router','TP-Link Archer AXE75, AXE5400, WiFi 6E, Tri-Band, MU-MIMO',            15, 149.00,  0,  5,  true,  0.650, 'storage/products/tplink-axe75.jpg'],
            [6, 13,'TP-Link TL-SG1024 24-Port Switch',  'TP-Link 24-Port Gigabit Desktop/Rackmount Switch',                      10, 79.00,   0,  5,  false, 2.100, 'storage/products/tplink-sg1024.jpg'],
            [6, 10,'Huawei WiFi AX3 Pro Router',         'Huawei WiFi AX3 Pro, WiFi 6+, 3000Mbps, Quad-Core',                   20, 69.00,   5,  8,  false, 0.450, 'storage/products/huawei-ax3-pro.jpg'],

            // =================== MONITORS ===================
            [9, 2, 'Samsung Odyssey G7 27" QHD 240Hz','Samsung Odyssey G7 27-inch QHD 240Hz curved gaming monitor',              8, 699.00,  0,  4,  true,  7.800, 'storage/products/samsung-odyssey-g7.jpg'],
            [9, 6, 'Dell U2723D 27" IPS 4K Monitor',  'Dell UltraSharp 27 4K USB-C Hub Monitor, IPS Black, color-accurate',     10, 549.00,  0,  4,  true,  5.550, 'storage/products/dell-u2723d.jpg'],
            [9, 9, 'Asus ProArt PA279CV 27" 4K IPS',  'ASUS ProArt 27" 4K IPS, 100% sRGB/Rec.709, USB-C, DisplayHDR',          8, 449.00,  0,  4,  false, 6.200, 'storage/products/asus-proart-pa279cv.jpg'],

            // =================== PRINTERS ===================
            [8, 7, 'HP LaserJet Pro M404dw',          'HP LaserJet Pro M404dw Wireless Monochrome Laser Printer',               5, 299.00,  5,  3,  false, 7.900, 'storage/products/hp-laserjet-m404dw.jpg'],
            [8, 7, 'HP Color LaserJet Pro M454dw',    'HP Color LaserJet Pro M454dw Wireless Color Laser Printer',              5, 499.00,  0,  3,  false, 21.50, 'storage/products/hp-color-laserjet-m454.jpg'],

            // =================== DESKTOPS ===================
            [7, 1, 'Apple Mac Mini M4 256GB',         'Apple Mac Mini with M4 chip, 16GB RAM, 256GB SSD, compact powerhouse',   8, 599.00,  0,  4,  true,  0.670, 'storage/products/mac-mini-m4.jpg'],
            [7, 6, 'Dell OptiPlex 3000 i5 8GB 256GB SSD','Dell OptiPlex 3000 Micro, Core i5, 8GB, 256GB SSD, business desktop', 10, 699.00,  0,  5,  false, 1.370, 'storage/products/dell-optiplex-3000.jpg'],

            // =================== GAMING ===================
            [10, 11,'Sony PlayStation 5 Slim 1TB',    'Sony PlayStation 5 Slim Disc Edition, DualSense controller',              5, 449.00,  0,  3,  true,  2.600, 'storage/products/ps5-slim.jpg'],
            [10, 15,'MSI MEG Trident X2 Gaming PC RTX4090','MSI MEG Trident X2, i9-13900K, 64GB, RTX4090, 2TB NVMe',            2, 3999.00, 0,  2,  true,  5.500, 'storage/products/msi-meg-trident.jpg'],
            [10, 9, 'Asus ROG Strix Scope RX TKL Keyboard','ASUS ROG Strix Scope RX TKL 80% optical gaming keyboard',           20, 129.00,  0,  8,  false, 0.695, 'storage/products/asus-rog-scope-rx.jpg'],
            [10, 2, 'Samsung Odyssey Ark 55" 4K 165Hz','Samsung Odyssey Ark 55-inch 4K Quantum Mini-LED gaming monitor',         2, 1499.00, 5,  2,  true,  19.20, 'storage/products/samsung-odyssey-ark.jpg'],

            // More products to reach 100+
            [12, 4, 'Oppo Find X8 Pro 256GB',         'Oppo Find X8 Pro, Dimensity 9400, Hasselblad triple camera 50MP',        12, 899.00,  0,  5,  true,  0.218, 'storage/products/oppo-find-x8-pro.jpg'],
            [12, 5, 'Vivo X200 Pro 512GB',             'Vivo X200 Pro, Dimensity 9400, ZEISS APO Telephoto, 6000mAh',            10, 799.00,  0,  5,  false, 0.229, 'storage/products/vivo-x200-pro.jpg'],
            [11, 1, 'iPhone 16 Plus 128GB',            'Apple iPhone 16 Plus, 6.7" Super Retina XDR, A18 chip, USB-C',          28, 899.00,  0,  10, false, 0.203, 'storage/products/iphone-16-plus.jpg'],
            [12, 2, 'Samsung Galaxy Z Fold6 256GB',    'Samsung Galaxy Z Fold6, 7.6" Foldable, Snapdragon 8 Gen 3, S Pen',       8, 1799.00, 0,  3,  true,  0.239, 'storage/products/samsung-z-fold6.jpg'],
            [12, 2, 'Samsung Galaxy Z Flip6 256GB',    'Samsung Galaxy Z Flip6, 6.7" Foldable, Snapdragon 8 Gen 3, 50MP',       10, 999.00,  0,  5,  false, 0.187, 'storage/products/samsung-z-flip6.jpg'],
            [14, 8, 'Lenovo Legion 5 Pro i7 RTX4060',  'Lenovo Legion 5 Pro, Core i7-13700H, 16GB, 512GB, RTX4060',             8, 1299.00, 0,  4,  true,  2.500, 'storage/products/lenovo-legion-5-pro.jpg'],
            [14, 6, 'Dell G16 Gaming i7 RTX4070',      'Dell G16 Gaming Laptop, Core i7-13650HX, 16GB, 512GB, RTX4070',         6, 1399.00, 0,  3,  false, 2.570, 'storage/products/dell-g16-gaming.jpg'],
            [3, 3, 'Xiaomi Pad 7 Pro 256GB',           'Xiaomi Pad 7 Pro, Snapdragon 8s Gen 3, 11.2" 2.8K, 144Hz',             18, 399.00,  0,  8,  false, 0.520, 'storage/products/xiaomi-pad-7-pro.jpg'],
            [17, 9, 'Asus ROG Cetra True Wireless',    'ASUS ROG Cetra True Wireless gaming earbuds, ANC, ESS DAC',             25, 149.00,  0,  10, false, 0.040, 'storage/products/asus-rog-cetra.jpg'],
            [6, 13,'TP-Link Deco XE75 Pro WiFi 6E Mesh','TP-Link Deco XE75 Pro Mesh WiFi 6E, 2-pack, tri-band 5400Mbps',       10, 249.00,  0,  4,  false, 1.100, 'storage/products/tplink-deco-xe75.jpg'],
            [18, 1, 'Corsair Vengeance 32GB DDR5 5600', 'Corsair Vengeance 32GB (2x16GB) DDR5-5600 Desktop Gaming RAM',         20, 89.00,   0,  8,  false, 0.080, 'storage/products/corsair-32gb-ddr5.jpg'],
            [16, 1, 'Apple MagSafe Charger 15W',       'Apple MagSafe Charger 15W for iPhone 12 and later',                    100, 38.00,   0,  30, false, 0.034, 'storage/products/apple-magsafe.jpg'],
            [20, 3, 'Xiaomi Smart Band 9 Pro',         'Xiaomi Smart Band 9 Pro, AMOLED, SpO2, 21-day battery, GPS',            60, 59.00,   0,  20, false, 0.032, 'storage/products/xiaomi-band9-pro.jpg'],
            [15, 2, 'Samsung Smart Clear View Cover S25 Ultra','Official Samsung Smart Clear View Cover for Galaxy S25 Ultra',  50, 39.00,   0,  20, false, 0.040, 'storage/products/samsung-cover-s25u.jpg'],
            [15, 3, 'Xiaomi Poco Phone Case Bundle',   'Xiaomi anti-drop frosted case for Poco X6 Pro, matte black',            80, 9.99,    0,  30, false, 0.025, 'storage/products/xiaomi-poco-case.jpg'],
            [9, 8, 'Lenovo ThinkVision T27p-30 4K USB-C','Lenovo ThinkVision 27" 4K IPS, USB-C 90W, height-adjustable',        8, 449.00,  0,  4,  false, 6.500, 'storage/products/lenovo-thinkvision-t27p.jpg'],
            [8, 3, 'Xiaomi Mi Compact Laser Projector',  'Xiaomi Mi 4K Laser Projector Cinema, 150", HDR10, 2200 lumens',       3, 999.00,  5,  2,  true,  4.500, 'storage/products/xiaomi-laser-projector.jpg'],
        ];

        $files = glob(storage_path('app/public/products/*.*'));
        $realImages = array_map(fn($f) => 'products/' . basename($f), $files);

        $insertData = [];
        foreach ($products as $index => $p) {
            $imgPath = count($realImages) > 0 ? $realImages[$index % count($realImages)] : $p[10];
            $insertData[] = [
                'category_id'     => $p[0],
                'brand_id'        => $p[1],
                'product_name'    => $p[2],
                'description'     => $p[3],
                'quantity'        => $p[4],
                'price'           => $p[5],
                'discount_percent'=> $p[6],
                'image'           => $imgPath,
                'status'          => true,
                'min_stock_alert' => $p[7],
                'is_featured'     => $p[8],
                'weight'          => $p[9],
                'gallery'         => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        DB::table('products')->insert($insertData);
    }
}
