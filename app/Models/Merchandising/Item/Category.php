<?php

namespace App\Models\Merchandising\Item;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Category extends BaseValidator
{
    protected $table = 'item_category';
    protected $primaryKey = 'category_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['category_code','category_name'];

    protected $rules = array(
        'category_code' => 'required',
        'category_name'  => 'required'/*,
        'category_id'  => 'required'*/
    );

    public function __construct()
    {
        parent::__construct();
        /*$this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );*/
    }


    static function getItemListByCategory($categoryId){
        return Category::select(
                'item_subcategory.subcategory_id',
                'item_subcategory.subcategory_code',
                'item_subcategory.subcategory_name',
                'item_category.category_id',
                'item_category.category_code',
                'item_category.category_name'
                )
            ->join('item_subcategory', 'item_category.category_id', '=', 'item_subcategory.category_id')
            ->where(
            [
                ['item_category.status', '=', '1'],
                ['item_subcategory.status', '=', '1'],
                ['item_category.category_id', '=', $categoryId ],
            ])->get();
    }

}
