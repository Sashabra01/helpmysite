<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'Американская корпорация, производитель персональных и планшетных компьютеров, аудиоплееров, телефонов, программного обеспечения.',
                'website' => 'https://www.apple.com',
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Apple - официальный магазин',
                'meta_description' => 'Купить продукцию Apple в официальном магазине',
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'Южнокорейская группа компаний, один из крупнейших чеболей, основанный в 1938 году.',
                'website' => 'https://www.samsung.com',
                'is_active' => true,
                'sort_order' => 2,
                'meta_title' => 'Samsung - официальный магазин',
                'meta_description' => 'Купить продукцию Samsung в официальном магазине',
            ],
            [
                'name' => 'Xiaomi',
                'slug' => 'xiaomi',
                'description' => 'Китайская компания, выпускающая смартфоны, планшеты и другую электронику.',
                'website' => 'https://www.mi.com',
                'is_active' => true,
                'sort_order' => 3,
                'meta_title' => 'Xiaomi - официальный магазин',
                'meta_description' => 'Купить продукцию Xiaomi в официальном магазине',
            ],
            [
                'name' => 'Sony',
                'slug' => 'sony',
                'description' => 'Японский производитель электроники, игровых консолей и другой техники.',
                'website' => 'https://www.sony.com',
                'is_active' => true,
                'sort_order' => 4,
                'meta_title' => 'Sony - официальный магазин',
                'meta_description' => 'Купить продукцию Sony в официальном магазине',
            ],
            [
                'name' => 'Huawei',
                'slug' => 'huawei',
                'description' => 'Китайская компания, производитель телекоммуникационного оборудования и электроники.',
                'website' => 'https://www.huawei.com',
                'is_active' => true,
                'sort_order' => 5,
                'meta_title' => 'Huawei - официальный магазин',
                'meta_description' => 'Купить продукцию Huawei в официальном магазине',
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Американская компания, производитель спортивной одежды и обуви.',
                'website' => 'https://www.nike.com',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Немецкий производитель спортивной одежды и обуви.',
                'website' => 'https://www.adidas.com',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'IKEA',
                'slug' => 'ikea',
                'description' => 'Шведская компания, производитель мебели и товаров для дома.',
                'website' => 'https://www.ikea.com',
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
