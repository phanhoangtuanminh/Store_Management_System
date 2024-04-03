<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;
class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api'); //bắt buộc khi sử dụng phải đăng nhập
    }
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        //
        $product = Product::with ([ 'brand','category',
        ])->get();
        return response()->json($product);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if ($request->user()->can('create-products')) {
            $product = new Product();
            $product->name = $request->input('name');
            $product->product_code = $this->generateProductCode();
            $product->amount = $request->input('amount');
            $product->gender_item_code = $request->input('gender_item_code');
            $product->import_price = $request->input('import_price');
            $product->sell_price = $request->input('sell_price');
            $product->size = $request->input('size');
            $product->brand_id = $request->input('brand_id');
            $product->category_id = $request->input('category_id');
            $product->save();
    
            return response()->json($product);
        }
    
        return response([
            'status' => false,
            'message' => 'You don\'t have permission to create Product!' 
        ], 404);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
        return $product;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $product = Product::find($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $product = Product::find($id);
        $product->update($request->all());

        return response()->json('Product successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        //
        if ($request->user()->can('delete-products')) {
            $product = Product::find($id);
            $product->delete();
            return response([
                'status' => true,
            ], 200);
        }
    
        return response([
            'status' => false,
            'message' => 'You don\'t have permission to delete Product!' 
        ], 200);
    }

    public function get_product_data()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function uploadImage(Request $request)
    {
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->file('image')) {
        $image = $request->file('image');
        $file_name = time() . '_' . $image->getClientOriginalName();
        $path = $request->file('image')->storeAs('images', $file_name, 'public');

        // Lưu đường dẫn hình ảnh vào cơ sở dữ liệu
        auth()->user()->update(['image' => $path]);

        return response()->json(['image' => $path]);
    }

    return response()->json(['error' => 'Failed to upload image.']);
}
public function generateProductCode() {
    $digits = 4; // Số lượng chữ số
    $letters = 2; // Số lượng chữ cái

    $numbers = '';
    for ($i = 0; $i < $digits; $i++) {
        $numbers .= mt_rand(0, 9); // Tạo ngẫu nhiên chữ số từ 0 đến 9
    }

    $characters = '';
    $lettersRange = range('A', 'Z'); // Mảng chứa các chữ cái từ A đến Z
    for ($i = 0; $i < $letters; $i++) {
        $characters .= $lettersRange[array_rand($lettersRange)]; // Chọn ngẫu nhiên một chữ cái từ mảng
    }

    return 'PROD' . $numbers . $characters;
}
}
