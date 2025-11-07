<?php

namespace App\Http\Controllers;

use App\Models\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductColorController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:2|max:20',
            'code' => 'required|min:6',
            'product_id' => 'required|numeric'
        ]);

        $color = ProductColor::create($validated);
        return response()->json(['color' => $color]);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|min:2|max:20',
            'code' => 'nullable|min:6',
        ]);

        $color = ProductColor::find($id);

        if (!$color)
            throw ValidationException::withMessages(['id' => 'The color does not exist']);

        $color->update($validated);
        return response()->json(['color' => $color]);
    }

    public function delete(Request $request)
    {
        $ids = $request->validate(['ids' => 'required|array'])['ids'];
        $deleted = ProductColor::whereIn('id', $ids)->delete();

        return response()->json(['deleted' => $deleted]);
    }
}
