# Quick Reference Guide - Laravel 11 + Vue.js + Inertia.js

## 🚀 Generate Complete Resource

### Single Command Generation
```bash
# Generate everything (Controller, Service, Requests, Vue Components)
php artisan make:resource Product

# Skip Vue components
php artisan make:resource Product --no-vue

# Skip service layer
php artisan make:resource Product --no-service

# Generate model and migration separately
php artisan make:model Product -m
```

## 📁 Generated File Structure

When you run `php artisan make:resource Product`, it creates:

```
app/
├── Http/
│   ├── Controllers/
│   │   └── ProductController.php
│   └── Requests/
│       ├── StoreProductRequest.php
│       └── UpdateProductRequest.php
├── Services/
│   └── ProductService.php
└── Models/
    └── Product.php (create separately)

resources/
└── js/
    └── Pages/
        └── Product/
            ├── Index.vue
            ├── Create.vue
            └── Edit.vue
```

## 🛠️ Standard Features Included

### Controller Features
- ✅ Service layer integration
- ✅ Permission-based middleware
- ✅ Exception handling
- ✅ Standardized responses
- ✅ Inertia.js integration
- ✅ Status toggle functionality

### Service Features
- ✅ Database transactions
- ✅ Error logging
- ✅ Multi-language support
- ✅ Filtering and pagination
- ✅ Vendor scoping (if applicable)

### Vue Components Features
- ✅ Responsive design with Bootstrap
- ✅ Multi-language support (i18n)
- ✅ Data tables with sorting/filtering
- ✅ Form validation
- ✅ Loading states
- ✅ Permission-based UI elements

## 📝 After Generation Checklist

### 1. Add Routes
```php
// routes/web.php
Route::resource('products', ProductController::class);
Route::post('products/{product}/activate', [ProductController::class, 'activate'])->name('products.activate');
```

### 2. Update Model
```php
// app/Models/Product.php
class Product extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'vendor_id', // if applicable
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Add relationships
    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }
}
```

### 3. Create Migration
```php
// database/migrations/xxxx_create_products_table.php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->boolean('is_active')->default(true);
    $table->foreignId('vendor_id')->nullable()->constrained('users');
    $table->timestamps();
});

// For translations (if needed)
Schema::create('product_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->string('locale', 2);
    $table->string('name');
    $table->timestamps();
    
    $table->unique(['product_id', 'locale']);
});
```

### 4. Register Service (Optional)
```php
// app/Providers/AppServiceProvider.php
public function register()
{
    $this->app->bind(ProductService::class, function ($app) {
        return new ProductService();
    });
}
```

### 5. Add Translations
```json
// lang/en.json
{
    "products": "Products",
    "product": "Product",
    "create_product": "Create Product",
    "edit_product": "Edit Product"
}

// lang/ar.json
{
    "products": "المنتجات",
    "product": "منتج",
    "create_product": "إنشاء منتج",
    "edit_product": "تعديل منتج"
}
```

## 🎨 Customization Examples

### Adding Custom Fields

#### 1. Update Request Validation
```php
// app/Http/Requests/StoreProductRequest.php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
        'is_active' => 'boolean',
        'translations' => 'required|array',
        'translations.*.name' => 'required|string|max:255',
        'translations.*.description' => 'nullable|string',
    ];
}
```

#### 2. Update Service
```php
// app/Services/ProductService.php
public function create(array $data): Product
{
    DB::beginTransaction();
    
    try {
        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'is_active' => $data['is_active'] ?? 1,
            'vendor_id' => auth()->id(),
        ]);

        if (isset($data['translations'])) {
            $this->saveTranslations($product, $data['translations']);
        }

        DB::commit();
        return $product;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

#### 3. Update Vue Form
```vue
<!-- resources/js/Pages/Product/Create.vue -->
<template>
    <!-- ... existing form fields ... -->
    
    <!-- Description field -->
    <div class="col-12">
        <label class="form-label">{{ $t('description') }}</label>
        <textarea 
            v-model="form.description"
            class="form-control"
            :class="{ 'is-invalid': form.errors.description }"
            rows="3"
        ></textarea>
        <div v-if="form.errors.description" class="invalid-feedback">
            {{ form.errors.description }}
        </div>
    </div>

    <!-- Price field -->
    <div class="col-md-6">
        <label class="form-label">{{ $t('price') }} *</label>
        <input 
            v-model="form.price"
            type="number"
            step="0.01"
            min="0"
            class="form-control"
            :class="{ 'is-invalid': form.errors.price }"
            required
        />
        <div v-if="form.errors.price" class="invalid-feedback">
            {{ form.errors.price }}
        </div>
    </div>
</template>

<script setup>
// Update form data
const form = useForm({
    name: props.product?.name || '',
    description: props.product?.description || '',
    price: props.product?.price || '',
    category_id: props.product?.category_id || '',
    is_active: props.product?.is_active ?? 1,
    translations: hasTranslations ? 
        locales.reduce((acc, locale) => {
            acc[locale] = {
                name: props.product?.translations?.find(t => t.locale === locale)?.name || '',
                description: props.product?.translations?.find(t => t.locale === locale)?.description || ''
            };
            return acc;
        }, {}) : {}
});
</script>
```

## 🔧 Common Patterns

### Adding Relationships
```php
// In Service
public function getPaginated(array $filters = [])
{
    return $query->latest()
        ->with(['translations', 'category', 'vendor']) // Add relationships
        ->paginate($filters['per_page'] ?? 10);
}
```

### Custom Filters
```php
// In Service
if (!empty($filters['category_id'])) {
    $query->where('category_id', $filters['category_id']);
}

if (!empty($filters['price_min'])) {
    $query->where('price', '>=', $filters['price_min']);
}
```

### File Uploads
```php
// In Service
if (isset($data['image'])) {
    $imagePath = $data['image']->store('products', 'public');
    $productData['image'] = $imagePath;
}
```

## 🚨 Important Notes

1. **Always use transactions** for operations that modify multiple tables
2. **Log errors** for debugging and monitoring
3. **Validate permissions** in both backend and frontend
4. **Use translations** for all user-facing text
5. **Follow naming conventions** consistently
6. **Test your generated code** before deploying

## 📚 Additional Resources

- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Vue.js 3 Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)

---

**Happy Coding! 🎉** 