-- ===========================================
-- CLEANUP DUPLICATE RELATIONSHIPS
-- ===========================================

-- 1. Clean up category_product duplicates
DELETE cp1 FROM category_product cp1
INNER JOIN category_product cp2
WHERE cp1.id > cp2.id
  AND cp1.product_id = cp2.product_id
  AND cp1.category_id = cp2.category_id;

-- 2. Clean up image_product duplicates
DELETE ip1 FROM image_product ip1
INNER JOIN image_product ip2
WHERE ip1.id > ip2.id
  AND ip1.product_id = ip2.product_id
  AND ip1.image_id = ip2.image_id
  AND ip1.img_type = ip2.img_type;

-- 3. Clean up option_product duplicates
DELETE op1 FROM option_product op1
INNER JOIN option_product op2
WHERE op1.id > op2.id
  AND op1.product_id = op2.product_id
  AND op1.option_id = op2.option_id;

-- 4. Show summary of remaining records
SELECT
    'category_product' as table_name,
    COUNT(*) as record_count
FROM category_product
UNION ALL
SELECT
    'image_product' as table_name,
    COUNT(*) as record_count
FROM image_product
UNION ALL
SELECT
    'option_product' as table_name,
    COUNT(*) as record_count
FROM option_product;
