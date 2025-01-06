<?php

/*原型模式 （Prototype）       start*/

interface PrototypeInterface
{
    public function shallowCopy();

    public function deepCopy();
}
