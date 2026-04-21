<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomDesign extends Model
{
    protected $fillable = [
        'user_id',
        'garment_id',
        'product_id',
        'base_color',
        'design_image_path',
        'preset_design',
        'design_position',
        'design_size',
    ];
}