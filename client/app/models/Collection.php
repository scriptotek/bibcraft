<?php

class Collection extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

    public function documents()
    {
        return $this->belongsToMany('Document')
            ->withPivot('created_at')
            ->orderBy('pivot_created_at', 'desc');
    }

    public function document_ids()
    {
        $a = array();
        foreach ($this->documents as $c) {
            $a[] = $c->id;
        }
        return $a;
    }

}
