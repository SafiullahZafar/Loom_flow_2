<?php

namespace App\Http\Controllers;

use App\Models\YarnQuality;
use App\YarnQualityWeightCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class YarnQualityController extends Controller
{
    public function store(Request $request, YarnQualityWeightCalculator $weightCalculator): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:yarn_qualities,name',
            'construction' => 'nullable|string|max:150',
            'read' => 'required|numeric|gt:0|max:100000',
            'pick' => 'required|numeric|gt:0|max:100000',
            'warp_count' => 'required|numeric|gt:0|max:100000',
            'weft_count' => 'required|numeric|gt:0|max:100000',
            'width_inches' => 'required|numeric|min:20|max:150',
            'unit_cost_meter' => 'required|numeric|min:0',
        ]);

        $weights = $weightCalculator->calculate(
            (float) $validated['read'],
            (float) $validated['pick'],
            (float) $validated['width_inches'],
            (float) $validated['warp_count'],
            (float) $validated['weft_count']
        );
        $quality = YarnQuality::create([...$validated, ...$weights]);

        return back()->with('createdYarnQuality', $quality->name)
            ->with('success', "Yarn quality {$quality->name} added and selected.");
    }
}
