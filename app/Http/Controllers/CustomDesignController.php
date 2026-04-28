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

        if ($request->boolean('add_to_cart')) {
            $cart  = session()->get('cart', []);
            $views = session()->get('cart_views', []);
            $key   = 'custom_' . $design->id;
            $cart[$key]  = 1;
            $views[$key] = 'custom';
            session()->put('cart', $cart);
            session()->put('cart_views', $views);
            return redirect()->route('cart.view')->with('success', 'Dizains pievienots grozam!');
        }

        return redirect()->route('profile.designs')->with('success', 'Dizains saglabāts!');
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

        $cart  = session()->get('cart', []);
        $views = session()->get('cart_views', []);
        $key   = 'custom_' . $design->id;

        $cart[$key]  = ($cart[$key] ?? 0) + 1;
        $views[$key] = 'custom';
        session()->put('cart', $cart);
        session()->put('cart_views', $views);

        return back()->with('success', 'Pielāgotais dizains pievienots grozam!');
    }

    // Visi lietotāja dizaini profilā
    public function profileIndex(Request $request)
    {
        $garmentNames = [
            'tshirt'   => 'Krekls (V)',
            'hoodie'   => 'Hudija (V)',
            'jeans'    => 'Bikses (V)',
            'tshirt_w' => 'Krekls (S)',
            'hoodie_w' => 'Hudija (S)',
            'jeans_w'  => 'Bikses (S)',
        ];

        $garmentFiles = [
            'tshirt'   => 't-shirt_dizaina.png',
            'hoodie'   => 'Hoodie_dizaina.jpg',
            'jeans'    => 'Jeans_dizaina.jpg',
            'tshirt_w' => 't-shirt_dizaina.png',
            'hoodie_w' => 'Hoodie_dizaina.jpg',
            'jeans_w'  => 'Jeans_dizaina.jpg',
        ];

        $designs = CustomDesign::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn($d) => [
                'id'            => $d->id,
                'garment_id'    => $d->garment_id,
                'garment_name'  => $garmentNames[$d->garment_id] ?? $d->garment_id,
                'garment_file'  => $garmentFiles[$d->garment_id] ?? null,
                'base_color'    => $d->base_color,
                'preset_design' => $d->preset_design,
                'design_position' => $d->design_position,
                'design_size'   => $d->design_size,
                'created_at'    => $d->created_at->format('d.m.Y'),
            ]);

        return Inertia::render('Profile/Designs', [
            'designs' => $designs,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        CustomDesign::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return back()->with('success', 'Dizains dzēsts.');
    }
}