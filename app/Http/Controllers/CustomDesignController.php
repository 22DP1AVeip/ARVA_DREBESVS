<?php
namespace App\Http\Controllers;

use App\Models\CustomDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CustomDesignController extends Controller
{
    public function create()
    {
        $garments = [
            ['id' => 'tshirt',   'name' => 'Krekls',  'file' => 't-shirt_dizaina.png', 'gender' => 'men'],
            ['id' => 'hoodie',   'name' => 'Hudija',  'file' => 'Hoodie_dizaina.jpg',  'gender' => 'men'],
            ['id' => 'jeans',    'name' => 'Bikses',  'file' => 'Jeans_dizaina.jpg',   'gender' => 'men'],
            ['id' => 'tshirt_w', 'name' => 'Krekls',  'file' => 't-shirt_dizaina.png', 'gender' => 'women'],
            ['id' => 'hoodie_w', 'name' => 'Hudija',  'file' => 'Hoodie_dizaina.jpg',  'gender' => 'women'],
            ['id' => 'jeans_w',  'name' => 'Bikses',  'file' => 'Jeans_dizaina.jpg',   'gender' => 'women'],
        ];

        $presets = [
            ['name' => 'Zvaigzne',  'file' => 'star.png'],
            ['name' => 'Sirds',     'file' => 'heart.png'],
            ['name' => 'Ziedonis',  'file' => 'flower.png'],
            ['name' => 'Vilnis',    'file' => 'water-waves.png'],
            ['name' => 'Geometric', 'file' => 'geometric.png'],
        ];

        $colors = [
            ['hex' => '#ffffff', 'name' => 'Balts'],
            ['hex' => '#000000', 'name' => 'Melns'],
            ['hex' => '#ff0000', 'name' => 'Sarkans'],
            ['hex' => '#0000ff', 'name' => 'Zils'],
            ['hex' => '#008000', 'name' => 'Zals'],
            ['hex' => '#ffff00', 'name' => 'Dzeltens'],
            ['hex' => '#ffc0cb', 'name' => 'Roza'],
            ['hex' => '#808080', 'name' => 'Peleks'],
        ];

        return Inertia::render('DesignView', [
            'garments' => $garments,
            'presets'  => $presets,
            'colors'   => $colors,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'garment_id'      => 'required|string',
            'base_color'      => 'required|string',
            'design_position' => 'required|string',
            'design_size'     => 'required|string',
            'design_image'    => 'nullable|image|max:2048',
            'preset_design'   => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('design_image')) {
            $imagePath = $request->file('design_image')
                ->store('designs/uploads', 'public');
        }

        $design = CustomDesign::create([
            'user_id'           => Auth::id(),
            'garment_id'        => $request->garment_id,
            'base_color'        => $request->base_color,
            'design_image_path' => $imagePath,
            'preset_design'     => $request->preset_design,
            'design_position'   => $request->design_position,
            'design_size'       => $request->design_size,
        ]);

        // Novirza uz dizaina apskatīšanas lapu
        return redirect()->route('design.show', $design->id)
            ->with('success', 'Dizains saglabats!');
    }

    // Rāda saglabāto dizainu
    public function show($id)
    {
        $design = CustomDesign::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $garments = [
            ['id' => 'tshirt',   'name' => 'Krekls',  'file' => 't-shirt_dizaina.png'],
            ['id' => 'hoodie',   'name' => 'Hudija',  'file' => 'Hoodie_dizaina.jpg'],
            ['id' => 'jeans',    'name' => 'Bikses',  'file' => 'Jeans_dizaina.jpg'],
            ['id' => 'tshirt_w', 'name' => 'Krekls',  'file' => 't-shirt_dizaina.png'],
            ['id' => 'hoodie_w', 'name' => 'Hudija',  'file' => 'Hoodie_dizaina.jpg'],
            ['id' => 'jeans_w',  'name' => 'Bikses',  'file' => 'Jeans_dizaina.jpg'],
        ];

        $garment = collect($garments)->firstWhere('id', $design->garment_id);

        return Inertia::render('DesignShow', [
            'design'  => $design,
            'garment' => $garment,
        ]);
    }

    // Pievieno grozam
    public function addToCart($id)
    {
        $design = CustomDesign::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cart = session()->get('cart', []);
        $key  = 'custom_' . $design->id;

        $cart[$key] = [
            'custom_design_id' => $design->id,
            'garment_id'       => $design->garment_id,
            'base_color'       => $design->base_color,
            'quantity'         => 1,
            'is_custom'        => true,
            'price'            => 25.00, // cena pielāgotam dizainam
            'name'             => 'Pielāgots dizains — ' . $design->garment_id,
        ];

        session()->put('cart', $cart);

        return redirect()->route('cart.view')
            ->with('success', 'Pielāgotais dizains pievienots grozam!');
    }
}