<?php
function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . DIRECTORY_SEPARATOR . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoader');
include_once 'OSS/aliyun-php-sdk-core/Config.php';
use Sts\Request\V20150401 as Sts;
use OSS\OssClient;
use OSS\Core\OssException;
/**
 * oss 封装 本类是用sts来获取securityToken
 * 判断是否成功 最好用 if ( $status === true ) { }
 **/
class oss
{
    private $AccessKeyID = ""; // sts AccessKeyID
    private $AccessKeySecret = ""; // sts AccessKeySecret
    private $RoleArn = ""; //sts RoleArn
    private $TokenExpireTime = "";//密钥有效时间
    private $TokenCache = ''; //本地令牌在有效期内缓存的目录
    private $endpoint = "";  //上传域名
    private $isCName = '';  //是否自定义域名
    private $bucket = '';  //bucket名
    private $ConnectTimeout = '';  //设置连接超时时间，单位秒，默认是10秒
    private $Timeout = '';  //设置请求超时时间，单位秒，默认是5184000秒, 这里建议 不要设置太小，如果上传/下载文件很大，消耗的时间会比较长
    private $CopyCallbackUrl = '';  //copy回调地址
    private $CopyCdn = '';  //copy cdn

    public function __construct ( $configArrayKey = 'default' )
    {
        $config = pc_base::load_config ( 'oss' , $configArrayKey );
        $this->AccessKeyID = $config[ 'AccessKeyID' ];
        $this->AccessKeySecret = $config[ 'AccessKeySecret' ];
        $this->RoleArn = $config[ 'RoleArn' ];
        $this->TokenExpireTime = $config[ 'TokenExpireTime' ];
        $this->TokenCache = $config[ 'TokenCache' ];
        if ( $config[ 'oss_inside' ] ) {
            $this->endpoint = $config[ 'endpointInside' ];
        } else {
            $this->endpoint = $config[ 'endpoint' ];
        }
        $this->isCName = $config[ 'isCName' ];
        $this->bucket = $config[ 'bucket' ];
        $this->ConnectTimeout = $config[ 'ConnectTimeout' ];
        $this->Timeout = $config[ 'Timeout' ];

        $this->CopyCallbackUrl = isset( $config[ 'CopyCallbackUrl' ] ) ? $config[ 'CopyCallbackUrl' ] : '';
        $this->CopyCdn = isset( $config[ 'Cdn' ] ) ? $config[ 'Cdn' ] : '';

        $securityToken = $this->getStsToken ();

        $this->ossClient = new OssClient( $securityToken[ 'AccessKeyId' ] , $securityToken[ 'AccessKeySecret' ] , $this->endpoint , $this->isCName , $securityToken[ 'SecurityToken' ] );

        $this->ossClient->setConnectTimeout ( $this->ConnectTimeout );//设置连接超时时间，单位秒，默认是10秒
    }

    /**
     * 创建Bucket
     *
     * @param string $bucket bucket名称
     * @param array $options
     * @return null
     */
    public function createBucket ( $bucket )
    {
        try {
            $this->ossClient->createBucket ( $bucket );/*创建BUCKET*/
            return true;
        } catch ( OssException $e ) {
            return $e->getMessage ();
        }
    }

    /**
     * 写入文件 自动创建目录
     *
     * @param string $object 文件名称
     * @param string $content 文件内容
     * @param array $options 设置文件的元数据
     * @return null
     */
    public function putObject ( $object , $content , $options = NULL )
    {
        try {

            if ( !empty( $this->CopyCallbackUrl ) ) {
                $json =
                    '{
        "callbackUrl":"' . $this->CopyCallbackUrl . '",
        "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
        "callbackBody":"{\"bucket\":${bucket},\"object\":${object},\"etag\":${etag},\"size\":${size},\"mimeType\":${mimeType},\"imageInfo.format\":${imageInfo.format},\"cdn\":${x:cdn},\"sign\":${x:sign}}",
        "callbackBodyType":"application/json"
    }';
                $var =
                    '{
        "x:cdn":"' . $this->CopyCdn . '",
        "x:sign":"' . md5 ( $this->CopyCdn . $object . 'adkjakldjkad' ) . '"
    }';
                $options = array( OssClient::OSS_CALLBACK => $json ,
                    OssClient::OSS_CALLBACK_VAR => $var
                );
            }
            $this->ossClient->setTimeout ( $this->Timeout );//设置请求超时时间，单位秒，默认是5184000秒, 这里建议 不要设置太小，如果上传文件很大，消耗的时间会比较长
            $this->ossClient->putObject ( $this->bucket , $object , $content , $options );
            return true;
        } catch ( OssException $e ) {
            return $e->getMessage ();
        }
    }


    /**
     * 上传本地文件 自动创建目录
     *
     * @param string $file 本地文件
     * @param string $object object名称
     * @param array $options 设置文件的元数据
     * @return null
     */
    function uploadFile ( $file , $object , $options = NULL )
    {
        try {

            if ( !empty( $this->CopyCallbackUrl ) ) {
                $json =
                    '{
        "callbackUrl":"' . $this->CopyCallbackUrl . '",
        "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
        "callbackBody":"{\"bucket\":${bucket},\"object\":${object},\"etag\":${etag},\"size\":${size},\"mimeType\":${mimeType},\"imageInfo.format\":${imageInfo.format},\"cdn\":${x:cdn},\"sign\":${x:sign}}",
        "callbackBodyType":"application/json"
    }';
                $var =
                    '{
        "x:cdn":"' . $this->CopyCdn . '",
        "x:sign":"' . md5 ( $this->CopyCdn . $object . 'adkjakldjkad' ) . '"
    }';
                $options = array( OssClient::OSS_CALLBACK => $json ,
                    OssClient::OSS_CALLBACK_VAR => $var
                );
            }

            $this->ossClient->uploadFile ( $this->bucket , $object , $file , $options );
            return true;
        } catch ( OssException $e ) {
            return $e->getMessage ();
        }
    }


    /**
     * 拷贝object 小于1G
     *
     * @param string $from_object 被复制object名称
     * @param string $to_object 复制object名称
     * @return null
     */
    function copyObject ( $from_object , $to_object )
    {
        try {
            $this->ossClient->copyObject ( $this->bucket , $from_object , $this->bucket , $to_object );
            return true;
        } catch ( OssException $e ) {
            return $e->getMessage ();
        }
    }


    /**
     * 获取文件内容
     *
     * @param string $object object名称
     * @return null
     */
    public function getObject ( $object )
    {
        try {
            $this->ossClient->setTimeout ( $this->Timeout );//设置请求超时时间，单位秒，默认是5184000秒, 这里建议 不要设置太小，如果文件很大，消耗的时间会比较长
            $content = $this->ossClient->getObject ( $this->bucket , $object );
            return $content;
        } catch ( OssException $e ) {
            return $e->getMessage ();
        }

    }


    /**
     * 获取文件夹下多少文件和文件夹
     *
     * @param string $prefix $prefix文件夹名称
     * $options array $options 设置条件
     * @return null
     */
    public function listAllObjects ( $prefix , $options = '' )
    {

        if ( substr ( $prefix , -1 ) !== '/' ) {
            $prefix = $prefix . '/';
        }

        $delimiter = '/';
        $nextMarker = '';
        $maxkeys = 30;
        if ( empty( $options ) ) {
            $options = array(
                'delimiter' => $delimiter ,//为行使文件夹功能的分割符号，如 / ；
                'prefix' => $prefix ,//是我们想获取的文件的目录，如 test/ 即为列出目录 test 下的所有文件及子文件夹（不递归获取）
                'max-keys' => $maxkeys ,//max-keys用于限定此次返回object的最大数，如果不设定，默认为100，max-keys取值不能大于1000。
                'marker' => $nextMarker ,//是实现分页时指向下一分页起始位置的标识。
            );
        }

        while ( true ) {

            try {
                $listObjectInfo = $this->ossClient->listObjects ( $this->bucket , $options );
            } catch ( OssException $e ) {
                printf ( __FUNCTION__ . ": FAILED\n" );
                printf ( $e->getMessage () . "\n" );
                return;
            }
            // 得到nextMarker，从上一次listObjects读到的最后一个文件的下一个文件开始继续获取文件列表
            $nextMarker = $listObjectInfo->getNextMarker ();//是实现分页时指向下一分页起始位置的标识
            $listObject = $listObjectInfo->getObjectList ();//第一个值为本目录的值 其他为此目录文件的值
            $listPrefix = $listObjectInfo->getPrefixList ();//此目录所有文件夹
            print_r ( $nextMarker );
            print_r ( $listObject );
            print_r ( $listPrefix );

//            var_dump ( count ( $nextMarker ) );
//            var_dump ( count ( $listObject ) );
//            var_dump ( count ( $listPrefix ) );
            if ( $nextMarker === '' ) {
                break;
            }
        }

    }

    /**
     * 删除文件
     *
     * @param string $object $object名称
     * @return null
     */
    public function deleteObject ( $object )
    {
        try {
            $this->ossClient->deleteObject ( $this->bucket , $object );
            return true;
        } catch ( OssException $e ) {
            return $e->getMessage ();
        }

    }

    /**
     * 批量删除object
     * $del_list数组
     * @param OssClient $ossClient OSSClient实例
     * @param string $bucket bucket名字
     * @return null
     */
    function deleteObjects ( $del_list )
    {
        try {
            $this->ossClient->deleteObjects ( $this->bucket , $del_list );
        } catch ( OssException $e ) {
            return $e->getMessage ();
        }
    }


    /**
     * 创建目录 其实这只是在创建Object时多加了/ 假如目录下有文件则不替换  结尾/可以不加 可以是层级目录 如果存在 返回也是true
     *
     * @param string $dir 目录名称
     * @return null
     */

    public function createObjectDir ( $dir )
    {
        if ( substr ( $dir , -1 ) === '/' ) {
            $dir = substr ( $dir , 0 , -1 );
        }
        try {
            $this->ossClient->createObjectDir ( $this->bucket , $dir );
            return true;
        } catch ( OssException $e ) {
            return $e->getMessage ();
        }
    }

    /**
     * 删除目录
     *
     * @param string $dir 目录名称
     * @return null
     */
    public function deleteDir ( $dir )
    {
        if ( substr ( $dir , -1 ) !== '/' ) {
            $dir = $dir . '/';
        }
        return $this->deleteObject ( $dir );
    }

    /**
     * 以sts方式获得 AccessKeyId AccessKeySecret SecurityToken
     *
     * @return $Credentials
     */

    public function getStsToken ()
    {
        $cacheDir = PHPCMS_PATH . $this->TokenCache;

        if ( !is_dir ( $cacheDir ) ) {
            mkdir ( $cacheDir , 0777 , true );
        }
        $cache = $cacheDir . 'token.php';
        if ( is_file ( $cache ) ) {
            $Credentials = json_decode ( trim ( substr ( file_get_contents ( $cache ) , 15 ) ) , true );
            //为了防止传输中密令失效 在原有有效期内减去5秒
            if ( isset( $Credentials[ 'Expiration' ] ) && ( strtotime ( $Credentials[ 'Expiration' ] ) - 5 ) > time () ) {
                return $Credentials;
            }
        }

        $iClientProfile = DefaultProfile::getProfile ( "cn-hangzhou" , $this->AccessKeyID , $this->AccessKeySecret );
        $client = new DefaultAcsClient( $iClientProfile );
        $request = new Sts\AssumeRoleRequest();
        $request->setRoleSessionName ( "admin" ); //用于区分用户
        $request->setRoleArn ( $this->RoleArn );
        $request->setDurationSeconds ( $this->TokenExpireTime );
        $response = $client->getAcsResponse ( $request );
        file_put_contents ( $cache , "<?php exit();?>" . json_encode ( $response->Credentials ) );
        return json_decode ( json_encode ( $response->Credentials ) , true );
    }


    /**
     * 上传远程文件到oss
     *
     * @param string $url 远程文件路径
     * @param string $object $object名称
     * @return bool
     */

    public function uploadUrl ( $url , $object = '' )
    {
        if ( !$object ) {
            $object = parse_url ( $url , PHP_URL_PATH );
        }
        $file_name = SYS_TIME . rand ( 10 , 1000 );
        $download_url_local = download_url_local ( $url , 'uploadfile/tmp' , $file_name );
        if ( $download_url_local[ 'error' ] != 0 ) {
            return false;
        }
        $mess = $this->uploadFile ( $download_url_local[ 'save_path' ] , $object );
        @unlink ( $download_url_local[ 'save_path' ] );
        if ( $mess === true ) {
            return true;
        }
        return false;
    }

    /**
     * 去除域名 必须是有域名的情况下 如果域名不同则传到本oss下
     *
     * @param string $url 远程文件路径
     * @param string $domain 要去除的域名
     * @return string or false
     */
    public function removeDomain ( $url , $domain )
    {

        $url_parse = parse_url ( $url );
        $domain_parse = parse_url ( $domain );

        if ( !isset( $domain_parse[ 'host' ] ) || !isset( $url_parse[ 'host' ] ) || !isset( $url_parse[ 'path' ] ) ) {
            return false;
        }

        $dir = substr ( $url_parse[ 'path' ] , 1 );

        if ( $domain_parse[ 'host' ] == $url_parse[ 'host' ] ) {
            return $dir;
        }

        if ( $this->uploadUrl ( $url , $dir ) ) {
            return $dir;
        }

        return false;
    }

    /**
     * 定时发布帖子图片回调
     *
     * @param string $object 原文件
     * @param string $dir 原文件传到哪个文件夹
     * @param string $page 第几张图
     * @param int $work_id 帖子id
     * @return bool
     */

    public function works_pic_timing ( $object , $dir , $page , $work_id )
    {

        $domain = str_replace ( "https://" , "http://" , APP_PATH_DJC );

        $json =
            '{
        "callbackUrl":"' . $domain . 'dacu_app/app/?c=Plaza_2_9/PlazaWorks&a=callback",
        "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
        "callbackBody":"{\"bucket\":${bucket},\"object\":${object},\"etag\":${etag},\"mimeType\":${mimeType},\"size\":${size},\"height\":${imageInfo.height},\"width\":${imageInfo.width},\"format\":${imageInfo.format},\"work_id\":${x:work_id},\"page\":${x:page},\"status\":${x:status},\"frame\":${x:frame},\"not_sm\":${x:not_sm}}",
        "callbackBodyType":"application/json"
  }';
        $var =
            '{
        "x:work_id":"' . $work_id . '",
        "x:page":"' . $page . '",
        "x:status":"1",
        "x:frame":"1",
        "x:not_sm":"1"
    }';

        $url = "http://" . $this->bucket . ".oss-cn-hangzhou.aliyuncs.com/" . $object;

        $file_name = SYS_TIME . rand ( 10 , 1000 );
        $download_url_local = download_url_local ( $url , 'uploadfile/tmp' , $file_name );
        if ( $download_url_local[ 'error' ] != 0 ) {
            return false;
        }
        $options = array( \OSS\OssClient::OSS_CALLBACK => $json ,
            \OSS\OssClient::OSS_CALLBACK_VAR => $var );
        try {
            $name = pathinfo ( $object , PATHINFO_BASENAME );
            $result = $this->ossClient->uploadFile ( $this->bucket , $dir . $name , $download_url_local[ 'save_path' ] , $options );
            @unlink ( $download_url_local[ 'save_path' ] );
            if ( isset( $result[ 'body' ] ) ) {
                $http_code = json_decode ( $result[ 'body' ] , true );
                if ( $http_code[ 'Status' ] == 200 ) {
                    return true;
                }
            }
            return false;
        } catch ( OssException $e ) {
            @unlink ( $download_url_local[ 'save_path' ] );
            return false;
        }
    }


}









