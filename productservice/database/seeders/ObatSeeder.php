<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Obat;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obat = [
            // --- Kategori: Analgesik (Perda Nyeri & Demam) ---
            [
                'name'        => 'Paracetamol 500mg',
                'category'    => 'Analgesik',
                'price'       => 15000,
                'stock'       => 150,
                'description' => 'Obat pereda nyeri ringan hingga sedang dan penurun demam.'
            ],
            [
                'name'        => 'Ibuprofen 400mg',
                'category'    => 'Analgesik',
                'price'       => 20000,
                'stock'       => 120,
                'description' => 'Obat antiinflamasi nonsteroid (OAINS) untuk meredakan nyeri dan peradangan.'
            ],
            [
                'name'        => 'Asam Mefenamat 500mg',
                'category'    => 'Analgesik',
                'price'       => 25000,
                'stock'       => 100,
                'description' => 'Sering digunakan untuk meredakan nyeri haid, sakit gigi, dan nyeri otot.'
            ],
            [
                'name'        => 'Ketorolac 10mg',
                'category'    => 'Analgesik',
                'price'       => 35000,
                'stock'       => 50,
                'description' => 'Obat pereda nyeri sedang hingga berat, biasanya untuk nyeri pasca operasi.'
            ],
            [
                'name'        => 'Meloxicam 15mg',
                'category'    => 'Analgesik',
                'price'       => 30000,
                'stock'       => 75,
                'description' => 'Digunakan untuk mengobati gejala osteoarthritis dan rheumatoid arthritis.'
            ],
            [
                'name'        => 'Diclofenac Sodium 50mg',
                'category'    => 'Analgesik',
                'price'       => 18000,
                'stock'       => 90,
                'description' => 'Meredakan nyeri sendi, peradangan, dan pembengkakan.'
            ],
            [
                'name'        => 'Tramadol HCl 50mg',
                'category'    => 'Analgesik',
                'price'       => 40000,
                'stock'       => 40,
                'description' => 'Pereda nyeri opioid untuk nyeri sedang hingga berat (Harus dengan resep).'
            ],
            [
                'name'        => 'Naproxen Sodium 550mg',
                'category'    => 'Analgesik',
                'price'       => 45000,
                'stock'       => 60,
                'description' => 'Efektif untuk meredakan kekakuan sendi dan nyeri otot.'
            ],
            [
                'name'        => 'Celecoxib 200mg',
                'category'    => 'Analgesik',
                'price'       => 75000,
                'stock'       => 45,
                'description' => 'Pereda nyeri spesifik untuk radang sendi dengan risiko efek lambung lebih rendah.'
            ],
            [
                'name'        => 'Aspirin 81mg',
                'category'    => 'Analgesik',
                'price'       => 12000,
                'stock'       => 200,
                'description' => 'Pereda nyeri ringan dan juga sering digunakan sebagai pengencer darah.'
            ],

            // --- Kategori: Antibiotik (Infeksi Bakteri) ---
            [
                'name'        => 'Amoxicillin Trihydrate 500mg',
                'category'    => 'Antibiotik',
                'price'       => 25000,
                'stock'       => 85,
                'description' => 'Antibiotik golongan penisilin spektrum luas untuk infeksi bakteri umum.'
            ],
            [
                'name'        => 'Cefadroxil 500mg',
                'category'    => 'Antibiotik',
                'price'       => 35000,
                'stock'       => 70,
                'description' => 'Antibiotik untuk infeksi saluran kemih, kulit, dan tenggorokan.'
            ],
            [
                'name'        => 'Azithromycin 500mg',
                'category'    => 'Antibiotik',
                'price'       => 65000,
                'stock'       => 50,
                'description' => 'Antibiotik makrolida yang sering digunakan untuk infeksi saluran pernapasan.'
            ],
            [
                'name'        => 'Ciprofloxacin 500mg',
                'category'    => 'Antibiotik',
                'price'       => 30000,
                'stock'       => 90,
                'description' => 'Antibiotik quinolone yang efektif untuk infeksi bakteri gram negatif.'
            ],
            [
                'name'        => 'Doxycycline 100mg',
                'category'    => 'Antibiotik',
                'price'       => 20000,
                'stock'       => 65,
                'description' => 'Digunakan untuk mengobati jerawat parah dan infeksi bakteri lainnya.'
            ],
            [
                'name'        => 'Clindamycin 300mg',
                'category'    => 'Antibiotik',
                'price'       => 45000,
                'stock'       => 55,
                'description' => 'Antibiotik untuk infeksi serius akibat bakteri anaerob.'
            ],
            [
                'name'        => 'Levofloxacin 500mg',
                'category'    => 'Antibiotik',
                'price'       => 55000,
                'stock'       => 40,
                'description' => 'Digunakan untuk infeksi paru-paru, sinus, kulit, dan saluran kemih.'
            ],
            [
                'name'        => 'Erythromycin 500mg',
                'category'    => 'Antibiotik',
                'price'       => 28000,
                'stock'       => 75,
                'description' => 'Alternatif untuk pasien yang alergi terhadap penisilin.'
            ],
            [
                'name'        => 'Metronidazole 500mg',
                'category'    => 'Antibiotik',
                'price'       => 18000,
                'stock'       => 110,
                'description' => 'Antibiotik dan antiprotozoa untuk infeksi saluran pencernaan.'
            ],
            [
                'name'        => 'Cotrimoxazole 480mg',
                'category'    => 'Antibiotik',
                'price'       => 15000,
                'stock'       => 100,
                'description' => 'Kombinasi antibiotik untuk mengatasi berbagai infeksi bakteri.'
            ],

            // --- Kategori: Vitamin ---
            [
                'name'        => 'Vitamin C Complex 1000mg',
                'category'    => 'Vitamin',
                'price'       => 45000,
                'stock'       => 200,
                'description' => 'Vitamin C dosis tinggi untuk menjaga dan meningkatkan daya tahan tubuh.'
            ],
            [
                'name'        => 'Vitamin D3 1000 IU',
                'category'    => 'Vitamin',
                'price'       => 35000,
                'stock'       => 150,
                'description' => 'Membantu penyerapan kalsium untuk kesehatan tulang dan imunitas.'
            ],
            [
                'name'        => 'B-Complex Forte',
                'category'    => 'Vitamin',
                'price'       => 25000,
                'stock'       => 180,
                'description' => 'Kombinasi vitamin B untuk menjaga sistem saraf dan metabolisme energi.'
            ],
            [
                'name'        => 'Vitamin E 400 IU',
                'category'    => 'Vitamin',
                'price'       => 60000,
                'stock'       => 100,
                'description' => 'Antioksidan kuat yang baik untuk kesehatan kulit dan mata.'
            ],
            [
                'name'        => 'Multivitamin & Mineral Dewasa',
                'category'    => 'Vitamin',
                'price'       => 85000,
                'stock'       => 90,
                'description' => 'Kombinasi vitamin dan mineral lengkap untuk kebutuhan harian.'
            ],
            [
                'name'        => 'Vitamin A 6000 IU',
                'category'    => 'Vitamin',
                'price'       => 15000,
                'stock'       => 130,
                'description' => 'Penting untuk menjaga fungsi penglihatan dan kesehatan jaringan.'
            ],
            [
                'name'        => 'Vitamin K2 100mcg',
                'category'    => 'Vitamin',
                'price'       => 95000,
                'stock'       => 45,
                'description' => 'Mendukung kesehatan tulang dengan mengarahkan kalsium ke tempat yang tepat.'
            ],
            [
                'name'        => 'Zinc + Vitamin C Tablet',
                'category'    => 'Vitamin',
                'price'       => 40000,
                'stock'       => 160,
                'description' => 'Kombinasi optimal untuk mempercepat penyembuhan dan imunitas.'
            ],
            [
                'name'        => 'Calcium + Vitamin D',
                'category'    => 'Vitamin',
                'price'       => 50000,
                'stock'       => 120,
                'description' => 'Membantu mencegah pengeroposan tulang (osteoporosis).'
            ],
            [
                'name'        => 'Folic Acid 400mcg',
                'category'    => 'Vitamin',
                'price'       => 12000,
                'stock'       => 250,
                'description' => 'Asam folat yang sangat penting untuk wanita hamil dan pembentukan sel darah.'
            ],

            // --- Kategori: Antiseptik ---
            [
                'name'        => 'Povidone Iodine 10% 60ml',
                'category'    => 'Antiseptik',
                'price'       => 15000,
                'stock'       => 60,
                'description' => 'Cairan antiseptik untuk membersihkan dan melindungi luka terbuka.'
            ],
            [
                'name'        => 'Alkohol 70% 100ml',
                'category'    => 'Antiseptik',
                'price'       => 10000,
                'stock'       => 300,
                'description' => 'Cairan disinfektan untuk sterilisasi alat dan permukaan kulit utuh.'
            ],
            [
                'name'        => 'Rivanol 100ml',
                'category'    => 'Antiseptik',
                'price'       => 8000,
                'stock'       => 150,
                'description' => 'Cairan kompres antiseptik untuk membersihkan luka bernanah atau bisul.'
            ],
            [
                'name'        => 'Chlorhexidine Gluconate 0.2%',
                'category'    => 'Antiseptik',
                'price'       => 25000,
                'stock'       => 80,
                'description' => 'Sering digunakan sebagai obat kumur antiseptik atau pembersih kulit sebelum operasi.'
            ],
            [
                'name'        => 'Hydrogen Peroxide 3% 100ml',
                'category'    => 'Antiseptik',
                'price'       => 12000,
                'stock'       => 100,
                'description' => 'Antiseptik ringan yang melepaskan oksigen untuk membersihkan luka kotor.'
            ],
            [
                'name'        => 'Hand Sanitizer Gel 50ml',
                'category'    => 'Antiseptik',
                'price'       => 15000,
                'stock'       => 250,
                'description' => 'Pembersih tangan berbasis alkohol praktis untuk membunuh kuman.'
            ],
            [
                'name'        => 'Sabun Cair Antiseptik 200ml',
                'category'    => 'Antiseptik',
                'price'       => 30000,
                'stock'       => 120,
                'description' => 'Sabun mandi dengan kandungan antibakteri untuk perlindungan ekstra.'
            ],
            [
                'name'        => 'Mouthwash Antiseptik 250ml',
                'category'    => 'Antiseptik',
                'price'       => 28000,
                'stock'       => 140,
                'description' => 'Membunuh bakteri penyebab plak, radang gusi, dan bau mulut.'
            ],
            [
                'name'        => 'Salep Kulit Antibakteri 10g',
                'category'    => 'Antiseptik',
                'price'       => 35000,
                'stock'       => 90,
                'description' => 'Salep topikal untuk mencegah infeksi pada luka gores atau bakar ringan.'
            ],
            [
                'name'        => 'Spray Antiseptik Luka 50ml',
                'category'    => 'Antiseptik',
                'price'       => 45000,
                'stock'       => 70,
                'description' => 'Semprotan antiseptik tanpa perih untuk anak-anak dan dewasa.'
            ],

            // --- Kategori: Suplemen ---
            [
                'name'        => 'Omega 3 Fish Oil Extract',
                'category'    => 'Suplemen',
                'price'       => 120000,
                'stock'       => 50,
                'description' => 'Kaya akan EPA dan DHA untuk mendukung kesehatan fungsi jantung dan otak.'
            ],
            [
                'name'        => 'Whey Protein Isolate Sachet',
                'category'    => 'Suplemen',
                'price'       => 35000,
                'stock'       => 200,
                'description' => 'Suplemen protein tinggi untuk mendukung pemulihan dan massa otot.'
            ],
            [
                'name'        => 'Creatine Monohydrate 300g',
                'category'    => 'Suplemen',
                'price'       => 250000,
                'stock'       => 30,
                'description' => 'Meningkatkan performa fisik dan kekuatan dalam aktivitas intensitas tinggi.'
            ],
            [
                'name'        => 'Ekstrak Ginseng Kapsul',
                'category'    => 'Suplemen',
                'price'       => 85000,
                'stock'       => 65,
                'description' => 'Membantu menjaga stamina, energi, dan mengurangi kelelahan.'
            ],
            [
                'name'        => 'Suplemen Sendi Glucosamine',
                'category'    => 'Suplemen',
                'price'       => 150000,
                'stock'       => 45,
                'description' => 'Suplemen pelumas sendi untuk meredakan nyeri akibat radang sendi.'
            ],
            [
                'name'        => 'Ashwagandha Extract Kapsul',
                'category'    => 'Suplemen',
                'price'       => 95000,
                'stock'       => 55,
                'description' => 'Suplemen herbal adaptogen yang membantu tubuh mengelola stres.'
            ],
            [
                'name'        => 'Probiotik 10 Milyar CFU',
                'category'    => 'Suplemen',
                'price'       => 110000,
                'stock'       => 80,
                'description' => 'Bakteri baik untuk menjaga kesehatan flora dan saluran pencernaan.'
            ],
            [
                'name'        => 'Kolagen Peptida Serbuk 150g',
                'category'    => 'Suplemen',
                'price'       => 180000,
                'stock'       => 40,
                'description' => 'Mendukung elastisitas kulit, kesehatan rambut, dan persendian.'
            ],
            [
                'name'        => 'Suplemen Zat Besi (Iron)',
                'category'    => 'Suplemen',
                'price'       => 45000,
                'stock'       => 110,
                'description' => 'Mencegah dan mengatasi anemia akibat kekurangan sel darah merah.'
            ],
            [
                'name'        => 'Melatonin 3mg',
                'category'    => 'Suplemen',
                'price'       => 75000,
                'stock'       => 70,
                'description' => 'Suplemen untuk membantu mengatur siklus tidur dan mengatasi insomnia ringan.'
            ],

            // --- Kategori: Obat Batuk ---
            [
                'name'        => 'Sirup Ekspektoran 60ml',
                'category'    => 'Obat Batuk',
                'price'       => 22000,
                'stock'       => 110,
                'description' => 'Obat sirup untuk meredakan batuk berdahak dan mengencerkan dahak.'
            ],
            [
                'name'        => 'Sirup Batuk Kering (Antitusif) 60ml',
                'category'    => 'Obat Batuk',
                'price'       => 25000,
                'stock'       => 95,
                'description' => 'Meredakan batuk kering yang tidak berdahak dan gatal di tenggorokan.'
            ],
            [
                'name'        => 'Ambroxol Tablet 30mg',
                'category'    => 'Obat Batuk',
                'price'       => 15000,
                'stock'       => 150,
                'description' => 'Agen mukolitik yang bekerja memecah dahak agar mudah dikeluarkan.'
            ],
            [
                'name'        => 'Bromhexine HCl Tablet',
                'category'    => 'Obat Batuk',
                'price'       => 18000,
                'stock'       => 130,
                'description' => 'Mengencerkan sekret saluran pernapasan pada batuk berdahak.'
            ],
            [
                'name'        => 'Dextromethorphan Sirup 60ml',
                'category'    => 'Obat Batuk',
                'price'       => 20000,
                'stock'       => 85,
                'description' => 'Menekan refleks batuk di otak, efektif untuk batuk non-produktif.'
            ],
            [
                'name'        => 'Guaifenesin 100mg Tablet',
                'category'    => 'Obat Batuk',
                'price'       => 12000,
                'stock'       => 160,
                'description' => 'Ekspektoran yang membantu melonggarkan dahak di dada dan tenggorokan.'
            ],
            [
                'name'        => 'Obat Batuk Herbal Madu & Jahe',
                'category'    => 'Obat Batuk',
                'price'       => 35000,
                'stock'       => 120,
                'description' => 'Sirup herbal alami untuk melegakan tenggorokan dan menghangatkan badan.'
            ],
            [
                'name'        => 'Tablet Hisap Pelega Tenggorokan',
                'category'    => 'Obat Batuk',
                'price'       => 10000,
                'stock'       => 300,
                'description' => 'Permen hisap medis untuk meredakan radang dan gatal tenggorokan.'
            ],
            [
                'name'        => 'Sirup Batuk Anak Rasa Jeruk',
                'category'    => 'Obat Batuk',
                'price'       => 28000,
                'stock'       => 90,
                'description' => 'Formulasi khusus batuk berdahak dan pilek untuk anak dengan rasa buah.'
            ],
            [
                'name'        => 'Obat Batuk Hitam (OBH) 100ml',
                'category'    => 'Obat Batuk',
                'price'       => 15000,
                'stock'       => 140,
                'description' => 'Obat batuk legendaris untuk meredakan batuk berdahak dan gejala flu.'
            ]
        ];

        foreach ($obat as $obat) {
            Obat::create($obat);
        }
    }
}
