<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\SubsectionCreateRequest;
use App\Http\Requests\Article\SubsectionDeleteRequest;
use App\Http\Requests\Article\SubsectionUpdateRequest;
use App\Models\Subsection;
use Illuminate\Http\Request;

class SubsectionController extends Controller
{
        public function store(SubsectionCreateRequest $request): array
    {
        $data = $request->validated();
        $subsection = Subsection::create($data);

        return [
            'subsection' => $subsection
        ];
    }

    public function update(SubsectionUpdateRequest $request, Subsection $subsection)
    {
        $data = $request->validated();

        $subsection->update($data);

        return [
            'subsection' => $subsection
        ];
    }

    public function index(Request $request)
    {
        $section_id = $request->section_id;

        $subsections = Subsection::withPagination();

        if ($section_id)
            $subsections = $subsections->where('section_id', $section_id);

        $subsections = $subsections->get();

        return [
            'subsections' => $subsections
        ];
    }

    public function destroy(SubsectionDeleteRequest $request)
    {
        $ids = explode(',', $request->subsections_ids);
        
        // FIX: Vérifier les permissions individuellement pour chaque sous-section
        foreach ($ids as $id) {
            $subsection = Subsection::find($id);
            if (!$subsection || !auth()->user()->can('delete', $subsection)) {
                abort(403, "Vous n'avez pas l'autorisation de supprimer cette sous-section");
            }
        }
        
        $deleted = Subsection::whereIn('id', $ids)->delete();

        return [
            'deleted' => $deleted
        ];
    }
}
