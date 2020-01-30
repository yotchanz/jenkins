<?php

namespace App\Http\Controllers;

use App\Keys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KeyController extends Controller
{
    public function __construct()
    {

    }
    
    /**
     * データ取得メソッド
     */
    public function select()
    {
      return DB::table('keys')->where('delete_flg', 0)->get();
    }
    
    /**
     * キー情報更新メソッド
     */
    public function update(Request $request)
    {
      $users = DB::table('keys')->where('keyid', $request['keyid'])->count();
       
      if ($users == 0) {
        return;
      }
    
      DB::table('keys')
              ->where('keyid', $request['keyid'])
              ->update(['accept_startdate' => $request->accept_startdate,
                        'accept_enddate' => $request->accept_enddate,
                        'cylinder_info' => $request->cylinder_info]);
                        
      $headers = [
        'Content-type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename=csvexport.csv',
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0',
      ];
      
      $callback = function() use($request)
      {      
        $createCsvFile = fopen(realpath('../SLP/csv/') . '\\test.csv', 'w'); //ファイル作成
        
        $replacedWord = array("-", " ", ":");
        $cylinderInfos = explode(",", $request->cylinder_info);

        foreach($cylinderInfos as $value) {
          $csv = ['1',
                  '12345',
                  str_replace($replacedWord, "", $request->accept_startdate),
                  str_replace($replacedWord, "", $request->accept_enddate),
                  $request->keyid,
                  substr($value, 0, -1),
                  substr($value, -1, 1)
                 ];
          
          fputcsv($createCsvFile, $csv); //ファイルに追記する
        }
        
        fclose($createCsvFile); //ファイル閉じる
      };
        
      return response()->stream($callback, 200, $headers); //ここで実行
    }
}