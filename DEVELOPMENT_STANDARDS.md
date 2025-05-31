# Laravel 11 + Vue.js + Inertia.js Development Standards

## Table of Contents
1. [Controller Standards](#controller-standards)
2. [Vue Component Standards](#vue-component-standards)
3. [Request Validation Standards](#request-validation-standards)
4. [Service Layer Standards](#service-layer-standards)
5. [Quick Templates](#quick-templates)
6. [Naming Conventions](#naming-conventions)

## Controller Standards

### Standard Resource Controller Structure

```php
<?php

namespace App\Http\Controllers;

use App\Models\{ModelName};
use App\Services\{ModelName}Service;
use App\Http\Requests\Store{ModelName}Request;
use App\Http\Requests\Update{ModelName}Request;
use Illuminate\Http\Request;
use Inertia\Inertia;

class {ModelName}Controller extends Controller
{
    protected ${modelName}Service;

    public function __construct({ModelName}Service ${modelName}Service)
    {
        $this->{modelName}Service = ${modelName}Service;
        
        // Permission middleware
        $this->middleware('permission:read {model_name_plural}', ['only' => ['index', 'show']]);
        $this->middleware('permission:create {model_name_plural}', ['only' => ['create', 'store']]);
        $this->middleware('permission:update {model_name_plural}', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete {model_name_plural}', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'per_page']);
        ${modelName_plural} = $this->{modelName}Service->getPaginated($filters);

        return Inertia::render('{ModelName}/Index', [
            '{modelName_plural}' => ${modelName_plural},
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return Inertia::render('{ModelName}/Create');
    }

    public function store(Store{ModelName}Request $request)
    {
        try {
            ${modelName} = $this->{modelName}Service->create($request->validated());
            
            return redirect()
                ->route('{modelName_plural}.index')
                ->with('success', __('messages.data_created_successfully'));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', __('messages.error_occurred'));
        }
    }

    public function show({ModelName} ${modelName})
    {
        return Inertia::render('{ModelName}/Show', [
            '{modelName}' => $this->{modelName}Service->getForShow(${modelName}),
        ]);
    }

    public function edit({ModelName} ${modelName})
    {
        return Inertia::render('{ModelName}/Edit', [
            '{modelName}' => $this->{modelName}Service->getForEdit(${modelName}),
        ]);
    }

    public function update(Update{ModelName}Request $request, {ModelName} ${modelName})
    {
        try {
            $this->{modelName}Service->update(${modelName}, $request->validated());
            
            return redirect()
                ->route('{modelName_plural}.index')
                ->with('success', __('messages.data_updated_successfully'));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', __('messages.error_occurred'));
        }
    }

    public function destroy({ModelName} ${modelName})
    {
        try {
            $this->{modelName}Service->delete(${modelName});
            
            return redirect()
                ->route('{modelName_plural}.index')
                ->with('success', __('messages.data_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('messages.error_occurred'));
        }
    }

    public function activate({ModelName} ${modelName})
    {
        try {
            $this->{modelName}Service->toggleStatus(${modelName});
            
            return back()->with('success', __('messages.status_updated_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('messages.error_occurred'));
        }
    }
}
```

### Controller Rules:
1. **Always use Service Layer** - Controllers should be thin and delegate business logic to services
2. **Permission Middleware** - Always implement permission-based access control
3. **Exception Handling** - Wrap operations in try-catch blocks
4. **Consistent Responses** - Use standardized success/error messages
5. **Request Validation** - Use Form Request classes for validation
6. **Inertia Responses** - Always return Inertia responses for web routes

## Vue Component Standards

### Standard Index Page Structure

```vue
<template>
    <AuthenticatedLayout>
        <!-- Breadcrumb -->
        <div class="pagetitle row">
            <BreadcrumbComponent 
                :pageTitle="$t('{model_name_plural}')" 
                createRoute="{modelName_plural}.create" 
                createPermission="create {model_name_plural}"
                :homeLabel="$t('home')" 
                :createButtonLabel="$t('create')" 
            />
        </div>

        <section class="section dashboard">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div class="col-md-12 px-2">
                            <FilterComponent 
                                :filter-fields="filterFields" 
                                :initial-filters="filterForm"
                                @update:filters="handleFilterUpdate" 
                            />
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <DataTable 
                            :headers="headers" 
                            :data="{modelName_plural}.data" 
                            :pagination-links="{modelName_plural}.links"
                            :noDataMessage="$t('no_data_found')" 
                            @update:page="handlePageChange"
                        >
                            <!-- Custom column slots -->
                            <template #is_active="{ data }">
                                <ActivateToggle 
                                    :id="data.id"
                                    :is-active="data.is_active == 1" 
                                    :activate-url="`/{modelName_plural}/${{data.id}}/activate`"
                                    @update:is-active="(newStatus) => updateStatus(data.id, newStatus)" 
                                />
                            </template>

                            <template #edit="{ data }">
                                <EditButton 
                                    @click="router.get(route('{modelName_plural}.edit', { {modelName}: data.id }))" 
                                />
                            </template>

                            <template #delete="{ data }">
                                <DeleteAction 
                                    :id="data.id" 
                                    :delete-url="route('{modelName_plural}.destroy', { {modelName}: data.id })" 
                                />
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import { reactive, computed } from "vue";
import { useI18n } from "vue-i18n";
import FilterComponent from "@/Components/FilterComponent.vue";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";
import DataTable from "@/Components/DataTable.vue";
import ActivateToggle from "@/Components/ActivateToggle.vue";
import EditButton from "@/Components/EditButton.vue";
import DeleteAction from "@/Components/DeleteAction.vue";

const { t } = useI18n();

// Props
const props = defineProps({
    {modelName_plural}: Object,
    filters: Object,
});

// Filter configuration
const filterFields = [
    {
        name: 'search',
        type: 'text',
        placeholder: t('search'),
        value: props.filters?.search || ''
    },
    {
        name: 'is_active',
        type: 'select',
        placeholder: t('status'),
        options: [
            { value: '', label: t('all') },
            { value: '1', label: t('active') },
            { value: '0', label: t('inactive') }
        ],
        value: props.filters?.is_active || ''
    }
];

const filterForm = reactive({
    search: props.filters?.search || '',
    is_active: props.filters?.is_active || '',
});

// Table headers
const headers = [
    { key: 'id', label: '#', sortable: true },
    { key: 'name', label: t('name'), sortable: true },
    { key: 'created_at', label: t('created_at'), sortable: true },
    { key: 'is_active', label: t('status'), slot: true },
    { key: 'edit', label: t('edit'), slot: true, permission: 'update {model_name_plural}' },
    { key: 'delete', label: t('delete'), slot: true, permission: 'delete {model_name_plural}' }
];

// Methods
const handleFilterUpdate = (filters) => {
    Object.assign(filterForm, filters);
    router.get(route('{modelName_plural}.index'), filterForm, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handlePageChange = (url) => {
    router.get(url, filterForm, {
        preserveState: true,
        preserveScroll: true,
    });
};

const updateStatus = (id, newStatus) => {
    const item = props.{modelName_plural}.data.find(item => item.id === id);
    if (item) {
        item.is_active = newStatus ? 1 : 0;
    }
};
</script>
```

### Standard Create/Edit Form Structure

```vue
<template>
    <AuthenticatedLayout>
        <!-- Breadcrumb -->
        <div class="pagetitle row">
            <BreadcrumbComponent 
                :pageTitle="isEdit ? $t('edit_{modelName}') : $t('create_{modelName}')" 
                :homeLabel="$t('home')" 
                :breadcrumbs="breadcrumbs"
            />
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        {{ isEdit ? $t('edit_{modelName}') : $t('create_{modelName}') }}
                    </h5>
                </div>
                
                <div class="card-body">
                    <form @submit.prevent="submit" class="row g-3">
                        <!-- Form fields -->
                        <div class="col-md-6">
                            <label class="form-label">{{ $t('name') }} *</label>
                            <input 
                                v-model="form.name"
                                type="text" 
                                class="form-control"
                                :class="{ 'is-invalid': errors.name }"
                                required
                            />
                            <div v-if="errors.name" class="invalid-feedback">
                                {{ errors.name }}
                            </div>
                        </div>

                        <!-- Multi-language fields if needed -->
                        <div v-if="hasTranslations" class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6>{{ $t('translations') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div v-for="locale in locales" :key="locale" class="mb-3">
                                        <label class="form-label">
                                            {{ $t('name') }} ({{ locale.toUpperCase() }}) *
                                        </label>
                                        <input 
                                            v-model="form.translations[locale].name"
                                            type="text" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors[`translations.${locale}.name`] }"
                                            required
                                        />
                                        <div v-if="errors[`translations.${locale}.name`]" class="invalid-feedback">
                                            {{ errors[`translations.${locale}.name`] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status field -->
                        <div class="col-md-6">
                            <label class="form-label">{{ $t('status') }}</label>
                            <select v-model="form.is_active" class="form-select">
                                <option value="1">{{ $t('active') }}</option>
                                <option value="0">{{ $t('inactive') }}</option>
                            </select>
                        </div>

                        <!-- Submit buttons -->
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary"
                                    :disabled="processing"
                                >
                                    <span v-if="processing" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ isEdit ? $t('update') : $t('create') }}
                                </button>
                                
                                <Link 
                                    :href="route('{modelName_plural}.index')" 
                                    class="btn btn-secondary"
                                >
                                    {{ $t('cancel') }}
                                </Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

// Props
const props = defineProps({
    {modelName}: {
        type: Object,
        default: null
    }
});

// Computed
const isEdit = computed(() => !!props.{modelName});
const hasTranslations = true; // Set based on your model
const locales = ['en', 'ar']; // Your supported locales

const breadcrumbs = [
    { label: t('{model_name_plural}'), route: '{modelName_plural}.index' },
    { label: isEdit.value ? t('edit') : t('create'), route: null }
];

// Form setup
const form = useForm({
    name: props.{modelName}?.name || '',
    is_active: props.{modelName}?.is_active ?? 1,
    translations: hasTranslations ? 
        locales.reduce((acc, locale) => {
            acc[locale] = {
                name: props.{modelName}?.translations?.find(t => t.locale === locale)?.name || ''
            };
            return acc;
        }, {}) : {}
});

// Methods
const submit = () => {
    if (isEdit.value) {
        form.put(route('{modelName_plural}.update', props.{modelName}.id));
    } else {
        form.post(route('{modelName_plural}.store'));
    }
};
</script>
```

## Request Validation Standards

### Store Request Template

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store{ModelName}Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('create {model_name_plural}');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            
            // Multi-language support
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('name')]),
            'translations.*.name.required' => __('validation.required', ['attribute' => __('name')]),
        ];
    }
}
```

### Update Request Template

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update{ModelName}Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('update {model_name_plural}');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            
            // Multi-language support
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('name')]),
            'translations.*.name.required' => __('validation.required', ['attribute' => __('name')]),
        ];
    }
}
```

## Service Layer Standards

### Standard Service Template

```php
<?php

namespace App\Services;

use App\Models\{ModelName};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class {ModelName}Service
{
    public function getPaginated(array $filters = [])
    {
        $query = {ModelName}::query();

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Apply vendor scope if needed
        if (auth()->user()->hasRole('vendor')) {
            $query->where('vendor_id', auth()->id());
        }

        return $query->latest()
            ->with(['translations']) // Load relationships
            ->paginate($filters['per_page'] ?? 10);
    }

    public function create(array $data): {ModelName}
    {
        DB::beginTransaction();
        
        try {
            ${modelName} = {ModelName}::create([
                'name' => $data['name'],
                'is_active' => $data['is_active'] ?? 1,
                'vendor_id' => auth()->id(), // If applicable
            ]);

            // Handle translations if applicable
            if (isset($data['translations'])) {
                $this->saveTranslations(${modelName}, $data['translations']);
            }

            DB::commit();
            
            return ${modelName};
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating {modelName}', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function update({ModelName} ${modelName}, array $data): {ModelName}
    {
        DB::beginTransaction();
        
        try {
            ${modelName}->update([
                'name' => $data['name'],
                'is_active' => $data['is_active'] ?? ${modelName}->is_active,
            ]);

            // Handle translations if applicable
            if (isset($data['translations'])) {
                $this->saveTranslations(${modelName}, $data['translations']);
            }

            DB::commit();
            
            return ${modelName};
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating {modelName}', [
                'error' => $e->getMessage(),
                'id' => ${modelName}->id,
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function delete({ModelName} ${modelName}): bool
    {
        try {
            return ${modelName}->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting {modelName}', [
                'error' => $e->getMessage(),
                'id' => ${modelName}->id
            ]);
            throw $e;
        }
    }

    public function toggleStatus({ModelName} ${modelName}): {ModelName}
    {
        ${modelName}->update(['is_active' => !${modelName}->is_active]);
        return ${modelName};
    }

    public function getForEdit({ModelName} ${modelName}): array
    {
        ${modelName}->load('translations');
        
        return [
            'id' => ${modelName}->id,
            'name' => ${modelName}->name,
            'is_active' => ${modelName}->is_active,
            'translations' => ${modelName}->translations,
        ];
    }

    public function getForShow({ModelName} ${modelName}): array
    {
        ${modelName}->load('translations');
        
        return [
            'id' => ${modelName}->id,
            'name' => ${modelName}->name,
            'is_active' => ${modelName}->is_active,
            'created_at' => ${modelName}->created_at,
            'updated_at' => ${modelName}->updated_at,
            'translations' => ${modelName}->translations,
        ];
    }

    private function saveTranslations({ModelName} ${modelName}, array $translations): void
    {
        foreach ($translations as $locale => $translation) {
            DB::table('{model_name}_translations')->updateOrInsert(
                [
                    '{model_name}_id' => ${modelName}->id,
                    'locale' => $locale
                ],
                [
                    'name' => $translation['name']
                ]
            );
        }
    }
}
```

## Naming Conventions

### Files and Classes
- **Controllers**: `{ModelName}Controller.php` (e.g., `UserController.php`)
- **Models**: `{ModelName}.php` (e.g., `User.php`)
- **Services**: `{ModelName}Service.php` (e.g., `UserService.php`)
- **Requests**: `Store{ModelName}Request.php`, `Update{ModelName}Request.php`
- **Vue Pages**: `{ModelName}/Index.vue`, `{ModelName}/Create.vue`, `{ModelName}/Edit.vue`

### Routes
- **Resource routes**: `{model_name_plural}` (e.g., `users`, `products`)
- **Custom routes**: `{model_name_plural}.{action}` (e.g., `users.activate`)

### Database
- **Tables**: `{model_name_plural}` (e.g., `users`, `products`)
- **Translation tables**: `{model_name}_translations` (e.g., `user_translations`)

### Variables
- **Single instance**: `${modelName}` (e.g., `$user`)
- **Collections**: `${modelName_plural}` (e.g., `$users`)
- **Service instances**: `${modelName}Service` (e.g., `$userService`)

## Quick Reference Commands

### Generate Resource Files
```bash
# Generate all resource files at once
php artisan make:controller {ModelName}Controller --resource
php artisan make:model {ModelName} -m
php artisan make:request Store{ModelName}Request
php artisan make:request Update{ModelName}Request
php artisan make:service {ModelName}Service
```

### Vue Component Generation
Create the following directory structure:
```
resources/js/Pages/{ModelName}/
├── Index.vue
├── Create.vue
├── Edit.vue
└── Show.vue (optional)
```

This standardized approach ensures:
- ✅ Consistent code structure
- ✅ Proper error handling
- ✅ Permission-based access control
- ✅ Multi-language support
- ✅ Reusable components
- ✅ Maintainable codebase
- ✅ Fast development workflow 