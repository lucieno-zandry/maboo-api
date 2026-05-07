<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use App\Models\VariantGroup;
use App\Models\VariantOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------
        // Catégories Maboo
        // -----------------------------
        $categories = [
            1 => 'Mode Bébé (0-24 mois)',
            2 => 'Maman & Puériculture',
        ];

        $categoryModels = [];
        foreach ($categories as $id => $title) {
            $categoryModels[$id] = Category::create([
                'title' => $title,
            ]);
        }

        // -----------------------------
        // Charger les produits depuis products.json
        // -----------------------------
        $jsonPath = database_path('data/products.json'); // à adapter selon l'emplacement réel
        if (!File::exists($jsonPath)) {
            $this->command->error("Fichier products.json introuvable au chemin : $jsonPath");
            return;
        }

        $productsData = json_decode(File::get($jsonPath), true);
        if (!$productsData) {
            $this->command->error("Erreur de décodage de products.json");
            return;
        }

        foreach ($productsData as $item) {
            $categoryId = $item['category_id']; // 1 ou 2
            if (!isset($categoryModels[$categoryId])) {
                $this->command->warn("Catégorie $categoryId non trouvée pour le produit {$item['title']}");
                continue;
            }

            $product = Product::create([
                'title' => $item['title'],
                'slug' => Str::slug($item['title']) . '-' . Str::uuid(),
                'description' => $item['description'],
                'category_id' => $categoryModels[$categoryId]->id,
            ]);

            // Ajout des variantes selon le type de produit
            $this->creerVariantesPourMaboo($product, $item);
        }
    }

    /**
     * Crée les variantes adaptées à chaque produit Maboo
     */
    private function creerVariantesPourMaboo(Product $product, array $item): void
    {
        $title = $product->title;
        $basePrice = (int) $item['price'];      // en cents ? Ici les prix sont en francs ? On suppose en centimes ou unité ? Dans le JSON c'est "20000" pour brassière. Gardons tel quel.
        $baseStock = (int) $item['inStock'];

        // -----------------------------
        // Vêtements bébé (brassière, body, ensemble, pelisse, pyjama, trousseau, peignoir)
        // -----------------------------
        if ($this->estVetementBebe($title)) {
            $this->creerVariantesVetementBebe($product, $title, $basePrice, $baseStock);
        }
        // Trousseau spécifique (multi-pièces)
        elseif (str_contains($title, 'Trousseau')) {
            $this->creerVariantesTrousseau($product, $basePrice, $baseStock);
        }
        // Pantoufle (pointures)
        elseif (str_contains($title, 'Pantoufle')) {
            $this->creerVariantesPantoufle($product, $basePrice, $baseStock);
        }
        // Peignoir (tailles adultes S à XXL)
        elseif (str_contains($title, 'Peignoir')) {
            $this->creerVariantesPeignoir($product, $basePrice, $baseStock);
        }
        // Pyjama (tailles S à XXL)
        elseif (str_contains($title, 'Pyjama')) {
            $this->creerVariantesPyjama($product, $basePrice, $baseStock);
        }
        // Sac à langer (couleurs)
        elseif (str_contains($title, 'Sac à Langer')) {
            $this->creerVariantesSacALanger($product, $basePrice, $baseStock);
        }
        // Tire-lait manuel (article unique)
        elseif (str_contains($title, 'Tire Lait')) {
            $this->creerVarianteUnique($product, $basePrice, $baseStock);
        }
        // Autres produits (fallback)
        else {
            $this->creerVarianteUnique($product, $basePrice, $baseStock);
        }
    }

    private function estVetementBebe(string $title): bool
    {
        $keywords = ['Brassière', 'Body', 'Ensemble', 'Pelisse', 'Pyjama MC', 'Pyjama ML'];
        foreach ($keywords as $kw) {
            if (str_contains($title, $kw)) return true;
        }
        return false;
    }

    /**
     * Vêtements bébé : tailles 0-3 mois, 3-6 mois, 6-12 mois + couleurs éventuelles
     */
    private function creerVariantesVetementBebe(Product $product, string $title, int $basePrice, int $baseStock): void
    {
        // Groupe Taille
        $groupeTaille = VariantGroup::create([
            'product_id' => $product->id,
            'name' => 'Taille (mois)'
        ]);
        $tailles = ['0-3 mois', '3-6 mois', '6-12 mois'];
        $optionsTaille = collect($tailles)->map(fn($t) => VariantOption::create([
            'variant_group_id' => $groupeTaille->id,
            'value' => $t
        ]));

        // Groupe Couleur (si pertinent)
        $couleurs = $this->getCouleursPourVetement($title);
        $groupeCouleur = null;
        $optionsCouleur = collect();
        if (!empty($couleurs)) {
            $groupeCouleur = VariantGroup::create([
                'product_id' => $product->id,
                'name' => 'Couleur'
            ]);
            $optionsCouleur = collect($couleurs)->map(fn($c) => VariantOption::create([
                'variant_group_id' => $groupeCouleur->id,
                'value' => $c
            ]));
        }

        // Création des combinaisons
        foreach ($optionsTaille as $taille) {
            if ($groupeCouleur && $optionsCouleur->isNotEmpty()) {
                foreach ($optionsCouleur as $couleur) {
                    $variant = Variant::create([
                        'product_id' => $product->id,
                        'sku' => strtoupper(Str::slug($product->title . '-' . $taille->value . '-' . $couleur->value)),
                        'price' => $basePrice,
                        'stock' => max(1, (int)($baseStock / (count($tailles) * count($couleurs)))),
                    ]);
                    $variant->variant_options()->sync([$taille->id, $couleur->id]);
                }
            } else {
                $variant = Variant::create([
                    'product_id' => $product->id,
                    'sku' => strtoupper(Str::slug($product->title . '-' . $taille->value)),
                    'price' => $basePrice,
                    'stock' => max(1, (int)($baseStock / count($tailles))),
                ]);
                $variant->variant_options()->sync([$taille->id]);
            }
        }
    }

    private function getCouleursPourVetement(string $title): array
    {
        if (str_contains($title, 'Brassière')) return ['Blanc', 'Beige', 'Bleu clair'];
        if (str_contains($title, 'Body')) return ['Blanc', 'Jaune poussin', 'Vert menthe'];
        if (str_contains($title, 'Ensemble')) {
            if (str_contains($title, 'Laine')) return ['Jaune', 'Bleu ciel', 'Rose', 'Bleu marine'];
            return ['Blanc', 'Gris chiné', 'Écru'];
        }
        if (str_contains($title, 'Pelisse')) return ['Rose poudré', 'Bleu clair', 'Moutarde'];
        if (str_contains($title, 'Pyjama')) return ['Blanc', 'Bleu', 'Rose'];
        return [];
    }

    private function creerVariantesTrousseau(Product $product, int $basePrice, int $baseStock): void
    {
        // Trousseau : pas de variante (coffret unique)
        Variant::create([
            'product_id' => $product->id,
            'sku' => strtoupper(Str::slug($product->title)) . '-KIT',
            'price' => $basePrice,
            'stock' => $baseStock
        ]);
    }

    private function creerVariantesPantoufle(Product $product, int $basePrice, int $baseStock): void
    {
        $groupePointure = VariantGroup::create([
            'product_id' => $product->id,
            'name' => 'Pointure'
        ]);
        $pointures = ['36', '37', '38', '39', '40', '41'];
        $optionsPointure = collect($pointures)->map(fn($p) => VariantOption::create([
            'variant_group_id' => $groupePointure->id,
            'value' => $p
        ]));

        foreach ($optionsPointure as $pointure) {
            Variant::create([
                'product_id' => $product->id,
                'sku' => strtoupper(Str::slug($product->title . '-' . $pointure->value)),
                'price' => $basePrice,
                'stock' => max(1, (int)($baseStock / count($pointures))),
            ])->variant_options()->sync([$pointure->id]);
        }
    }

    private function creerVariantesPeignoir(Product $product, int $basePrice, int $baseStock): void
    {
        $groupeTaille = VariantGroup::create([
            'product_id' => $product->id,
            'name' => 'Taille'
        ]);
        $tailles = ['S', 'M', 'L', 'XL', 'XXL'];
        $optionsTaille = collect($tailles)->map(fn($t) => VariantOption::create([
            'variant_group_id' => $groupeTaille->id,
            'value' => $t
        ]));

        foreach ($optionsTaille as $taille) {
            Variant::create([
                'product_id' => $product->id,
                'sku' => strtoupper(Str::slug($product->title . '-' . $taille->value)),
                'price' => $basePrice + ($taille->value === 'XXL' ? 5000 : 0), // petit ajustement
                'stock' => max(1, (int)($baseStock / count($tailles))),
            ])->variant_options()->sync([$taille->id]);
        }
    }

    private function creerVariantesPyjama(Product $product, int $basePrice, int $baseStock): void
    {
        // Pyjama : taille + couleurs (blanc, bleu, rose)
        $groupeTaille = VariantGroup::create([
            'product_id' => $product->id,
            'name' => 'Taille'
        ]);
        $tailles = ['S', 'M', 'L', 'XL', 'XXL'];
        $optionsTaille = collect($tailles)->map(fn($t) => VariantOption::create([
            'variant_group_id' => $groupeTaille->id,
            'value' => $t
        ]));

        $groupeCouleur = VariantGroup::create([
            'product_id' => $product->id,
            'name' => 'Couleur'
        ]);
        $couleurs = ['Blanc', 'Bleu', 'Rose'];
        $optionsCouleur = collect($couleurs)->map(fn($c) => VariantOption::create([
            'variant_group_id' => $groupeCouleur->id,
            'value' => $c
        ]));

        foreach ($optionsTaille as $taille) {
            foreach ($optionsCouleur as $couleur) {
                Variant::create([
                    'product_id' => $product->id,
                    'sku' => strtoupper(Str::slug($product->title . '-' . $taille->value . '-' . $couleur->value)),
                    'price' => $basePrice,
                    'stock' => max(1, (int)($baseStock / (count($tailles) * count($couleurs)))),
                ])->variant_options()->sync([$taille->id, $couleur->id]);
            }
        }
    }

    private function creerVariantesSacALanger(Product $product, int $basePrice, int $baseStock): void
    {
        $groupeCouleur = VariantGroup::create([
            'product_id' => $product->id,
            'name' => 'Couleur'
        ]);
        $couleurs = ['Marron', 'Bleu', 'Rose'];
        $optionsCouleur = collect($couleurs)->map(fn($c) => VariantOption::create([
            'variant_group_id' => $groupeCouleur->id,
            'value' => $c
        ]));

        foreach ($optionsCouleur as $couleur) {
            Variant::create([
                'product_id' => $product->id,
                'sku' => strtoupper(Str::slug($product->title . '-' . $couleur->value)),
                'price' => $basePrice,
                'stock' => max(1, (int)($baseStock / count($couleurs))),
            ])->variant_options()->sync([$couleur->id]);
        }
    }

    private function creerVarianteUnique(Product $product, int $basePrice, int $baseStock): void
    {
        Variant::create([
            'product_id' => $product->id,
            'sku' => strtoupper(Str::slug($product->title)) . '-UNIQ',
            'price' => $basePrice,
            'stock' => $baseStock
        ]);
    }
}
