<?php

class Item extends Eloquent {
    protected $guarded = array();

    public static $rules = array();

    public function visits() {
        return $this->hasMany('Visit');
    }

    public function cached_cover() {
        $cover_url = $this->cover;
        $ext = '.jpg';
        return sha1($cover_url) . $ext;
    }

}