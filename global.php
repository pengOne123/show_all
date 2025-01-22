<?php
/**
 * Excel文件导出
 * excelnane 文件名
 * fileurl  保存路径
 * td 表格的列
 * array 数据
 * colorarry 表格框颜色
 **/

function excel_push($excelnane,$fileurl,$td,$array,$colorarry){
    header("Content-Type:text/html; charset=utf-8");
    error_reporting(E_ALL);
    pc_base::load_sys_class('PHPExcel', '', 0);
    $objPHPExcel = new PHPExcel();
    $excelnane = str_replace('+','%20',urlencode($excelnane));
    $fileName = $fileurl.$excelnane;
    $objPHPExcel
        ->getProperties()  //获得文件属性对象，给下文提供设置资源
        ->setCreator( "MaartenBalliauw")             //设置文件的创建者
        ->setLastModifiedBy( "MaartenBalliauw")       //设置最后修改者
        ->setTitle( "Office2007 XLSX Test Document" )    //设置标题
        ->setSubject( "Office2007 XLSX Test Document" )  //设置主题
        ->setDescription( "Test document for Office2007 XLSX, generated using PHP classes.") //设置备注
        ->setKeywords( "office 2007 openxmlphp")        //设置标记
        ->setCategory( "Test resultfile");                //设置类别
    // 位置aaa *为下文代码位置提供锚
    //给表格添加数据

    foreach($td as $key=>$val){
        $objPHPExcel->getActiveSheet()->getColumnDimension($key)->setWidth(22);
        $end = $key;
    }
    $objPHPExcel->getActiveSheet()->getStyle($colorarry[0])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($colorarry[0])->getFill()->getStartColor()->setARGB($colorarry[1]);
    $objPHPExcel->getActiveSheet()->getStyle(key($td).'1:'.$end.'1')->getFont()->setBold(true);

    //TH标题列
    foreach($td as $key=>$val){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $key.'1', $val );
    }


    for($i=0;$i<count($array);$i++){
        foreach($td as $key=>$val){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $key.($i+2), $array[$i][$key] );
        }
    }

    $objWriter =PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($fileName);

    header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition:attachment;filename="'.$excelnane.'"');
    header('Cache-Control:max-age=0');
    $objWriter = PHPExcel_IOFactory:: createWriter($objPHPExcel, 'Excel5');
    $objWriter->save( 'php://output');
    unlink($fileName);
}

/**
 * 断点测试数据
 **/

function varDump($data='1')
{

    if (is_array($data)) {

        echo "<pre>";
        var_dump($data);

    } else if(is_string($data)||is_numeric($data)) {
        die("$data");
    }

    die("数据格式不支持查看此方法");
}

/**
 * 根据id进行目录分组
 **/
function getUserHeadFilePath($head, $id)
{
    $dir1 = ceil ( $id / 10000 );
    $dir2 = ceil ( $id % 10000 / 1000 );
    $head_path = $dir1 . "/" . $dir2 . "/" . $id;
    return $head . $head_path.'/';
}

/**
 * 获得url code
 **/
function get_http_response_code($theURL) {
    $headers = get_headers($theURL);
    return substr($headers[0], 9, 3);
}

/**
 * 下载文件到本地指定路径
 **/
function download_url_local($url,$save_dir='',$filename='',$type=0)
{
    if ( trim ( $url ) == '' ) {
        return array( 'file_name' => '' , 'save_path' => '' , 'error' => 1 );
    }
    if ( trim ( $save_dir ) == '' ) {
        $save_dir = './';
    }
    if ( trim ( $filename ) == '' ) {//保存文件名
        $ext = strrchr ( $url , '.' );
        if ( $ext != '.gif' && $ext != '.jpg' ) {
            return array( 'file_name' => '' , 'save_path' => '' , 'error' => 3 );
        }
        $filename = time () . $ext;
    }
    if ( 0 !== strrpos ( $save_dir , '/' ) ) {
        $save_dir .= '/';
    }
    //创建保存目录
    if ( !file_exists ( $save_dir ) && !mkdir ( $save_dir , 0777 , true ) ) {
        return array( 'file_name' => '' , 'save_path' => '' , 'error' => 5 );
    }
    //获取远程文件所采用的方法
    if ( $type ) {
        $ch = curl_init ();
        $timeout = 5;
        curl_setopt ( $ch , CURLOPT_URL , $url );
        curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , 1 );
        curl_setopt ( $ch , CURLOPT_CONNECTTIMEOUT , $timeout );
        $img = curl_exec ( $ch );
        curl_close ( $ch );
    } else {
        ob_start ();
        readfile ( $url );
        $img = ob_get_contents ();
        ob_end_clean ();
    }
    //$size=strlen($img);
    //文件大小
    $fp2 = @fopen ( $save_dir . $filename , 'a' );
    fwrite ( $fp2 , $img );
    fclose ( $fp2 );
    unset( $img , $url );
    return array( 'file_name' => $filename , 'save_path' => $save_dir . $filename , 'error' => 0 );
}

/**
 * 获得判断资源是否存在
 **/
function is_url( $url )
{
    if ( filter_var ( $url , FILTER_VALIDATE_URL ) === false ) {
        return false;
    }

    if ( intval ( get_http_response_code ( $url ) < 400 ) ) {
        return true;
    }
    return false;
}

/**
 * oss qc 生成小说zip
 **/
function add_admin_chapter( $json_db , $book_id , $page , $scroll_id )
{
    $json_db = json_decode ( $json_db );
    $dir_path = PHPCMS_PATH . "uploadfile/book/" . $book_id . '/';

    if ( !is_dir ( $dir_path . $scroll_id ) ) {
        mkdir ( $dir_path . $scroll_id , 0777 , true );
    }
    $filename = $dir_path . $scroll_id . "/" . $page . ".zip";

    if ( file_exists ( $filename ) ) {
        @unlink ( $filename );
    }

    $zip = new ZipArchive ();

    if ( $zip->open ( $filename , ZIPARCHIVE::CREATE ) !== TRUE ) {
        return false;
    }

    $jsond = '';
    foreach ( $json_db as $key => $vas ) {
        if ( $vas->type == 1 ) {
            $imgUrl = str_replace ( "http://cdnqc.517w.com/" , "" , $vas->value );//图片的地址
            $imgCdnUlr = QC_APP_CDN_IMG . parse_url ( $imgUrl , PHP_URL_PATH );
            if ( !is_url ( $imgCdnUlr ) ) {
                continue;
            }
            $imgZipName = substr ( $vas->value , strrpos ( $vas->value , '/' , 1 ) + 1 );//文件名
            download_url_local ( $imgCdnUlr , $dir_path , SYS_TIME . $imgZipName );
            $zip->addFile ( $dir_path . SYS_TIME . $imgZipName , $imgZipName ); // 第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
            $ArrUnlink[] = $dir_path . SYS_TIME . $imgZipName;
            $jsond[ $key ] = array( "type" => $vas->type , "value" => $imgZipName );
        } else {
            $jsond[ $key ] = array( "type" => $vas->type , "value" => urlencode ( str_replace ( '\\' , '\\\\' , $vas->value ) ) );
        }
    }

    $json_name = SYS_TIME . rand ( 10 , 1000 );

    $uysyl = urldecode ( json_encode ( $jsond ) );
    $unsy = json_decode ( $uysyl , true );


    if ( file_put_contents ( $dir_path . $json_name . "json" , $uysyl , FILE_APPEND ) ) {
        $zip->addFile ( $dir_path . $json_name . "json" , "1.json" );
    }

    $zip->close (); // 关闭
    $ArrUnlink[] = $dir_path . $json_name . "json";

    if ( isset( $ArrUnlink ) && is_array ( $ArrUnlink ) ) {
        foreach ( $ArrUnlink as $value ) {
            if ( file_exists ( $value ) ) {
                unlink ( $value );
            }
        }
    }

    if ( empty( $unsy ) ) {
        unlink ( $filename );
        return false;
    } else {
        //上传到oss
        pc_base::load_sys_class ( 'oss' , '' , 0 );
        $oss = new oss('djc_qc');

        $file = "book/" . $book_id . '/' . $scroll_id . "/" . $page . ".zip";

        $mess = $oss->uploadFile ( $filename , $file );
        unlink ( $filename );
        if ( $mess === true ) {
            return $file;
        } else {
            showmessage ( '上传文件失败!' );
        }

        return true;
    }

}

//url替换参数
function url_rep_value($url,$key,$value)
{
    $a = explode ( '?' , $url );
    $url_f = $a[ 0 ];
    $query = $a[ 1 ];
    parse_str ( $query , $arr );
    $arr[ $key ] = $value;

    return $url_f . '?' . http_build_query ( $arr );
}

//转码
function iconv_to_utf8($keyword, $to='UTF-8'){
    $encode = mb_detect_encoding($keyword, array('ASCII','UTF-8','GBK','GB2312'));
    if($encode != $to){
        $keyword = iconv($encode, $to, $keyword);
    }
    return $keyword;
}

//大数据单order 翻转limit算法 因为sql排序翻转了 所以在外部数据要自己翻转回来 用array_reverse()即可
function big_limit_handle($total,$cut_start,$cut_num,$desc=true)
{
    $mid = floor ( $total / 2 );
    //如果大于间隔 则翻转
    if ( $cut_start > $mid ) {
        $desc = !$desc;
    }else{
        return false;
    }

    //下面是翻转算法

    $h_limit = $total - ( $cut_start + $cut_num );

    $h_cut_num=$cut_num;

    //如果小于0说明是最后一页
    if($h_limit<0){
        $h_limit=$cut_num+$h_limit;
        $h_cut_num=$h_limit;
        $h_limit=0;
    }

    if ( $desc ) {
        $order = 'desc';
    } else {
        $order = 'asc';
    }

    $return = array(
        'limit' => $h_limit,
        'h_cut_num'=>$h_cut_num,
        'order' => $order ,
    );

    return $return;
}


?>