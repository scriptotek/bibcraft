<?php

class Loan extends Eloquent {
	protected $guarded = array();
	protected $softDelete = true;

	public static $rules = array();

	public function document()
    {
        return $this->belongsTo('Document');
    }

	public function user()
    {
        return $this->belongsTo('User');
    }

}
