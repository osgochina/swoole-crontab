<?php
/**
 * Created by PhpStorm.
 * User: vic
 * Date: 15-11-2
 * Time: 下午9:38
 */

class Tasks implements Iterator{

    private $obj;

    public function __construct($config = array()){
        if(empty($config)){
            $this->obj = new LoadFile();
        }else{
            $this->obj = new $config["class"];
        }
    }


    public function current()
    {
        return $this->obj->current();
    }


    public function next()
    {
        $this->obj->next();
    }


    public function key()
    {
        return $this->obj->key();
    }


    public function valid()
    {
        return $this->obj->valid();
    }


    public function rewind()
    {
        $this->obj->rewind();
    }
}
