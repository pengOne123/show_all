<?php
/*21.访问者模式（Visitor）      start*/

namespace Visitor;

interface VisitorInterface
{
    public function visit(SubjectInterface $subject);
}

class MyVisitor implements VisitorInterface
{
    public function visit(SubjectInterface $subject)
    {
        echo 'visit the subject:' . $subject->getSubject();
    }

}

interface SubjectInterface
{
    public function accept(VisitorInterface $visitor);

    public function getSubject();
}

class MySubject implements SubjectInterface
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visit($this);
    }

    public function getSubject()
    {
        return 'love';
    }

}

class Test
{
    public static function main()
    {
        $Visitor = new MyVisitor();
        $Sub     = new MySubject();
        $Sub->accept($Visitor);
    }

}

Test::main();

//访问者模式就是 MyVisitor（访问者）类 进入MySubject（被访问者）类，MySubject将自身传递给MyVisitor，MyVisitor进行一系列的操作。
