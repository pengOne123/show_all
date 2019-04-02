<?php
/**
 *  model.class.php 数据模型基类
 */
defined('IN_PHPCMS') or exit('Access Denied');
pc_base::load_sys_class('db_factory', '', 0);
class TableSplitterNumModel
{

    //数据库配置
    protected $db_config = '';
    //数据库连接
    protected $db = '';
    //调用数据库的配置项
    protected $db_setting = 'default';
    //数据表名
    protected $table_name = '';
    //表前缀
    public $db_tablepre = '';
    //全部分表名
    public $array_table_names = array();
    // 默认按照300万进行分表
    public $splitter_num = '3000000';

    public function __construct ()
    {
        //echo '<pre>';
        if ( !isset( $this->db_config[ $this->db_setting ] ) ) {
            $this->db_setting = 'default';
        }

        $this->table_name = $this->db_config[ $this->db_setting ][ 'tablepre' ] . $this->table_name;
        $this->db_tablepre = $this->db_config[ $this->db_setting ][ 'tablepre' ];
        $this->db = db_factory::get_instance ( $this->db_config )->get_database ( $this->db_setting );
    }

    /**
     * 获取所有表名
     */
    final public function array_table_name ( $sort = 'asc' )
    {
        if ( isset( $this->array_table_names[ $this->table_name ] ) ) {
            return $this->array_table_names[ $this->table_name ];
        }
        $table_sub_explain = $this->select_table_sub_explain ( 'id' );
        $datas = $this->db->select ( 'tablename' , 'rgap_table_sub_record' , 'record_type=' . $table_sub_explain[ 'id' ] , '' , 'id ' . $sort );//获取所有分表的表名
        if ( empty( $datas ) ) {
            die( 'NULL' );
        }
        foreach ( $datas as $value ) {
            $this->array_table_names[ $this->table_name ][] = $this->db_config[ $this->db_setting ][ 'tablepre' ] . $value[ 'tablename' ];
        }

        return $this->array_table_names[ $this->table_name ];
    }

    /**
     * 获取分表配置
     */
    private function select_table_sub_explain ( $data = '*' )
    {
        return $this->db->get_one ( $data , 'rgap_table_sub_explain' , 'tablename=\'' . str_replace ( $this->db_config[ $this->db_setting ][ 'tablepre' ] , '' , $this->table_name ) . '\'' );
    }

    /**
     * 执行sql查询
     * @param $where        查询条件[例`name`='$name']
     * @param $data        需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param $limit        返回结果范围[例：10或10,10 默认为空]
     * @param $order        排序方式    [默认按数据库默认方式排序]
     * @param $group        分组方式    [默认为空]
     * @param $key          返回数组按键名排序
     * @return array        查询结果集数组
     */
    final public function select ( $where = '' , $data = '*' , $limit = '' , $order = '' , $group = '' , $key = '' )
    {

        if ( is_array ( $where ) ) $where = $this->sqls ( $where );
        $return_datas = array();

        if ( strpos ( $order , 'desc' ) !== false ) {
            $this->array_table_name ( 'desc' );
        }

        $offset = 0;
        if ( strpos ( $limit , ',' ) !== false ) {
            $limit = explode ( ',' , $limit );
            $offset = trim ( $limit[ 0 ] );
            $limit = trim ( $limit[ 1 ] );
        }


        foreach ( $this->array_table_name () as $table_name ) {
            //  echo "$offset, $limit".' '.$table_name.'<br>';

            if ( empty( $limit ) ) {
                $datas_one = $this->db->select ( $data , $table_name , $where , "" , $order , $group , $key );
                if ( !empty( $datas_one ) ) {
                    if ( !$key ) {
                        $return_datas = array_merge ( $return_datas , $datas_one );
                    } else {
                        $return_datas = $return_datas + $datas_one;
                    }
                }
                continue;
            }

            $count = $this->db->select ( 'count(*) as num' , $table_name , $where , "" , $order , $group );

            $offset_limit = $this->offset_limit ( $offset , $limit , $count[ 0 ][ 'num' ] );
            //print_r($offset_limit);
            $offset = $offset_limit[ 0 ][ 0 ];
            $limit = $offset_limit[ 0 ][ 1 ];
            if ( $limit !== 0 ) {
                $datas_one = $this->db->select ( $data , $table_name , $where , "$offset, $limit" , $order , $group , $key );
            }


            $offset = $offset_limit[ 1 ][ 0 ];
            $limit = $offset_limit[ 1 ][ 1 ];

            if ( !empty( $datas_one ) ) {
                if ( !$key ) {
                    $return_datas = array_merge ( $return_datas , $datas_one );
                } else {
                    $return_datas = $return_datas + $datas_one;
                }
            }

            if ( $offset === 0 && $limit === 0 ) {
                break;
            }

        }

        return $return_datas;
    }


    /**
     * 返回这次查询要用的分页 和 下次用的分页
     * $offset 第几个值开始
     * $limit 要取多少个
     * $pre_num 先查出当前表的个数
     * return array(array(第几个,要取的个数),array(下次要从几开始取取的,要取的个数))
     * 如果返回array(array( 第几个,要取的个数 ) ,array( 0 , 0 )); 表示这次取完就结束
     */
    final public function offset_limit ( $offset , $limit , $pre_num )
    {

        //echo  '<font color="red">'.$offset .' '.$limit.' '.$pre_num.'</font><br>';

        if ( $limit == 0 ) {
            return
                array(
                    array( 0 , 0 ) ,
                    array( 0 , 0 )
                );
        }

        if ( $offset === 0 ) {

            if ( $pre_num > $limit ) {
                return
                    array(
                        array( 0 , $limit ) ,
                        array( 0 , 0 )
                    );
            }

        }

        if ( $offset == $pre_num ) {
            return
                array(
                    array( 0 , 0 ) ,
                    array( 0 , $limit )
                );
        }


        if ( $pre_num < $offset ) {
            return
                array(
                    array( 0 , 0 ) ,
                    array( $offset - $pre_num , $limit )
                );
        }

        $sum = $offset + $limit;
        if ( $pre_num > $sum ) {
            return
                array(
                    array( $offset , $limit ) ,
                    array( 0 , 0 )
                );
        }


        if ( $pre_num > $offset ) {
            return
                array(
                    array( $offset , $pre_num - $offset ) ,
                    array( 0 , $limit - ( $pre_num - $offset ) )
                );
        }


    }

    /**
     * 分页大于等于2查询
     */
    private function get_page_datas ( $datas , $page , $page_num = 20 , $where , $data , $order = '' , $keys = '' )
    {
        $return_datas = array();
        if ( $datas[ 1 ] == 0 ) {
            return $return_datas;
        }
        $pre_num = ( $page - 1 ) * $page_num;//前面多余的数据

        $str = 0;//判断是否从0,n开始
        foreach ( $datas[ 0 ] as $key => $value ) {
            if ( $str == 0 ) {
                $pre_num = $pre_num - $value[ 0 ][ 'num' ];//前边表要减去的
            }
            if ( $pre_num < 0 && $str === 0 ) {
                $str = 1;
                $offset = $pre_num + $value[ 0 ][ 'num' ];
                $pagesize = -$pre_num;
                if ( $pagesize > $page_num ) {
                    $pagesize = $page_num;
                }
                // echo "<font color=\"red\">$key $offset, $pagesize </font><br>";
                $pre_num = $page_num - $pagesize;

                $datas_one = $this->db->select ( $data , $key , $where , "$offset, $pagesize" , $order , '' , $keys );

                if ( !empty( $datas_one ) ) {
                    if ( !$keys ) {
                        $return_datas = array_merge ( $return_datas , $datas_one );
                    } else {
                        $return_datas = $return_datas + $datas_one;
                    }
                }
                if ( $pagesize >= $page_num ) {
                    break;
                }
                continue;
            }

            if ( $str == 1 ) {
                if ( $value[ 0 ][ 'num' ] > $pre_num ) {
                    $pagesize = $pre_num;
                } else {
                    $pagesize = $value[ 0 ][ 'num' ];
                }
                $offset = 0;
                $pre_num = $pre_num - $pagesize;
                //echo $key." $offset, $pagesize <br>";
                $datas_one = $this->db->select ( $data , $key , $where , "$offset, $pagesize" , $order , '' , $keys );
                if ( !empty( $datas_one ) ) {
                    if ( !$keys ) {
                        $return_datas = array_merge ( $return_datas , $datas_one );
                    } else {
                        $return_datas = $return_datas + $datas_one;
                    }
                }
            }
            if ( $pre_num <= 0 && $str == 1 ) {
                break;
            }
        }


        return $return_datas;

    }

    /**
     * 查询多条数据并分页
     * @param $where
     * @param $order 只支持id这种顺序排序
     * @param $page
     * @param $pagesize
     * @return unknown_type
     */
    final public function listinfo ( $where = '' , $order = '' , $page = 1 , $pagesize = 20 , $key = '' , $setpages = 10 , $urlrule = '' , $array = array() , $data = '*' )
    {

        if ( strpos ( $order , 'desc' ) !== false ) {
            $this->array_table_name ( 'desc' );
        }

        $page = intval ( $page );
        $where = to_sqls ( $where );
        $count_array = $this->count ( $where , true );
        $this->number = $count_array[ 1 ];
        $page = max ( intval ( $page ) , 1 );
        $offset = $pagesize * ( $page - 1 );
        $this->pages = pages ( $this->number , $page , $pagesize , $urlrule , $array , $setpages );

        if ( $this->number > 0 ) {
            $datas = array();

            if ( $page > 1 ) {

                return $this->get_page_datas ( $count_array , $page , $pagesize , $where , $data , $order , $key );

            } else {

                //取前几条
                foreach ( $this->array_table_name () as $table_name ) {
                    $datas_one = $this->db->select ( $data , $table_name , $where , "$offset, $pagesize" , $order , '' , $key );
                    if ( !empty( $datas_one ) ) {
                        $datas = $datas + $datas_one;
                    }
                    $num = count ( $datas );
                    if ( $num >= $pagesize ) {
                        break;
                    }
                    $pagesize -= $num;

                }
            }

            return $datas;

//            return $this->select($where, $data, "$offset, $pagesize", $order, '', $key);
        } else {
            return array();
        }
    }

    /**
     * 获取单条记录查询
     * @param $where        查询条件
     * @param $data        需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param $order        排序方式    [默认按数据库默认方式排序]
     * @param $group        分组方式    [默认为空]
     * @return array/null    数据查询结果集,如果不存在，则返回空
     */
    final public function get_one ( $where = '' , $data = '*' , $order = '' , $group = '' )
    {
        if ( is_array ( $where ) ) $where = $this->sqls ( $where );

        foreach ( $this->array_table_name () as $table_name ) {
            $datas = $this->db->get_one ( $data , $table_name , $where , $order , $group );
            if ( $datas ) {
                return $datas;
            }
        }

        return array();

    }

    /**
     * 直接执行sql查询
     * @param $sql                            查询sql语句
     * @return    boolean/query resource        如果为查询语句，返回资源句柄，否则返回true/false
     */
    final public function query ( $sql )
    {
        $sql = str_replace ( 'phpcms_' , $this->db_tablepre , $sql );
        return $this->db->query ( $sql );
    }

    /*
    事务处理
    */
    final public function begins ( $sql )
    {
        return $this->db->begins ( $sql );
    }

    /**
     * 执行添加记录操作
     * @param $data        要增加的数据，参数为数组。数组key为字段值，数组值为数据取值
     * @param $return_insert_id 是否返回新建ID号
     * @param $replace 是否采用 replace into的方式添加数据
     * @return boolean
     */
    final public function insert ( $data , $return_insert_id = false , $replace = false )
    {
        $data[ 'id' ] = $this->db->insert ( array( 'id' => '' ) , $this->table_name . '_' . 'key' , true , $replace );
        return $this->db->insert ( $data , $this->get_table_name ( $data[ 'id' ] ) , $return_insert_id , $replace );
    }

    /**
     * 获取最后一次添加记录的主键号
     * @return int
     */
    final public function insert_id ()
    {
        return $this->db->insert_id ();
    }

    /**
     * 执行更新记录操作
     * @param $data        要更新的数据内容，参数可以为数组也可以为字符串，建议数组。
     *                        为数组时数组key为字段值，数组值为数据取值
     *                        为字符串时[例：`name`='phpcms',`hits`=`hits`+1]。
     *                        为数组时[例: array('name'=>'phpcms','password'=>'123456')]
     *                        数组的另一种使用array('name'=>'+=1', 'base'=>'-=1');程序会自动解析为`name` = `name` + 1, `base` = `base` - 1
     * @param $where        更新数据时的条件,可为数组或字符串
     * @return boolean
     */
    final public function update ( $data , $where = '' )
    {
        if ( is_array ( $where ) ) $where = $this->sqls ( $where );

        $status = false;
        foreach ( $this->array_table_name () as $table_name ) {
            $status = $this->db->update ( $data , $table_name , $where );
        }

        return $status;
    }


    /**
     * 执行删除记录操作
     * @param $where        删除数据条件,不充许为空。
     * @return boolean
     */
    final public function delete ( $where )
    {
        if ( is_array ( $where ) ) $where = $this->sqls ( $where );

        $status = false;
        foreach ( $this->array_table_name () as $table_name ) {
            $status = $this->db->delete ( $table_name , $where );
        }

        return $status;
    }

    /**
     * 计算记录数
     * @param string /array $where 查询条件
     */
    final public function count ( $where = '' , $get_array = false )
    {
        if ( is_array ( $where ) ) $where = $this->sqls ( $where );
        $r = array();
        foreach ( $this->array_table_name () as $table_name ) {
            $r[ $table_name ] = $this->db->select ( "COUNT(*) AS num" , $table_name , $where );
        }
        $num = array_sum ( array_map ( function ( $val ) {
            return $val[ 0 ][ 'num' ];
        } , $r ) );

        if ( $get_array ) {
            return array( $r , $num );
        }

        return $num;
    }

    /**
     * 将数组转换为SQL语句
     * @param array $where 要生成的数组
     * @param string $font 连接串。
     */
    final public function sqls ( $where , $font = ' AND ' )
    {
        if ( is_array ( $where ) ) {
            $sql = '';
            foreach ( $where as $key => $val ) {
                $sql .= $sql ? " $font `$key` = '$val' " : " `$key` = '$val'";
            }
            return $sql;
        } else {
            return $where;
        }
    }


    /**
     * 获取最后数据库操作影响到的条数
     * @return int
     */
    final public function affected_rows ()
    {
        return $this->db->affected_rows ();
    }

    /**
     * 获取数据表主键
     * @return array
     */
    final public function get_primary ()
    {
        return $this->db->get_primary ( $this->table_name );
    }

    /**
     * 获取表字段
     * @param string $table_name 表名
     * @return array
     */
    final public function get_fields ( $table_name = '' )
    {
        if ( empty( $table_name ) ) {
            $table_name = $this->table_name;
        } else {
            $table_name = $this->db_tablepre . $table_name;
        }
        return $this->db->get_fields ( $table_name );
    }

    /**
     * 检查表是否存在
     * @param $table 表名
     * @return boolean
     */
    final public function table_exists ( $table )
    {
        return $this->db->table_exists ( $this->db_tablepre . $table );
    }

    /**
     * 检查字段是否存在
     * @param $field 字段名
     * @return boolean
     */
    public function field_exists ( $field )
    {
        $fields = $this->db->get_fields ( $this->table_name );
        return array_key_exists ( $field , $fields );
    }

    final public function list_tables ()
    {
        return $this->db->list_tables ();
    }

    /**
     * 返回数据结果集
     * @param $query （mysql_query返回值）
     * @return array
     */
    final public function fetch_array ()
    {
        $data = array();
        while ( $r = $this->db->fetch_next () ) {
            $data[] = $r;
        }
        return $data;
    }

    /**
     * 返回数据库版本号
     */
    final public function version ()
    {
        return $this->db->version ();
    }

    /**
     * 根据id获取表名
     **/
    final public function get_table_name ( $id )
    {
        $num = intval ( ( $id - 1 ) / $this->splitter_num );
        if ( $num > 0 ) {
            return $this->table_name . '_' . $num;
        }
        return $this->table_name;
    }


    public function listInfoJoin ( $join , $fields , $where = '' , $order = '' , $page = 1 , $pagesize = 20 , $key = '' , $setpages = 10 , $urlrule = '' , $array = array() )
    {


        if ( strpos ( $order , 'desc' ) !== false ) {
            $this->array_table_name ( 'desc' );
        }

        if ( !empty( $where ) ) {
            $where = ' where ' . $where;
        }

        if ( !empty( $order ) ) {
            $order = ' order by ' . $order;
        }

        $count = count ( $join );
        $function_count = 'get_join_' . $count . '_count';
        $function_data = 'get_join_' . $count . '_datas';

        $page = intval ( $page );
        $where = to_sqls ( $where );
        $this->number = $this->$function_count ( $join , $where );

        $page = max ( intval ( $page ) , 1 );
        $offset = $pagesize * ( $page - 1 );
        $this->pages = pages ( $this->number , $page , $pagesize , $urlrule , $array , $setpages );

        return $this->$function_data ( $join , $where , $fields , "$offset, $pagesize" , $order );


    }


    //1个表 链表查询
    final public function get_join_1_count ( $join , $where )
    {
        $join = $join[ 0 ][ 'join' ];
        $count = 0;
        $this->listNum = array();
        foreach ( $this->array_table_name () as $tableOne ) {
            $table = $tableOne . $join;
            $sql = 'select COUNT(*) as num from ' . $table . $where;
            $this->query ( $sql );
            $countData = $this->fetch_array ();
            $this->listNum[ $tableOne ] = $countData[ 0 ][ 'num' ];
            $count += $countData[ 0 ][ 'num' ];
        }
        return $count;
    }

    //1个表 获得数据
    final public function get_join_1_datas ( $join , $where = '' , $fields , $limit = 20 , $order = '' )
    {
        $offset = 0;
        $return_datas = array();
        if ( strpos ( $limit , ',' ) !== false ) {
            $limit = explode ( ',' , $limit );
            $offset = trim ( $limit[ 0 ] );
            $limit = trim ( $limit[ 1 ] );
        }

        $join = $join[ 0 ][ 'join' ];

        foreach ( $this->array_table_name () as $tableOne ) {
            if ( $offset === 0 && $limit === 0 ) {
                break;
            }

            $table = ' from '.$tableOne . $join;
            if ( empty( $limit ) ) {
                $sql = "select $fields $table $where $order";
                $this->query ( $sql );
                $datas_one = $this->fetch_array ();
                if ( !empty( $datas_one ) ) {
                    $return_datas = $return_datas + $datas_one;
                }
                continue;
            }

            if ( isset( $this->listNum[ $tableOne ] ) ) {
                $countData[ 0 ][ 'num' ] = $this->listNum[ $tableOne ];
            } else {
                $countData[ 0 ][ 'num' ] = 0;
            }

            $offset_limit = $this->offset_limit ( $offset , $limit , $countData[ 0 ][ 'num' ] );

            $offset = $offset_limit[ 0 ][ 0 ];
            $limit = $offset_limit[ 0 ][ 1 ];

            if ( $limit !== 0 ) {
                $sql = "select $fields $table $where $order limit " . $offset . ',' . $limit;
                $this->query ( $sql );
                $datas_one = $this->fetch_array ();
            }

            $offset = $offset_limit[ 1 ][ 0 ];
            $limit = $offset_limit[ 1 ][ 1 ];
            if ( !empty( $datas_one ) ) {
                $return_datas = array_merge ( $return_datas , $datas_one );
            }

            if ( $offset === 0 && $limit === 0 ) {
                break;
            }

        }

        return $return_datas;

    }


    //2个表 链表查询
    final public function get_join_2_count ( $join , $where = '' , $JoinTable = '' )
    {
        $count = 0;
        $this->listNum = array();

        foreach ( $this->TableNameArray[ 0 ] as $tableOne ) {
            foreach ( $this->TableNameArray[ 1 ] as $tableTwo ) {
                $table = $tableOne . $join[ 0 ] . $tableTwo . $join[ 1 ] . $JoinTable;
                $sql = 'select COUNT(*) as num from ' . $table . $where;
                $this->query ( $sql );
                $countData = $this->fetch_array ();
                $this->listNum[ $tableOne . '_' . $tableTwo ] = $countData[ 0 ][ 'num' ];
                $count += $countData[ 0 ][ 'num' ];
            }
        }
        return $count;
    }

    //获得数据
    final public function get_join_2_datas ( $join , $where = '' , $fields , $limit = 20 , $order = '' , $JoinTable = '' )
    {
        $offset = 0;
        $return_datas = array();
        if ( strpos ( $limit , ',' ) !== false ) {
            $limit = explode ( ',' , $limit );
            $offset = trim ( $limit[ 0 ] );
            $limit = trim ( $limit[ 1 ] );
        }

        foreach ( $this->TableNameArray[ 0 ] as $tableOne ) {
            if ( $offset === 0 && $limit === 0 ) {
                break;
            }
            foreach ( $this->TableNameArray[ 1 ] as $tableTwo ) {
                $table = $tableOne . $join[ 0 ] . $tableTwo . $join[ 1 ] . $JoinTable;
                if ( empty( $limit ) ) {
                    $sql = "select $fields $table $where $order";
                    $this->query ( $sql );
                    $datas_one = $this->fetch_array ();
                    if ( !empty( $datas_one ) ) {
                        $return_datas = $return_datas + $datas_one;
                    }
                    continue;
                }

                if ( isset( $this->listNum[ $tableOne . '_' . $tableTwo ] ) ) {
                    $countData[ 0 ][ 'num' ] = $this->listNum[ $tableOne . '_' . $tableTwo ];
                } else {
                    $countData[ 0 ][ 'num' ] = 0;
                }

                $offset_limit = $this->offset_limit ( $offset , $limit , $countData[ 0 ][ 'num' ] );

                $offset = $offset_limit[ 0 ][ 0 ];
                $limit = $offset_limit[ 0 ][ 1 ];

                if ( $limit !== 0 ) {
                    $sql = "select $fields $table $where $order limit " . $offset . ',' . $limit;
                    $this->query ( $sql );
                    $datas_one = $this->fetch_array ();
                }

                $offset = $offset_limit[ 1 ][ 0 ];
                $limit = $offset_limit[ 1 ][ 1 ];
                if ( !empty( $datas_one ) ) {
                    $return_datas = array_merge ( $return_datas , $datas_one );
                }

                if ( $offset === 0 && $limit === 0 ) {
                    break;
                }
            }
        }

        return $return_datas;

    }


}