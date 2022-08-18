<?php 
namespace App\Helpers;

class BaseXHelper
{

    /**
     * ソート機能付きのth要素を作成する
     * @return string
     */
    public static function sortLink(&$searches, $table_name, $field, $wamei)
    {
        
        $now_sort_field = $searches['sort'] ?? ''; // 現在のソートフィールドを取得
        
        $query_param_str = ''; // クエリパラメータ文字列
        foreach ($searches as $prop => $value){
            if($prop == 'sort' || $prop == 'desc') continue;
            if($value === null) continue;
            $query_param_str .= "{$prop}={$value}&";
        }
        
        // クエリパラメータ文字列が空でないなら末尾の一文字「&」を除去
        if(!empty($query_param_str)) $query_param_str=mb_substr($query_param_str,0,mb_strlen($query_param_str)-1);

        $url = '';
        $arrow = '';
        $dire = 'asc'; // 並び向き
        if($now_sort_field == $field){
            $desc_flg = $searches['desc'] ?? 0;
            if(empty($desc_flg)){ // 並び向きが昇順である場合
                $arrow = '▲';
                $url = "?{$query_param_str}&sort={$field}&desc=1";
            }else{ // 並び向きが降順である場合
                $arrow = '▼';
                $url = "?{$query_param_str}&sort={$field}";
            }
        }else{
            $url = "?{$query_param_str}&sort={$field}";
        }
        
        $html = "<a href='{$url}'>{$arrow}{$wamei}</a>";

        return $html;
    }
    
    /**
     * フラグを「有効」、「無効」の形式で表記する
     * @param int $flg フラグ
     * @return string
     */
    public static function notationFlg($flg){
        $notation = "<span class='text-success'>ON</span>";
        if(!empty($flg)){
            $notation = "<span class='text-secondary'>OFF</span>";
        }
        return $notation;
    }
    
    /**
     * 無効フラグを「有効」、「無効」の形式で表記する
     * @param int $delete_flg 無効フラグ
     * @return string
     */
    public static function notationDeleteFlg($delete_flg){
        $notation = "<span class='text-success'>有効</span>";
        if(!empty($delete_flg)){
            $notation = "<span class='text-secondary'>無効</span>";
        }
        return $notation;
    }
    
    
    /**
     * 長文を折りたたみ式にする
     * @param array $ent データのエンティティ
     * @param string $field フィールド名
     * @param int $strLen 表示文字数（バイト）(省略時は無制限に文字表示）
     */
    public static function foldableNote($v, $field,$str_len = null){
        
        $v2="";
        $long_over_flg = 0; // 制限文字数オーバーフラグ
        if(!empty($v)){
            $v = str_replace(array('<','>'),array('&lt;','&gt;'), $v); // XSSサニタイズ
            if($str_len === null){
                $v2 = $v;
            }else{
                if(mb_strlen($v) > $str_len){
                    $v2=mb_strimwidth($v, 0, $str_len * 2);
                    $long_over_flg = 1;
                }else{
                    $v2 = $v;
                }
            }
            $v2= str_replace('\\r\\n', ' ', $v2);
            $v2= str_replace('\\', '', $v2);
        }
        
        // ノート詳細開きボタンのHTMLを作成
        $note_detail_open_html = '';
        if($long_over_flg) {
            $note_detail_open_html = "<input type='button' class='btn btn-secondary btn-sm note_detail_open_btn' value='...' onclick=\"openNoteDetail(this, '{$field}')\" />";
        }
        
        $td = "
			<div>
				<input type='hidden' name='{$field}' value='{$v}' />
				<div class='{$field}' style='white-space:pre-wrap; word-wrap:break-word;'>{$v2}</div>
                {$note_detail_open_html}
			</div>";
        return $td;
    }
    
    
    /**
     * 行入替ボタンを表示する
     * @param [] $searches 検索データ
     */
    public static function rowExchangeBtn(&$searches){
        $html = '';

        // ソートフィールドが「順番」もしくは空である場合のみ、行入替ボタンを表示する。他のフィールドの並びであると「順番」に関して倫理障害が発生するため。
        if($searches['sort'] == 'sort_no' || empty($searches['sort'])){
            $html = "<input type='button' value='↑↓' onclick='rowExchangeShowForm(this)' class='row_exc_btn btn btn-info btn-sm text-light' />";
        }
       return $html;
    }
    
    
    /**
     * 削除/削除取消ボタン（無効/有効ボタン）を表示する
     * @param [] $searches 検索データ
     */
    public static function disabledBtn(&$searches, $id){
        $html = '';
        
        if(empty($searches['delete_flg'])){
            // 削除ボタンを作成
            $html = "<input type='button' data-id='{$id}' onclick='disabledBtn(this, 1)' class='btn btn-danger btn-sm text-light'  value='削除'>";
        }else{
            // 削除取消ボタンを作成
            //$html = "<buttton type='button' data-id='{$id}' onclick='disabledBtn(this, 0)' class='btn btn-success btn-sm text-light' >削除取消</button>";
            $html = "<input type='button' data-id='{$id}' onclick='disabledBtn(this, 0)' class='btn btn-success btn-sm text-light' value='削除取消'>";
        }
        return $html;
    }
    
    
    /**
     * 抹消ボタン
     * @param [] $searches 検索データ
     */
    public static function destroyBtn(&$searches, $id){
        $html = '';
        
        // 削除フラグONの時のみ、抹消ボタンを表示する
        if(!empty($searches['delete_flg'])){
            // 抹消ボタンを作成
            $html = "<input type='button' data-id='{$id}' onclick='destroyBtn(this)' class='btn btn-danger btn-sm text-light' value='抹消'>";
        }
        return $html;
    }
    

    /**
     * JSONに変換して埋め込み
     * @param [] $data
     */
    public static function embedJson($xid, $data){
        
        $jData = [];
        if(gettype($data) == 'object'){
            foreach($data as $ent){
                $jData[] = (array)$ent;
            }
            
        }elseif(gettype($data) == 'array'){
            $jData = $data;
        }else{
            throw new Exception('220709A');
        }
        
        $json = json_encode($jData, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
        $html = "<input type='hidden' id='{$xid}' value='{$json}'>";
        return $html;
    }
    
    
    /**
     * 金額などの数値を3桁区切り表記に変換する
     * @param int $number 任意の数値
     * @throws Exception
     * @return string 3桁区切り表記文字列
     */
    public static function amount($number){
        if($number === '' || $number === null) return null;
        if(!is_numeric($number)) throw new Exception('220711A BaseXHelper:amount:');
        return number_format($number);
        
        
    }
    
}