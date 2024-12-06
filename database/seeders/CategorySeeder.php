<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Materiais Básicos',
                'description' => 'Cimento, areia, brita e outros materiais básicos para construção',
                'slug' => Str::slug('Materiais Básicos'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Ferramentas Manuais',
                'description' => 'Martelos, chaves de fenda, alicates e outras ferramentas manuais',
                'slug' => Str::slug('Ferramentas Manuais'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Ferramentas Elétricas',
                'description' => 'Furadeiras, serras elétricas, lixadeiras e outras ferramentas elétricas',
                'slug' => Str::slug('Ferramentas Elétricas'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Hidráulica',
                'description' => 'Tubos, conexões, registros e materiais para instalações hidráulicas',
                'slug' => Str::slug('Hidráulica'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Elétrica',
                'description' => 'Fios, cabos, disjuntores e materiais para instalações elétricas',
                'slug' => Str::slug('Elétrica'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Pisos e Revestimentos',
                'description' => 'Cerâmicas, porcelanatos, pisos laminados e revestimentos',
                'slug' => Str::slug('Pisos e Revestimentos'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Tintas e Acessórios',
                'description' => 'Tintas, vernizes, pincéis e rolos',
                'slug' => Str::slug('Tintas e Acessórios'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Esquadrias',
                'description' => 'Portas, janelas, batentes e guarnições',
                'slug' => Str::slug('Esquadrias'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Iluminação',
                'description' => 'Lâmpadas, luminárias, spots e acessórios para iluminação',
                'slug' => Str::slug('Iluminação'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Metais Sanitários',
                'description' => 'Torneiras, chuveiros, registros e acabamentos',
                'slug' => Str::slug('Metais Sanitários'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Louças Sanitárias',
                'description' => 'Vasos sanitários, pias, tanques e cubas',
                'slug' => Str::slug('Louças Sanitárias'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Madeiras',
                'description' => 'Tábuas, compensados, MDF e outros produtos de madeira',
                'slug' => Str::slug('Madeiras'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Ferragens',
                'description' => 'Fechaduras, dobradiças, puxadores e acessórios',
                'slug' => Str::slug('Ferragens'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Impermeabilizantes',
                'description' => 'Mantas, resinas e produtos para impermeabilização',
                'slug' => Str::slug('Impermeabilizantes'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Telhas e Coberturas',
                'description' => 'Telhas cerâmicas, de concreto, metálicas e acessórios',
                'slug' => Str::slug('Telhas e Coberturas'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Equipamentos de Proteção',
                'description' => 'EPIs, capacetes, luvas e equipamentos de segurança',
                'slug' => Str::slug('Equipamentos de Proteção'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Acessórios para Banheiro',
                'description' => 'Papeleiras, saboneteiras, porta-toalhas e acessórios',
                'slug' => Str::slug('Acessórios para Banheiro'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Jardinagem',
                'description' => 'Ferramentas e produtos para jardim',
                'slug' => Str::slug('Jardinagem'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Escadas e Andaimes',
                'description' => 'Escadas, andaimes e equipamentos para altura',
                'slug' => Str::slug('Escadas e Andaimes'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Adesivos e Colas',
                'description' => 'Colas, silicones, fitas adesivas e produtos para fixação',
                'slug' => Str::slug('Adesivos e Colas'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table('categories')->insert($categories);
    }
}
