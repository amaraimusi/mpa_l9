<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Neko;


class NekoController extends BaseXController{
	
	// 画面のバージョン → 開発者はこの画面を修正したらバージョンを変更すること。バージョンを変更するとキャッシュやセッションのクリアが自動的に行われます。
	public $this_page_version = '1.0.0';
	
	/**
	 * indexページのアクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request){

	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');

		// 検索データのバリデーション
	    $validated = $request->validate([
	        'id' => 'nullable|numeric',
			'per_page' => 'nullable|numeric',
		]);
		
		$sesSearches = session('neko_searches_key');// セッションからセッション検索データを受け取る

		// セッション検索データの画面から旧画面バージョンを受け取る
		$new_version = $this->judgeNewVersion($sesSearches, $this->this_page_version);

		$searches = []; // 検索データ
		
		// リクエストのパラメータが空でない、または新バージョンフラグがONである場合、リクエストから検索データを受け取る
		if(!empty($request->all()) || $new_version == 1){
			$searches = [
			    'main_search' => $request->main_search, // メイン検索
			    
			    // CBBXS-3000
				'id' => $request->id, // id
				'neko_val' => $request->neko_val, // neko_val
				'neko_name' => $request->neko_name, // neko_name
				'neko_date' => $request->neko_date, // neko_date
				'neko_type' => $request->neko_type, // 猫種別
				'neko_dt' => $request->neko_dt, // neko_dt
				'neko_flg' => $request->neko_flg, // ネコフラグ
				'img_fn' => $request->img_fn, // 画像ファイル名
				'note' => $request->note, // 備考
				'sort_no' => $request->sort_no, // 順番
				'delete_flg' => $request->delete_flg, // 無効フラグ
				'update_user_id' => $request->update_user_id, // 更新者
				'ip_addr' => $request->ip_addr, // IPアドレス
				'created' => $request->created, // 生成日時
				'modified' => $request->modified, // 更新日

			    // CBBXE
			    
				'sort' => $request->sort, // 並びフィールド
				'desc' => $request->desc, // 並び向き
				'per_page' => $request->per_page, // 行制限数
			];
			
		}else{
			// リクエストのパラメータが空かつ新バージョンフラグがOFFである場合、セッション検索データを検索データにセットする
			$searches = $sesSearches;
		}

		$searches['this_page_version'] = $this->this_page_version; // 画面バージョン
		$searches['new_version'] = $new_version; // 新バージョンフラグ
		session(['neko_searches_key' => $searches]); // セッションに検索データを書き込む

		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$model = new Neko();
		$data = $model->getData($searches);

	   return view('neko.index', [
			'data'=>$data,
			'searches'=>$searches,
			'userInfo'=>$userInfo,
			'this_page_version'=>$this->this_page_version,
	   ]);
		
	}
	
	
	/**
	 * 新規入力画面の表示アクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function create(Request $request){
	    
	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		return view('neko.create', [
			'userInfo'=>$userInfo,
			'this_page_version'=>$this->this_page_version,
			
		]);
		
	}
	
	
	/**
	 * 新規入力画面の登録ボタンアクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function store(Request $request){
	    
	    if(\Auth::id() == null) die;

	    $request->validate([
	        'neko_name' => 'nullable|max:200',
	        'tell' => 'nullable|max:20',
	        'address' => 'nullable|max:200',
	        'note' => 'nullable|max:2000',
			
		]);
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$neko = new Neko();
		$neko->neko_name = $request->neko_name;
		$neko->tell = $request->tell;
		$neko->address = $request->address;
		$neko->note = $request->note;
		$neko->sort_no = $neko->nextSortNo();
		$neko->delete_flg = 0;
		$neko->update_user_id = $userInfo['id'];
		$neko->ip_addr = $userInfo['ip_addr'];

		$neko->save();
		
		return redirect('/neko');
		
	}
	
	
	/**
	 * 詳細画面の表示アクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function show(Request $request){
	    
	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');
	    
	    $model = new Neko();
	    $userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
	    
	    $id = $request->id;
	    if(!is_numeric($id)){
	        echo 'invalid access';
	        die;
	    }
	    
	    $ent = Neko::find($id);

	    return view('neko.show', [
	        'ent'=>$ent,
	        'userInfo'=>$userInfo,
	        'this_page_version'=>$this->this_page_version,
	        
	    ]);
	    
	}
	
	
	/**
	 * 編集画面の表示アクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function edit(Request $request){
	    
	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');

		$model = new Neko();
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$id = $request->id;
		if(!is_numeric($id)){
			echo 'invalid access';
			die;
		}
	
		$ent = Neko::find($id);
		
		return view('neko.edit', [
			'ent'=>$ent,
		    'userInfo'=>$userInfo,
		    'this_page_version'=>$this->this_page_version,
			
		]);
		
	}
	
	
	/**
	 * 新規入力画面の登録ボタンアクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function update(Request $request){
	    
	    if(\Auth::id() == null) die();

		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する

		$request->validate([
		    'neko_name' => 'nullable|max:200',
		    'tell' => 'nullable|max:20',
		    'address' => 'nullable|max:200',
		    'note' => 'nullable|max:2000',
		    
		]);
		
		$neko = Neko::find($request->id);

		$neko->id = $request->id;
		$neko->neko_name = $request->neko_name;
		$neko->tell = $request->tell;
		$neko->address = $request->address;
		$neko->note = $request->note;
		$neko->sort_no = $neko->nextSortNo();
		$neko->delete_flg = 0;
		$neko->update_user_id = $userInfo['id'];
		$neko->ip_addr = $userInfo['ip_addr'];
		
 		$neko->update();
		
		return redirect('/neko');
		
	}
	
	
	/**
	 * 削除/削除取消アクション(無効/有効アクション）
	 */
	public function disabled(){
	    
	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');
	    
	    $userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
	    
	    $json=$_POST['key1'];
	    
	    $param = json_decode($json,true);//JSON文字を配列に戻す
	    $id = $param['id'];
	    $action_flg =  $param['action_flg'];

	    $neko = Neko::find($id);
	    
	    if(empty($action_flg)){
	        $neko->delete_flg = 0; // 削除フラグをOFFにする
	    }else{
	        $neko->delete_flg = 1; // 削除フラグをONにする
	    }
	    
	    $neko->update_user_id = $userInfo['id'];
	    $neko->ip_addr = $userInfo['ip_addr'];
	    
	    $neko->update();
	    
	    $res = ['success'];
	    $json_str = json_encode($res);//JSONに変換
	    
	    return $json_str;
	}
	
	
	/**
	 * 抹消アクション(無効/有効アクション）
	 */
	public function destroy(){
	    
	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');
	    
	    $userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
	    
	    $json=$_POST['key1'];
	    
	    $param = json_decode($json,true);//JSON文字を配列に戻す
	    $id = $param['id'];
	    
	    $neko = new Neko();
	    $neko->destroy($id);// idを指定して抹消（データベースかDELETE）
	    
	    $res = ['success'];
	    $json_str = json_encode($res);//JSONに変換
	    
	    return $json_str;
	}
	
	
	/**
	 * Ajax | ソート後の自動保存
	 *
	 * @note
	 * バリデーション機能は備えていない
	 *
	 */
	public function auto_save(){
	    
	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) die;

		$json=$_POST['key1'];
		
		$data = json_decode($json,true);//JSON文字を配列に戻す
		
		$neko = new Neko();
		$neko->saveAll($data);

		$res = ['success'];
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	
	
	/**
	 * CSVダウンロード
	 *
	 * 一覧画面のCSVダウンロードボタンを押したとき、一覧データをCSVファイルとしてダウンロードします。
	 */
	public function csv_download(){
	    
	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');

		$searches = session('neko_searches_key');// セッションからセッション検索データを受け取る

		$model = new Neko();
		$data = $model->getData($searches, 'csv');
		
		// データ件数が0件ならCSVダウンロードを中断し、一覧画面にリダイレクトする。
		$count = count($data);
		if($count == 0){
		    return redirect('/neko');
		}
		
		// ダブルクォートで値を囲む
		foreach($data as &$ent){
			foreach($ent as $field => $value){
				if(mb_strpos($value,'"')!==false){
					$value = str_replace('"', '""', $value);
				}
				$value = '"' . $value . '"';
				$ent[$field] = $value;
			}
		}
		unset($ent);
		
		//列名配列を取得
		$clms=array_keys($data[0]);
		
		//データの先頭行に列名配列を挿入
		array_unshift($data,$clms);
		
		//CSVファイル名を作成
		$date = new \DateTime();
		$strDate=$date->format("Y-m-d");
		$fn='neko'.$strDate.'.csv';
		
		//CSVダウンロード
		$this->csvOutput($fn, $data);

	}


}