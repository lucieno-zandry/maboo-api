<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


class SearchController extends Controller
{
    const TYPES = [
        'products',
        'sellers',
    ];

    protected string $keywords;
    protected Request $request;

    protected function searchByType(string $type): Collection|\Illuminate\Support\Collection
    {
        $limit = $this->request->limit ?: 2;
        $offset = $this->request->offset ?: 0;
        $Model = null;

        switch ($type) {
            case 'products':
                $Model = Product::selectRaw('products.*')
                    ->where('title', 'like', $this->keywords)
                    ->orWhere('description', 'like', $this->keywords);

                if ($limit > 2) {
                    $Model = $Model
                        ->join('product_variants', 'product_variants.product_id', 'products.id')
                        ->orWhere('product_variants.name', 'like', $this->keywords);
                }

                break;

            case 'sellers':
                $query = preg_replace("/[\s]+/", '', $this->format($this->keywords));

                $Model = User::whereRaw("CONCAT(LOWER(name), LOWER(firstname)) like LOWER(\"{$query}\")")
                    ->orWhereRaw("CONCAT(LOWER(firstname), LOWER(name)) like LOWER(\"{$query}\")");

                break;

            default:
                break;
        }

        $result = $Model
            ->limit($limit)
            ->offset($offset)
            ->distinct()
            ->get();

        return $result;
    }

    protected function format(string $string): string
    {
        $trimed = trim($string);
        $stripped = strip_tags($trimed);
        $htmled = htmlspecialchars($stripped);

        return $htmled;
    }

    public function search(string $query, Request $request)
    {
        $results = [];


        $this->keywords = "%$query%";
        $this->request = $request;

        if ($request->type) {
            $results[$request->type] = $this->searchByType($request->type);
        } else {
            foreach (self::TYPES as $type) {
                $results[$type] = $this->searchByType($type);
            }
        }

        return response()->json($results);
    }
}
