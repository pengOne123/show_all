<?php
/*11.组合模式 （Composite）      start*/

class TreeNode
{
    private $name;

    private $parent;

    private $childern = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(TreeNode $parent)
    {
        $this->parent = $parent;
    }

    public function add(TreeNode $node)
    {
        $this->childern[] = $node;
    }

    public function remove(TreeNode $node)
    {
        $key = array_search($node, $this->childern);
        if ($key !== false) array_splice($this->childern, $key, 1);
//        array_diff($this->childern, [$node]);
    }

    public function getChildren()
    {
        return array_values($this->childern);
    }

}

class Tree
{
    public $root = Null;

    public function __construct($name)
    {
        $this->root = new TreeNode($name);
    }

    public static function main()
    {
        $Tree = new Tree('A');
        $NodeB = new TreeNode('B');
        $NodeC = new TreeNode('C');
        $NodeB->add($NodeC);
        $Tree->root->add($NodeB);

//        $NodeB->remove($NodeC);
//        $Tree->root->remove($NodeB);
        echo 'build the tree finished';
        echo '<pre>';
        print_r($Tree->root->getChildren());
    }

}

Tree::main();

//使用场景：将多个对象组合在一起进行操作，常用于表示树形结构，例如二叉树等。

