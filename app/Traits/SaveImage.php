<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

trait SaveImage{
    public function salesDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/sales/'),$filenamenew);
        return $filenamepath;
    }
    
    public function coaDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/coa/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/coa/'),$filenamenew);
        return $filenamepath;
    }

    public function jv1Doc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/jv1/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/jv1/'),$filenamenew);
        return $filenamepath;
    }

    public function pdcDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/pdc/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/pdc/'),$filenamenew);
        return $filenamepath;
    }

    public function jv2Doc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/jv2/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/jv2/'),$filenamenew);
        return $filenamepath;
    }

    public function pur1Doc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/pur1/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/pur1/'),$filenamenew);
        return $filenamepath;
    }

    public function sale1Doc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/sale1/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/sale1/'),$filenamenew);
        return $filenamepath;
    }

    public function sale2Doc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/sale2/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/sale2/'),$filenamenew);
        return $filenamepath;
    }

    public function pur2Doc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/pur2/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/pur2/'),$filenamenew);
        return $filenamepath;
    }

    public function tStockInDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/tstockin/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/tstockin/'),$filenamenew);
        return $filenamepath;
    }
    
    public function StockInDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/stockin/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/stockin/'),$filenamenew);
        return $filenamepath;
    }
    
    public function StockOutDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/stockout/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/stockout/'),$filenamenew);
        return $filenamepath;
    }

    public function tStockOutDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/tstockout/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/tstockout/'),$filenamenew);
        return $filenamepath;
    }

    public function compDoc($file, $extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/complains/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/complains/'),$filenamenew);
        return $filenamepath;
    }

    public function weightDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/weight/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/weight/'),$filenamenew);
        return $filenamepath;
    }

    public function tquotDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/tquotation/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/tquotation/'),$filenamenew);
        return $filenamepath;
    }

    public function quotDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/quotDoc/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/quotDoc/'),$filenamenew);
        return $filenamepath;
    }
    
    public function tpoDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/tpo/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/tpo/'),$filenamenew);
        return $filenamepath;
    }

    public function UserProfile($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/users/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/users/'),$filenamenew);
        return $filenamepath;
    }

    public function poDoc($file,$extension)
    {
        $img = $file;
        $number = rand(1,999);
        $numb = $number / 7 ;
        $filenamenew    = date('Y-m-d')."_.".$numb."_.".$extension;
        $filenamepath   = 'uploads/po/'.$filenamenew;
        $filename       = $img->move(public_path('uploads/po/'),$filenamenew);
        return $filenamepath;
    }
}
?>