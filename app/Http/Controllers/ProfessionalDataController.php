<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessionalDataUpdateRequest;
use App\Models\ProfessionalData;
use App\Models\User;
use Illuminate\Http\Request;

class ProfessionalDataController extends Controller
{
    public function show(int $professional_id)
    {
        $user = User::with('professional_data')->findOrFail($professional_id);

        return [
            'professional' => $user
        ];
    }

    public function update(ProfessionalDataUpdateRequest $request)
    {
        $data = $request->validated();
        $professionalData = ProfessionalData::where('user_id', $data['user_id'])->first();

        if (!$professionalData) {
            $professionalData = ProfessionalData::create($data);
        }

        return [
            'professional_data' => $professionalData
        ];
    }

    public function index()
    {
        $professionals = User::withPagination()
            ->with('professional_data')
            ->where('type', User::TYPE_PROFESSIONAL)
            ->get();

        return [
            'professionals' => $professionals
        ];
    }
}
