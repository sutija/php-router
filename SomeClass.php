<?php


class SomeClass
{
    public function resolve() {
        echo 'RESOLVE';
    }

    public static function get() {

    }

    public static function on_get() {
        echo 'GET SOMETHING';
    }

    public static function on_post() {
        echo 'POST SOMETHING';
    }
}
