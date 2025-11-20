<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём категории
        $clothing = Category::create([
            'name' => 'Одежда',
            'slug' => 'clothing',
            'description' => 'Модная одежда для всех'
        ]);

        $shirts = Category::create([
            'name' => 'Футболки',
            'slug' => 't-shirts',
            'parent_id' => $clothing->id,
            'description' => 'Стильные футболки'
        ]);

        $pants = Category::create([
            'name' => 'Штаны',
            'slug' => 'pants',
            'parent_id' => $clothing->id,
            'description' => 'Удобные штаны и джинсы'
        ]);

        // Создаём товары с slug
        $productsData = [
            [
                'name' => 'Футболка классическая черная',
                'slug' => 'futbolka-klassicheskaya-chernaya',
                'description' => 'Комфортная хлопковая футболка черного цвета',
                'price' => 1299,
                'sku' => 'TSHIRT-BLACK-001',
                'stock' => 50,
                'category_id' => $shirts->id,
                'is_active' => true
            ],
            [
                'name' => 'Футболка белая с принтом',
                'slug' => 'futbolka-belaya-s-printom',
                'description' => 'Стильная белая футболка с уникальным принтом',
                'price' => 1499,
                'sku' => 'TSHIRT-WHITE-002',
                'stock' => 30,
                'category_id' => $shirts->id,
                'is_active' => true
            ],
            [
                'name' => 'Джинсы синие классические',
                'slug' => 'dzhinsy-sinie-klassicheskie',
                'description' => 'Качественные джинсы синего цвета',
                'price' => 3499,
                'sku' => 'JEANS-BLUE-001',
                'stock' => 25,
                'category_id' => $pants->id,
                'is_active' => true
            ],
            [
                'name' => 'Штаны спортивные серые',
                'slug' => 'shtany-sportivnye-serye',
                'description' => 'Удобные спортивные штаны для активного отдыха',
                'price' => 2299,
                'sku' => 'PANTS-GRAY-001',
                'stock' => 40,
                'category_id' => $pants->id,
                'is_active' => true
            ],
            [
                'name' => 'Футболка красная базовая',
                'slug' => 'futbolka-krasnaya-bazovaya',
                'description' => 'Базовая футболка красного цвета из хлопка',
                'price' => 1199,
                'sku' => 'TSHIRT-RED-003',
                'stock' => 35,
                'category_id' => $shirts->id,
                'is_active' => true
            ],
            [
                'name' => 'Джинсы черные скинни',
                'slug' => 'dzhinsy-chernye-skinny',
                'description' => 'Стильные черные джинсы скинни',
                'price' => 3799,
                'sku' => 'JEANS-BLACK-002',
                'stock' => 20,
                'category_id' => $pants->id,
                'is_active' => true
            ],
            [
                'name' => 'Футболка зеленая с лого',
                'slug' => 'futbolka-zelenaya-s-logo',
                'description' => 'Зеленая футболка с фирменным логотипом',
                'price' => 1599,
                'sku' => 'TSHIRT-GREEN-004',
                'stock' => 15,
                'category_id' => $shirts->id,
                'is_active' => true
            ],
            [
                'name' => 'Штаны джинсовые прямые',
                'slug' => 'shtany-dzhinsovye-pryamye',
                'description' => 'Прямые джинсовые штаны классического кроя',
                'price' => 2999,
                'sku' => 'PANTS-JEANS-003',
                'stock' => 28,
                'category_id' => $pants->id,
                'is_active' => true
            ]
        ];

        foreach ($productsData as $productData) {
            Product::create($productData);
        }

        // Создаём атрибуты
        $colorAttribute = Attribute::create([
            'name' => 'Цвет',
            'slug' => 'color',
            'type' => 'select',
            'is_filterable' => true
        ]);

        $sizeAttribute = Attribute::create([
            'name' => 'Размер',
            'slug' => 'size', 
            'type' => 'select',
            'is_filterable' => true
        ]);

        $materialAttribute = Attribute::create([
            'name' => 'Материал',
            'slug' => 'material',
            'type' => 'select',
            'is_filterable' => true
        ]);

        // Создаём значения атрибутов
        $colors = [
            ['value' => 'Чёрный', 'slug' => 'black'],
            ['value' => 'Белый', 'slug' => 'white'],
            ['value' => 'Синий', 'slug' => 'blue'],
            ['value' => 'Серый', 'slug' => 'gray'],
            ['value' => 'Красный', 'slug' => 'red'],
            ['value' => 'Зеленый', 'slug' => 'green']
        ];

        $sizes = [
            ['value' => 'S', 'slug' => 's'],
            ['value' => 'M', 'slug' => 'm'], 
            ['value' => 'L', 'slug' => 'l'],
            ['value' => 'XL', 'slug' => 'xl'],
            ['value' => 'XXL', 'slug' => 'xxl']
        ];

        $materials = [
            ['value' => 'Хлопок', 'slug' => 'cotton'],
            ['value' => 'Деним', 'slug' => 'denim'],
            ['value' => 'Полиэстер', 'slug' => 'polyester'],
            ['value' => 'Лён', 'slug' => 'linen']
        ];

        foreach ($colors as $color) {
            AttributeValue::create(array_merge($color, ['attribute_id' => $colorAttribute->id]));
        }

        foreach ($sizes as $size) {
            AttributeValue::create(array_merge($size, ['attribute_id' => $sizeAttribute->id]));
        }

        foreach ($materials as $material) {
            AttributeValue::create(array_merge($material, ['attribute_id' => $materialAttribute->id]));
        }

        // Привязываем атрибуты к товарам
        $products = Product::all();
        $colorValues = AttributeValue::where('attribute_id', $colorAttribute->id)->get();
        $sizeValues = AttributeValue::where('attribute_id', $sizeAttribute->id)->get();
        $materialValues = AttributeValue::where('attribute_id', $materialAttribute->id)->get();

        // Распределяем атрибуты по товарам
        $productAttributes = [
            // Футболка черная
            ['color' => 'black', 'size' => 'l', 'material' => 'cotton'],
            // Футболка белая
            ['color' => 'white', 'size' => 'm', 'material' => 'cotton'],
            // Джинсы синие
            ['color' => 'blue', 'size' => 'xl', 'material' => 'denim'],
            // Штаны серые
            ['color' => 'gray', 'size' => 'l', 'material' => 'polyester'],
            // Футболка красная
            ['color' => 'red', 'size' => 's', 'material' => 'cotton'],
            // Джинсы черные
            ['color' => 'black', 'size' => 'm', 'material' => 'denim'],
            // Футболка зеленая
            ['color' => 'green', 'size' => 'xl', 'material' => 'linen'],
            // Штаны джинсовые
            ['color' => 'blue', 'size' => 'l', 'material' => 'denim']
        ];

        foreach ($products as $index => $product) {
            if (isset($productAttributes[$index])) {
                $attrs = $productAttributes[$index];
                
                // Находим ID значений атрибутов по slug
                $colorValue = $colorValues->where('slug', $attrs['color'])->first();
                $sizeValue = $sizeValues->where('slug', $attrs['size'])->first();
                $materialValue = $materialValues->where('slug', $attrs['material'])->first();
                
                // Привязываем атрибуты к товару
                if ($colorValue) {
                    $product->attributeValues()->attach($colorValue->id);
                }
                if ($sizeValue) {
                    $product->attributeValues()->attach($sizeValue->id);
                }
                if ($materialValue) {
                    $product->attributeValues()->attach($materialValue->id);
                }
            }
        }

        $this->command->info('Каталог успешно создан!');
        $this->command->info('Категории: ' . Category::count());
        $this->command->info('Товары: ' . Product::count());
        $this->command->info('Атрибуты: ' . Attribute::count());
        $this->command->info('Значения атрибутов: ' . AttributeValue::count());
    }
}
