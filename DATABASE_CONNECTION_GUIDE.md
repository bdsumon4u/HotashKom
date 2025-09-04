# Database Connection Management Guide

## Overview

This document outlines the database connection patterns used in the Oninda application, specifically for jobs that interact with both the main Oninda database and reseller databases.

## Database Connections

### 1. Default Connection (Oninda Database)

-   **Connection Name**: `mysql` (default)
-   **Usage**: All operations on the main Oninda database
-   **Access**: `DB::table()`, `Model::query()`, etc.

### 2. Reseller Connection (Reseller Databases)

-   **Connection Name**: `reseller`
-   **Usage**: Operations on individual reseller databases
-   **Access**: `DB::connection('reseller')->table()`, `Model::on('reseller')`

## Connection Pattern

### Centralized PDO Configuration

All PDO connection options are centralized in the `User::getDatabaseConfig()` method:

```php
// In app/Models/User.php
public function getDatabaseConfig()
{
    return [
        'driver' => 'mysql',
        'host' => config('database.connections.mysql.host'),
        'port' => config('database.connections.mysql.port'),
        'database' => $this->db_name,
        'username' => $this->db_username,
        'password' => $this->db_password,
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
        'options' => [
            // Connection timeout (how long to wait for initial connection)
            \PDO::ATTR_TIMEOUT => 10,

            // Don't use persistent connections for queue jobs
            \PDO::ATTR_PERSISTENT => false,

            // Set error mode to throw exceptions
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,

            // Enable prepared statements (security)
            \PDO::ATTR_EMULATE_PREPARES => false,

            // MySQL specific options
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            \PDO::MYSQL_ATTR_LOCAL_INFILE => false, // Security: disable local infile
        ],
    ];
}
```

**Benefits of Centralized Configuration:**

-   ✅ **No redundancy**: PDO options defined once
-   ✅ **Consistency**: All jobs use the same settings
-   ✅ **Easy maintenance**: Change settings in one place
-   ✅ **DRY principle**: Don't Repeat Yourself

### Connection Setup Pattern

```php
// Configure reseller database connection (PDO options are centralized in User model)
$resellerConfig = $reseller->getDatabaseConfig();
config(['database.connections.reseller' => $resellerConfig]);

// Purge and reconnect to ensure fresh connection
DB::purge('reseller');
DB::reconnect('reseller');

// Test connection before proceeding
try {
    DB::connection('reseller')->getPdo();
} catch (\Exception $e) {
    Log::error("Failed to connect to reseller database", [
        'domain' => $this->domain,
        'error' => $e->getMessage()
    ]);
    return;
}
```

### Connection Cleanup

```php
finally {
    // Always purge the reseller connection to free up resources
    DB::purge('reseller');
}
```

### Clear Separation with Comments

All database operations are clearly marked with comments:

```php
// ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====
$reseller = User::where('domain', $this->domain)->first();

// ===== RESELLER DATABASE OPERATIONS =====
$resellerOrder = Order::on('reseller')->find($this->orderId);
```

## PDO Connection Options Explained

### 1. **ATTR_TIMEOUT** (10 seconds)

```php
\PDO::ATTR_TIMEOUT => 10
```

**What it does**: Sets the connection timeout - how long to wait for initial connection to the database server.

**Why 10 seconds instead of 30**:

-   ✅ **Faster failure detection**: 10 seconds is enough for most network conditions
-   ✅ **Better user experience**: Jobs fail faster, retry sooner
-   ✅ **Resource efficiency**: Don't waste time on slow/unreachable databases
-   ✅ **Queue worker efficiency**: Workers can process other jobs faster

**When to increase**:

-   Very slow network connections
-   Remote databases with high latency
-   During database maintenance windows

### 2. **ATTR_PERSISTENT** (false)

```php
\PDO::ATTR_PERSISTENT => false
```

**What it does**: Controls whether connections are kept alive between requests.

**Why false for queue jobs**:

-   ✅ **Prevents connection pool exhaustion**: Each job gets a fresh connection
-   ✅ **Avoids memory leaks**: Connections are properly cleaned up
-   ✅ **Stateless operations**: Jobs don't inherit state from previous operations
-   ✅ **Better error isolation**: Connection issues don't affect other jobs

**When to use true**:

-   Web requests (not queue jobs)
-   High-frequency operations
-   When connection overhead is significant

### 3. **ATTR_ERRMODE** (ERRMODE_EXCEPTION)

```php
\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
```

**What it does**: Controls how PDO handles errors.

**Why ERRMODE_EXCEPTION**:

-   ✅ **Consistent error handling**: All errors become exceptions
-   ✅ **Better debugging**: Stack traces show exact error location
-   ✅ **Laravel integration**: Works well with Laravel's exception handling
-   ✅ **Try-catch blocks**: Easy to catch and handle specific errors

### 4. **ATTR_EMULATE_PREPARES** (Removed)

**What it does**: Controls whether PDO emulates prepared statements in PHP or uses native database prepared statements.

**Why removed**:

-   ✅ **Let Laravel decide**: Laravel's query builder handles SQL injection protection
-   ✅ **Better optimization**: Laravel can choose optimal approach per operation
-   ✅ **Reduced overhead**: No unnecessary PDO option setting
-   ✅ **Database driver defaults**: Use MySQL's optimal default behavior

### 6. **MySQL Specific Options**

#### **MYSQL_ATTR_INIT_COMMAND**

```php
\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
```

**What it does**: Sets the character set and collation for the connection.

**Why utf8mb4**:

-   ✅ **Unicode support**: Full UTF-8 support including emojis
-   ✅ **Consistent encoding**: Matches Laravel's default
-   ✅ **Internationalization**: Supports all languages and characters

#### **MYSQL_ATTR_USE_BUFFERED_QUERY** (Removed)

**What it does**: Controls whether queries are buffered (results loaded into memory) or unbuffered (streamed).

**Why removed**:

-   ✅ **MySQL default**: MySQL's default is already `true` (buffered)
-   ✅ **Laravel compatibility**: Laravel's query builder expects buffered results
-   ✅ **No benefit**: Setting explicitly doesn't provide any advantage
-   ✅ **Let database decide**: Use MySQL's optimal default behavior

#### **MYSQL_ATTR_LOCAL_INFILE** (false)

```php
\PDO::MYSQL_ATTR_LOCAL_INFILE => false
```

**What it does**: Controls whether LOAD DATA LOCAL INFILE is allowed.

**Why false**:

-   ✅ **Security**: Prevents potential file system access
-   ✅ **Best practice**: Disable unless specifically needed
-   ✅ **Compliance**: Meets security requirements

## Timeout Considerations

### **Different Types of Timeouts**

1. **Connection Timeout** (`ATTR_TIMEOUT`): 10 seconds

    - Time to establish initial connection
    - Network-level timeout

2. **Query Timeout**: Not set (uses database default)

    - Time for individual queries to complete
    - Can be set with `SET SESSION MAX_EXECUTION_TIME`

3. **Job Timeout**: Set in job class
    - Total time for entire job to complete
    - `public $timeout = 600;` (10 minutes)

### **Recommended Timeout Values**

```php
// For different scenarios
$timeouts = [
    'fast_operations' => 5,    // Simple reads/writes
    'normal_operations' => 10,  // Most operations
    'slow_operations' => 30,    // Complex queries, bulk operations
    'very_slow_operations' => 60, // Data migrations, large imports
];
```

## Job Improvements Made

### 1. PlaceOnindaOrder Job

**Issues Fixed:**

-   ❌ Mixed default and reseller connections without clear context
-   ❌ Poor error handling for null products data
-   ❌ No connection testing before operations
-   ❌ Missing connection cleanup

**Improvements:**

-   ✅ Clear separation of database operations with comments
-   ✅ Proper validation of reseller order data
-   ✅ Connection testing before operations
-   ✅ Comprehensive error handling
-   ✅ Automatic connection cleanup
-   ✅ Extracted methods for better organization

### 2. CopyProductToResellers Job

**Critical Bug Fixed:**

-   ❌ **SEVERE**: `source_id` was being overwritten with reseller's database ID instead of original Oninda ID
-   ❌ **Root Cause**: Using `Product::on('reseller')->create()` triggered model events that caused infinite loops
-   ❌ **Impact**: Products lost their link to original Oninda products

**Critical Fix:**

-   ✅ **Use DB facade instead of Eloquent models**: `DB::connection('reseller')->table('products')->insertGetId()`
-   ✅ **Prevent model events**: Avoid triggering `saved` events that dispatch copy jobs again
-   ✅ **Preserve source_id**: Original Oninda product ID is now correctly preserved
-   ✅ **Added verification logging**: Log actual vs expected source_id values
-   ✅ **Consistent approach**: Match the pattern used in CopyResourceToResellers job

**Related Resources Issue Fixed:**

-   ❌ **PROBLEM**: Related resources (images, categories, brands, options) were not being copied
-   ❌ **Root Cause**: Race condition between event listeners and copy job dispatch
-   ❌ **Impact**: Products copied without their relationships

**Race Condition Fix:**

-   ✅ **Moved copy job dispatch**: From Product model's `saved` event to `ManageProductImages` listener
-   ✅ **Proper execution order**: Copy job now runs after all relationships are established
-   ✅ **Event-driven approach**: Uses Laravel's event system for proper sequencing
-   ✅ **Consistent unique columns**: Use same unique columns as CopyResourceToResellers job
-   ✅ **Images**: Use `'path'` instead of `'filename'` (more unique and reliable)
-   ✅ **Brands**: Use `'slug'` as unique column (URL-friendly and unique)
-   ✅ **Categories**: Use `'slug'` as unique column (URL-friendly and unique)
-   ✅ **Comprehensive logging**: Added detailed logging for debugging
-   ✅ **Error handling**: Added try-catch blocks for relationship insertion

### 2. CopyProductToResellers Job

**Issues Fixed:**

-   ❌ Large monolithic handle method
-   ❌ Unclear connection context
-   ❌ Poor error handling
-   ❌ No connection testing

**Improvements:**

-   ✅ Split into smaller, focused methods
-   ✅ Clear database operation separation
-   ✅ Connection testing and validation
-   ✅ Better error handling and logging
-   ✅ Proper resource cleanup

### 3. CopyResourceToResellers Job

**Issues Fixed:**

-   ❌ Complex nested operations without clear context
-   ❌ Poor error handling for connection failures
-   ❌ No connection testing

**Improvements:**

-   ✅ Clear separation of database operations
-   ✅ Connection testing before operations
-   ✅ Better error handling and logging
-   ✅ Proper resource cleanup

## Best Practices

### 1. Always Use Connection Comments

```php
// ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====
$data = DB::table('users')->where('id', 1)->first();

// ===== RESELLER DATABASE OPERATIONS =====
$resellerData = DB::connection('reseller')->table('users')->where('id', 1)->first();
```

### 2. Test Connections Before Use

```php
try {
    DB::connection('reseller')->getPdo();
} catch (\Exception $e) {
    Log::error("Connection failed", ['error' => $e->getMessage()]);
    return;
}
```

### 3. Always Clean Up Connections

```php
finally {
    DB::purge('reseller');
}
```

### 4. Use Proper Error Handling

```php
try {
    // Database operations
} catch (\PDOException $e) {
    Log::error("Database connection failed: " . $e->getMessage());
    continue;
} catch (\Exception $e) {
    Log::error("Operation failed: " . $e->getMessage());
    continue;
}
```

### 5. Validate Data Before Processing

```php
if (!$products || !is_array($products) && !is_object($products)) {
    Log::error("Invalid products data", ['products' => $products]);
    return;
}
```

## Common Patterns

### Reading from Oninda, Writing to Reseller

```php
// ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====
$sourceData = DB::table('products')->where('id', $productId)->first();

// ===== RESELLER DATABASE OPERATIONS =====
DB::connection('reseller')->table('products')->insert($processedData);
```

### Using Eloquent with Reseller Connection

```php
// ===== RESELLER DATABASE OPERATIONS =====
$resellerProduct = Product::on('reseller')->create($productData);
```

### Checking Existence in Reseller Database

```php
// ===== RESELLER DATABASE OPERATIONS =====
$exists = DB::connection('reseller')
    ->table('products')
    ->where('source_id', $sourceId)
    ->exists();
```

## Error Handling Patterns

### Connection Errors

```php
catch (\PDOException $e) {
    Log::error("Database connection failed for reseller {$reseller->id}: " . $e->getMessage());
    continue; // Skip this reseller, continue with others
}
```

### Data Validation Errors

```php
if (!$validData) {
    Log::error("Invalid data", ['data' => $data]);
    return; // Exit the job
}
```

### General Errors

```php
catch (\Exception $e) {
    Log::error("Operation failed", [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    continue; // Continue with next iteration
}
```

## Monitoring and Logging

### Job Start/End Logging

```php
Log::info('Starting job', ['jobId' => $this->id, 'params' => $params]);
// ... job operations ...
Log::info('Job completed successfully', ['jobId' => $this->id]);
```

### Database Operation Logging

```php
Log::info('Database operation', [
    'operation' => 'insert',
    'table' => 'products',
    'connection' => 'reseller',
    'resellerId' => $reseller->id
]);
```

### Error Logging

```php
Log::error('Operation failed', [
    'operation' => 'copy_product',
    'productId' => $this->product->id,
    'resellerId' => $reseller->id,
    'error' => $e->getMessage()
]);
```

### Connection Monitoring

```sql
-- Monitor active connections
SHOW PROCESSLIST;

-- Check connection count
SHOW STATUS LIKE 'Threads_connected';

-- Check connection errors
SHOW STATUS LIKE 'Connection_errors%';
```

### Performance Monitoring

```sql
-- Check slow queries
SHOW VARIABLES LIKE 'slow_query_log';
SHOW VARIABLES LIKE 'long_query_time';

-- Check connection timeouts
SHOW VARIABLES LIKE 'connect_timeout';
SHOW VARIABLES LIKE 'wait_timeout';
```

## Security Considerations

### SQL Injection Prevention

```php
// ✅ Good: Let Laravel handle prepared statements
// Laravel's query builder automatically prevents SQL injection
// No need to set ATTR_EMULATE_PREPARES explicitly

// ❌ Bad: Manually setting prepared statement options
// Let Laravel and the database driver optimize this
```

## Critical: Proper Execution Order for Product Copying

### The Problem (Race Condition)

**Before Fix:**

```php
// 1. Product is created
Product::create($data);

// 2. Product model's 'saved' event fires IMMEDIATELY
//    → Dispatches CopyProductToResellers job

// 3. ProductCreated event fires
event(new ProductCreated($product, $data));

// 4. Event listeners run AFTER copy job is already dispatched
ManageProductCategories::handle($event);  // Attaches categories
ManageProductImages::handle($event);      // Attaches images

// 5. Copy job runs but finds no relationships!
//    → categoryCount: 0, imageCount: 0
```

**After Fix (Controller-Based Approach):**

```php
// 1. Product is created
$product = Product::create($data);

// 2. Categories are attached immediately
$product->categories()->sync($data['categories']);

// 3. Images are attached immediately
$product->images()->sync($images);

// 4. Copy job is dispatched AFTER all relationships are established
CopyProductToResellers::dispatch($product);

// 5. Copy job runs with complete relationships
//    → categoryCount: N, imageCount: N
```

### The Solution

```php
// ✅ CORRECT: Handle everything in controller for guaranteed order
public function store(ProductRequest $request)
{
    $data = $request->validationData();

    // Create the product
    $product = Product::create($data);

    // Handle relationships and dispatch copy job
    $this->handleProductRelationships($product, $data);
}

/**
 * Handle product relationships and dispatch copy job
 */
private function handleProductRelationships(Product $product, array $data): void
{
    // Handle categories
    if (isset($data['categories'])) {
        $product->categories()->sync($data['categories']);
    }

    // Handle images
    if (isset($data['base_image'])) {
        $order = 0;
        $images = [$data['base_image'] => ['img_type' => 'base']];

        if (isset($data['additional_images'])) {
            foreach ($data['additional_images'] as $additional_image) {
                if ($additional_image != $data['base_image']) {
                    $images[$additional_image] = ['img_type' => 'additional', 'order' => ++$order];
                }
            }
        }

        $product->images()->sync($images);
    }

    // Dispatch copy job after all relationships are established
    CopyProductToResellers::dispatch($product);
}
```

### Why This Approach is Better

1. **Guaranteed Execution Order**: No dependency on event listener registration order
2. **Simpler Logic**: Everything happens in one place (controller)
3. **Easier to Debug**: Clear, linear flow of operations
4. **No Race Conditions**: Relationships are established before job dispatch
5. **Maintainable**: No complex event listener dependencies
6. **Reliable**: Works regardless of event system changes

## Critical: Avoid Eloquent Models in Reseller Database Operations

### The Problem

```php
// ❌ DANGEROUS: Using Eloquent models on reseller connections
Product::on('reseller')->create($productData);

// This triggers model events (saved, created, etc.)
// Which can cause infinite loops and data corruption
```

### The Solution

```php
// ✅ SAFE: Use DB facade for reseller database operations
DB::connection('reseller')
    ->table('products')
    ->insertGetId($productData);

// No model events triggered
// No infinite loops
// Preserves data integrity
```

### Why This Matters

1. **Model Events**: Eloquent models have `saved`, `created`, `updated` events that can trigger jobs
2. **Infinite Loops**: Copy jobs can trigger more copy jobs, causing infinite recursion
3. **Data Corruption**: `source_id` can be overwritten with wrong values
4. **Performance**: Unnecessary job dispatches and database operations

### Best Practices

```php
// ✅ For reading from reseller database
$product = DB::connection('reseller')
    ->table('products')
    ->where('source_id', $originalId)
    ->first();

// ✅ For writing to reseller database
$newId = DB::connection('reseller')
    ->table('products')
    ->insertGetId($data);

// ✅ For updating reseller database
DB::connection('reseller')
    ->table('products')
    ->where('id', $id)
    ->update($data);

// ❌ Avoid Eloquent models on reseller connections
// Product::on('reseller')->create($data);
// Product::on('reseller')->find($id);
// Product::on('reseller')->where(...)->get();
```

### File System Access

```php
// ✅ Good: Disable local infile
\PDO::MYSQL_ATTR_LOCAL_INFILE => false

// ❌ Bad: Allow local infile (security risk)
\PDO::MYSQL_ATTR_LOCAL_INFILE => true
```

### Error Information

```php
// ✅ Good: Exceptions for proper error handling
\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION

// ❌ Bad: Silent failures
\PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT
```

## Testing

### Unit Testing

```php
// Mock reseller connection
DB::shouldReceive('connection')
    ->with('reseller')
    ->andReturn($mockConnection);
```

### Integration Testing

```php
// Test with actual reseller database
$reseller = User::factory()->create(['db_name' => 'test_reseller_db']);
// ... test operations ...
```

## Conclusion

These improvements ensure:

1. **Clear separation** between database operations
2. **Proper error handling** and recovery
3. **Resource cleanup** to prevent memory leaks
4. **Better logging** for monitoring and debugging
5. **Consistent patterns** across all jobs
6. **Improved maintainability** and readability
7. **Centralized configuration** for easy maintenance
8. **Production-ready PDO settings** for security and performance

The jobs are now more robust, easier to debug, and follow consistent patterns for database connection management. All PDO settings are centralized in the User model, eliminating redundancy and ensuring consistency across all jobs.
