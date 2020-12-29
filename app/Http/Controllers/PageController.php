<?php

namespace App\Http\Controllers;
use App\Slide;
use App\Product;
use App\ProductType;
use App\Cart;
use Session;


use Illuminate\Http\Request;

class PageController extends Controller
{
    public function getIndex(){
    	// Lay thong tin co trong bang slide
        $slide =Slide::all();
    	//return view('page.trangchu',['slide'=>$slide]);

        // Lay san pham hien thi cho san pham moinhat
        $new_product = Product::where('new',1)->paginate(8);
        

        // Lay san pham hien thi khuyen mai

        //$sanpham_khuyenmai = Product::where('promotion_price','<>',0) ->limit(4) ->orderBy('promotion_price')->get();
        $sanpham_khuyenmai = Product::where('promotion_price','<>',0) ->orderBy('promotion_price') ->paginate(4);
      

    	return view('page.trangchu',compact('slide','new_product','sanpham_khuyenmai'));
    }


    public function getLoaiSp($type){
        //Lay san pham hien thi theoloai
        $sp_theoloai= Product::where('id_type',$type) ->limit(3)->get();

       // Lay san pham hien thi Khac <> loai
        $sp_khac= Product::where('id_type','<>',$type)->limit(3)->get();

        // Lay san pham hien thi theoloai typeproduct  cho menu ben trai
        $loai = ProductType::all();

        // Lay ten Loai san pham moi khi chung ta chon danh muc loai san pham(phan menu ben trai)
         $loai_sp = ProductType::where('id',$type)->first();

    	return view('page.loai_sanpham',compact('sp_theoloai','sp_khac','loai','loai_sp'));
    }

    public function getChitiet(Request $req){
        // Lay san pham chi tiet, nghiala chung ta kich chuot vao 1 san pham thi no cho chi tiet rieng sang pham do theo id 
        $sanpham = Product::where('id',$req->id)->first();

      // Lay san pham lien quan = la san pham tuong tu 
        $sp_tuongtu = Product::where('id_type',$sanpham->id_type)->paginate(6);

        // Lay san pham ban chay = la san pham co truong new =1
        $sp_banchay = Product::where('promotion_price','=',0) ->paginate(3);

          // Lay san pham MOI NHAT LA SAN PHAM MOI CAP NHAT
       // $sp_new = Product::where('promotion_price','=',0) ->paginate(3);

        $sp_new = Product::select('id', 'name', 'id_type', 'description', 'unit_price', 'promotion_price', 'image', 'unit', 'new', 'created_at', 'updated_at')->where('new','>',0)->orderBy('updated_at','ASC')->paginate(3);


    return view('page.chitiet_sanpham',compact('sanpham','sp_tuongtu','sp_banchay','sp_new'));

    }

    public function getAddToCart(Request $req, $id){
        $product = Product::find($id);
        $oldCart = Session('cart')?Session::get('cart'):null;
        $cart = new Cart($oldCart);
        $cart->add($product,$id);
        $req->session()->put('cart', $cart);
        return redirect()->back();
    }

    public function getDelItemCart($id){
        $oldCart = Session::has('cart')?Session::get('cart'):null;
        $cart = new Cart($oldCart);
        $cart->removeItem($id);
        if(count($cart->items)>0){
        Session::put('cart',$cart);

        }
        else{
            Session::forget('cart');
        }
        return redirect()->back();
    }


    public function getLienhe(){
    return view('page.lienhe');
    }

    public function getAbout(){
    return view('page.about');
    }


}
