<?php
//基本知识 1.继承 2.接口 3.解耦、低耦合。
//七大原则:1、单一原则 2、里氏替换原则 3、依赖倒置原则 4、接口隔离原则 5、迪米特法则 6、开闭原则 7、组合/聚合原则。
//DRY-Don't Repeat Yourself:千万不要重复你自身。尽量在项目中减少重复的代码行，重复的方法，重复的模块。
//KISS-Keep It Simple & Stupid:保持简单易懂。
//YAGNI-You Ain't Gonna Need It:你将不会需要它。千万不要进行过度设计。

//23设计模式
//简单工厂模式 1.普通简单工厂->用一个方法if实现  2.多方法简单工厂->分为多个方法 3.静态方法简单工厂->将2里面的方法变为静态
//创建模式 6种

/*1.工厂方法模式 （Factory Method Pattern）       start*/

include 'class\Factory.php';

/*2.抽象工厂模式 （Abstract Factory Pattern）       start*/

include 'class\AbstractFactory.php';

/*3.单例模式 （Singleton）       start*/

include 'class\Singleton.php';

/*4.建造者模式 （Builder）       start*/

include 'class\Builder.php';

/*5.原型模式 （Prototype）       start*/

include 'class\Prototype.php';

//结构模式 7种 1.适配器模式(类的适配器模式、对象的适配器模式、接口的适配器模式) 2.装饰模式 3.代理模式 4.外观模式 5.桥接模式 6.组合模式 7.享元模式

/*6.适配器模式 （Adapter）       start*/

include 'class\Adapter.php';

/*7.装饰器模式 （Decorator）       start*/

include 'class\Decorator.php';

/*8.代理模式 （Proxy）       start*/

include 'class\Proxy.php';

/*9.外观模式 （Facade）      start*/

include 'class\Facade.php';

/*10.桥接模式 （Bridge）      start*/

include 'class\Bridge.php';

/*11.组合模式 （Composite）      start*/

include 'class\Composite.php';

/*11.享元模式 （Flyweight）      start*/

include 'class\Flyweight.php';

/*关系模式11种 第一类：父类与子类实现（1.策略模式 2.模板方法模式） 第二类：两个类之间（3.观察者模式 4.迭代子模式 5.责任链模式 6.命令模式） 第三类：类的状态 （7.备忘录模式 8.状态模式）第四类：通过中间类（9.访问者模式 10.中介者模式 11.解释器模式）*/

/*13.策略模式 （Strategy）      start*/

include 'class\Strategy.php';

/*14.模板方法模式 （Template Method）      start*/

include 'class\TemplateMethod.php';

/*15.观察者模式 （Observer）      start*/

include 'class\Observer.php';

/*16.迭代器模式 （Iterator）      start*/

include 'class\Iterator.php';

/*17.责任链模式 （Chain Of Responsibility）      start*/

//include 'class\ChainOfResponsibility.php';

/*18.命令模式 （Command）      start*/

include 'class\Command.php';

/*19.备忘录模式（Memento）      start*/

//include 'class\Memento.php';

/*20.状态模式（State）      start*/

include 'class\State.php';

/*21.访问者模式（Visitor）      start*/

include 'class\Visitor.php';

/*22.中介者模式（Mediator）      start*/

include 'class\Mediator.php';

/*23.解释器模式（Interpreter）      start*/

include 'class\Interpreter.php';

//参考文档：https://www.cnblogs.com/geek6/p/3951677.html























[
    'data'=>[
        'rankString'=>'',//服务器生成的随机字符串或者时间戳加随机字符串作为值
        'key'=>'',//由(rankString+data)Md5 利用非对称秘钥生成 双方解密后对比

        'data'=>[],//双方真正需要的值
//        'dataMd5'=>[],//data的md5值保持数据完整性
    ],//对称加密后的参数  用对称秘钥解析
];
