<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Imports\ExcelIn;
use Excel;

class DocumentUploadController extends Controller
{
  
    public function uploadfile(Request $request){
        $import = new ExcelIn;
        $array  = Excel::toArray($import, $request->file('file'));
        return $this->sendResponse($array, 'User');
    }
}
