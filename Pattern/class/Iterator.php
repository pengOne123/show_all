<?php
/*16.迭代子模式 （Iterator）      start*/

interface CollectionInterface
{
    public function iterator();

    /*获取集合元素*/
    public function get($i);

    /*取得集合大小*/
    public function size();

}

interface IteratorInterface
{
    //前移
    public function previous();

    //后移
    public function next();

    public function hasNext();

    //取得用第一个元素
    public function first();

}

class MyCollection implements CollectionInterface
{
    public $string = ['A', 'B', 'C', 'D', 'E'];

    public function iterator()
    {
        return new MyIterator($this);
    }

    public function get($i)
    {
        return $this->string[$i];
    }

    public function size()
    {
        return count($this->string);
    }

}


class MyIterator implements IteratorInterface
{
    private $Collection;
    private $pos = -1;

    public function __construct(CollectionInterface $Collection)
    {
        $this->Collection = $Collection;
    }


    public function previous()
    {
        if ($this->pos > 0) {
            $this->pos--;
        }
        return $this->Collection->get($this->pos);
    }

    public function next()
    {
        if ($this->pos < $this->Collection->size() - 1) {
            $this->pos++;
        }
        return $this->Collection->get($this->pos);
    }

    public function hasNext()
    {
        if ($this->pos < $this->Collection->size() - 1) {
            return true;
        }
        return false;
    }

    public function first()
    {
        $this->pos = 0;
        return $this->Collection->get($this->pos);
    }

}

class Test
{
    public static function main()
    {
        $Collection = new MyCollection();
        $it         = $Collection->iterator();
        while ($it->hasNext()) {
            echo $it->next();
        }
    }

}

Test::main();
//迭代器模式就是顺序访问聚集中的对象  这里模拟了一个集合类  MyIterator就是一个迭代器

